<?php

namespace mathewparet\LaravelInvites\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelInvites extends Facade
{
    /**
     * @method mixed get() Get the invites from DB
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites for(string $email) Set the email property for generating or getting invitation
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites validFrom(\Carbon\Carbon $date) Get the validity start date for the invitation
     * @method mixed generate(integer $number_of_invites = 1) Generate invitations
     * @method \mathewparet\LaravelInvites\Models\Invite generateFor(string $email) Generate invitations for a particular email
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites allow(integer $num_allowed = 1) Set the number of uses allowed for this invite
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites withoutExpiry() Set expiry to never expire (overrides config)
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites setExpiry(\Carbon\Carbon $date) Set an expiry date
     * @method \mathewparet\LaravelInvites\Models\Invite find(string $code) Find an invitation based on code
     * @method boolean validate(string $attribute, string $value, array $parameters, Validator $validator) Validate the form submission for a valid invitation code
     * @method boolean isValid(strong $code, string $email = null) Check whether an invitation is valid with the provided email
     * @method boolean check(string $code, string $email = null) Check the validity of the invitiation code & thrown exception
     * @method boolean redeem(string $code, string $email = null) Redeem the invitation code. Make sure the form field is validated before this calls to avoid exceptions
     * @method \mathewparet\LaravelInvites\Facades\LaravelInvites notBefore(\Carbon\Carbon $date) Set a validity start date for the invitation
     */

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelinvites';
    }
}
