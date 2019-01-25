<?php

namespace mathewparet\LaravelInvites;

use mathewparet\LaravelInvites\Exceptions\InvalidInvitationCodeException;
use mathewparet\LaravelInvites\Exceptions\InvalidEmailIdException;
use mathewparet\LaravelInvites\Exceptions\InvitationNotYetActiveException;
use mathewparet\LaravelInvites\Exceptions\InvitationExpiredException;
use mathewparet\LaravelInvites\Exceptions\InvitationNotValidWithEmailException;
use mathewparet\LaravelInvites\Exceptions\MaximumUseOfCodeException;
use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;
use mathewparet\LaravelInvites\Exceptions\AnEmailCanHaveOnlyOneInvitation;

use mathewparet\LaravelInvites\Mail\InvitationMail;

use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Validator;

use mathewparet\LaravelInvites\Models\Invite;

class LaravelInvites
{
    private $number_of_invites = 1;
    private $data = [];

    /**
     * Initialize the data attributes and reset values
     * 
     * @return void
     */
    private function initializeData()
    {
        $this->data = ['allow_count' => 1];
    }

    public function __construct()
    {
        $this->initializeData();
    }

    /**
     * Get the invites from DB
     * 
     * @return mixed
     */
    public function get()
    {
        if (!blank(optional($this->data)['email']))
            $result = Invite::valid()->whereEmail($this->data['email'])->first();
        else
            $result = Invite::valid()->get();

        $this->initializeData();

        return $result;
    }

    /**
     * Set the email property for generating or getting invitation
     * 
     * @param string $email
     * 
     * @return \mathewparet\LaravelInvites\Facades\LaravelInvites
     */
    public function for ($email = null)
    {
        if (!$email)
        {
            unset($this->data['email']);
            return $this;
        }

        $validator = Validator::make(compact('email'), [
            'email'=>'required|email'
        ]);
        if ($validator->fails())
            throw new InvalidEmailIdException;

        $this->data['email'] = $email;

        return $this;
    }

    /**
     * Create a single invite model
     * 
     * @return \mathewparet\LaravelInvites\Models\Invite
     */
    private function prepareSingle()
    {
        $invite = Invite::create($this->data);

        if ($invite->email && config('laravelinvites.mail.enabled', true))
            Mail::to($invite->email)->send(new InvitationMail($invite));

        return $invite;
    }

    /**
     * Get the validity start date for the invitation
     * 
     * @param \Carbon\Carbon $date
     * @return \mathewparet\LaravelInvites\Facades\LaravelInvites
     */
    public function validFrom(Carbon $date)
    {
        $this->data['valid_from'] = $date;

        return $this;
    }

    /**
     * Generate the requested invitations
     * 
     * @return mixed
     */
    private function prepare()
    {
        if ($this->number_of_invites == 1)
            return $this->prepareSingle();

        $invites = [];

        for ($i = 0; $i < $this->number_of_invites; $i++)
        {
            $invites[] = $this->prepareSingle();
        }

        return $invites;
    }

    /**
     * Validate email ID before generating invitation code
     * 
     * @param integer $number_of_invites
     * 
     * @throws \mathewparet\LaravelInvites\Exceptions\AnEmailCanHaveOnlyOneInvitation
     */
    private function validateEmailBeforeGeneration($number_of_invites = 1)
    {
        if (optional($this->data)['email'] && !blank($this->data['email']))
        {
            if ($number_of_invites > 1)
                throw new AnEmailCanHaveOnlyOneInvitation;

            $validator = Validator::make($this->data, [
                'email'=>'unique:'.config('laravelinvites.table').',email'
            ]);

            if ($validator->fails())
                throw new AnEmailCanHaveOnlyOneInvitation;
        }
    }

    /**
     * Generate invitations
     * 
     * @param integer $number_of_invites | default = 1
     * 
     * @return mixed array of invitations generated
     */
    public function generate($number_of_invites = 1)
    {
        $this->validateEmailBeforeGeneration($number_of_invites);

        $this->number_of_invites = $number_of_invites;

        $invitations = $this->prepare();
        
        $this->initializeData();

        return $invitations;
    }

