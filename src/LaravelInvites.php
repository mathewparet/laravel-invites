<?php

namespace mathewparet\LaravelInvites;

use mathewparet\LaravelInvites\Exceptions\InvalidInvitationCodeException;
use mathewparet\LaravelInvites\Exceptions\InvalidEmailIdException;
use mathewparet\LaravelInvites\Exceptions\InvitationNotYetActiveException;
use mathewparet\LaravelInvites\Exceptions\InvitationExpiredException;
use mathewparet\LaravelInvites\Exceptions\InvitationNotValidWithEmailException;
use mathewparet\LaravelInvites\Exceptions\MaximumUseOfCodeException;

use Carbon\Carbon;
use Validator;

use mathewparet\LaravelInvites\Models\Invite;

/**
 * Usage
 * use mathewparet\LaravelInvites\LaravelInvites;
 * 
 * $invitation = LaravelInvites::withoutExpiry()->allowUses(10)->generate(100);
 */

class LaravelInvites
{
    private $number_of_invites = 1;
    private $data = [];

    private function initializeData()
    {
        $this->data = ['allow_count' => 1];
    }

    public function __construct()
    {
        $this->initializeData();
    }

    public function for($email)
    {
        $validator = Validator::make(compact('email'),[
            'email'=>'required|email'
        ]);
        if($validator->fails())
            throw new InvalidEmailIdException;

        $this->data['email'] = $email;

        return $this;
    }

    private function prepareSingle()
    {
        return Invite::create($this->data);
    }

    public function validFrom($date = null)
    {
        $this->data['valid_from'] = $date ? : now();

        return $this;
    }

    /**
     * Generate the requested invitations
     * 
     * @return \mathewparet\LaravelInvites\Model\Invite::clss $invite
     */
    private function prepare()
    {
        if($this->number_of_invites == 1)
            return $this->prepareSingle();

        $invites = [];

        for($i = 0; $i < $this->number_of_invites; $i++)
        {
            $invites[] = $this->prepareSingle();
        }

        return $invites;
    }

    /**
     * Generate invitations
     * 
     * @param intiger $number_of_invites | default = 1
     * 
     * @return mixed array of invitations generated
     */
    public function generate($number_of_invites = 1)
    {
        $this->number_of_invites = $number_of_invites;

        $invitations =  $this->prepare();
        
        $this->initializeData();

        return $invitations;
    }

    /**
     * Set the number of uses allowed for this invite
     * 
     * @param integer $num_allowed
     * 
     * @return $this
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
     * @return reference to self
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
     * @return \mathewparet\LaravelInvites\Model\Invite $invite
     */
    public function find($code)
    {
        return Invite::whereCode($code)->firstOrFail();
    }

    
    public function validate($attribute, $value, $parameters, $validator)
    {
        $emailFieldName = $parameters[0] ? : 'email';

        try {        
            $email = $validator->data[$emailFieldName];

            $this->check($code, $email);
            return true;
        }
        catch(InvalidInvitationCodeException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is invalid');
            return false;
        }
        catch(InvitationNotYetActiveException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is not valid yet');
            return false;
        }
        catch(InvitationExpiredException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute expired');
            return false;
        }
        catch(InvitationNotValidWithEmailException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute is not valid with the provided '.$emailFieldName);
            return false;
        }
        catch(MaximumUseOfCodeException $e)
        {
            $validator->errors()->add($emailFieldName, ':attribute has been used for the maximum possible times');
            return false;
        }
    }

    /**
     * Check the validity of the invitiation code
     * 
     * @param string $code
     * @param string $email
     */
    public function check($code, $email=null)
    {
        $invite = Invite::whereCode($code)->first();

        if(!$invite)
            throw new InvalidInvitationCodeException;
        
        if($invite->valid_from > now())
            throw new InvitationNotYetActiveException;

        if($invite->valid_upto && $invite->valid_upto <= now())
            throw new InvitationExpiredException;

        if($invite->used_count > ($invite->allowed_count-1))
            throw new MaximumUseOfCodeException;

        if($invite->email !== $email && !blank($invite->email))
            throw new InvitationNotValidWithEmailException;

        return true;
    }

    /**
     * Redeem the invitation code. Make sure the form field is validated before this calls to avoid exceptions
     * 
     * @param string $code
     * @param string $email (optional)
     * 
     * @return mixed
     */

    public function redeem($code, $email = null)
    {
        $this->check($code, $email);
     
        $this->find($code)->redeem();

        return true;
    }
}