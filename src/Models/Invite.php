<?php

namespace mathewparet\LaravelInvites\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'email', 'allowed_count', 'valid_upto', 'valid_from'
    ];

    public function __construct(array $attributes = [])
    {

        $this->code = Str::uuid();
        $this->valid_from = now();
        $this->allowed_count = 1;

        $this->valid_upto = $this->setDefaultExpiry();

        $this->table = config('laravelinvites.table');

        parent::__construct($attributes);

    }

    public function redeem()
    {
        $this->used_count ++;
        $this->save();
    }

    /**
     * Set default expiry as per configuration
     */
    private function setDefaultExpiry()
    {
        if(config('laravelinvites.expiry.type')==='none')
            return null;
        
        if(config('laravelinvites.expiry.type') === 'hours')
            return now()->addHours(config('laravelinvites.expiry.value'));
            
        elseif(config('laravelinvites.expiry.type') === "days")
            return now()->addDays(config('laravelinvites.expiry.days'));
    }
}