    /**
     * Set the number of uses allowed for this invite
     * 
     * @param integer $num_allowed
     * 
     * @return \mathewparet\LaravelInvites\Facades\LaravelInvites
     */
    public function allow($num_allowed = 1)
    {
        $this->data['allowed_count'] = $num_allowed;

        return $this;
    }

    /**
     * Generate a single invitiation to be used only by a specific email
     * 
     * @param string $email
     * 
     * @return mixed the invitation record
     */
    public function generateFor($email)
    {
        $this->for($email);

        return $this->generate(1);
    }

    /**
     * Set expiry to never expire
     * 
     * @return \mathewparet\LaravelInvites\Facades\LaravelInvites
     */
    public function withoutExpiry()
    {
        $this->data['valid_upto'] = null;

        return $this;
    }

    /**
     * Set an expiry date
     * 
     * @param \Carbon\Carbon $data
     */
    public function setExpiry(Carbon $date)
    {
        $this->data['valid_upto'] = $date;

        return $this;
    }

    /**
     * Find an invitation by code
     * 
     * @param string $code
     *
     * @return \mathewparet\LaravelInvites\Models\Invite $invite
     */
    public function find($code)
    {
        return Invite::whereCode($code)->firstOrFail();
    }

    /**
     * Identify the email attribute name from validator parameter
     * 
     * @param Array $parameters
     * 
     * @return string
     */
    private function getEmailParameter($parameters)
    {
        return $parameters[0] ?: 'email';
    }
    
    /**
     * Validate the form submission for a valid invitation code.
     * This is extended through validator.
     * 
     * @param String $attribute
     * @param String $value
     * @param Array $parameters
     * @param Validator $validator
     * 
     * @return boolean
     * 
     * @throws \mathewparet\LaravelInvites\Exceptions\LaravelInvitesException
     */
    public function validate(/** @scrutinizer ignore-unused */ $attribute, /** @scrutinizer ignore-unused */ $value, $parameters, $validator)
    {
        $emailFieldName = $this->getEmailParameter($parameters);

        try {        
            $email = $validator->data[$emailFieldName];

            $this->check($value, $email);
            return true;
        }
        catch (InvalidInvitationCodeException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is invalid');
            return false;
        }
        catch (InvitationNotYetActiveException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is not valid yet');
            return false;
        }
        catch (InvitationExpiredException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute expired');
            return false;
        }
        catch (InvitationNotValidWithEmailException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is not valid with the provided '.$emailFieldName);
            return false;
        }
        catch (MaximumUseOfCodeException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute has been used for the maximum possible times');
            return false;
        }
    }

    /**
     * Check whether an invitation is valid with the provided email
     * 
     * @param string $code
     * @param string $email to be checked against
     * 
     * @return boolean
     */

    public function isValid($code, $email = null)
    {
        try
        {
            $this->check($code, $email);

            return true;
        }
        catch (LaravelInvitesException $e)
        {
            return false;
        }
    }

    /**
     * Check the validity of the invitiation code
     * 
     * @param string $code
     * @param string $email
     * 
     * @return boolean
     */
    public function check($code, $email = null)
    {
        $invite = Invite::whereCode($code)->first();

        if (!$invite)
            throw new InvalidInvitationCodeException;
        
        if ($invite->valid_from > now())
            throw new InvitationNotYetActiveException($invite->valid_from);

        if ($invite->valid_upto && $invite->valid_upto <= now())
            throw new InvitationExpiredException($invite->valid_upto);

        if ($invite->used_count > ($invite->allowed_count - 1))
            throw new MaximumUseOfCodeException($invite->allowed_count);

        if ($invite->email !== $email && !blank($invite->email))
            throw new InvitationNotValidWithEmailException($email, $invite->email);

        return true;
    }

    /**
     * Redeem the invitation code. Make sure the form field is validated before this calls to avoid exceptions
     * 
     * @param string $code
     * @param string $email (optional)
     * 
     * @return boolean
     */

    public function redeem($code, $email = null)
    {
        $this->check($code, $email);
     
        $this->find($code)->redeem();

        return true;
    }

    /**
     * Set a validity start date for the invitation
     * 
     * @param \Carbon\Carbon $date
     * 
     * @return \mathewparet\LaravelInvites\Facades\LaravelInvites
     */
    public function notBefore(Carbon $date)
    {
        $this->data['valid_from'] = $date;
        return $this;
    }

}