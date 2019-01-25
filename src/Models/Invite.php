<?php

namespace mathewparet\LaravelInvites\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'email', 'allowed_count', 'valid_upto', 'valid_from'
    ];

    protected $dates = ['valid_from', 'valid_upto'];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

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
        $this->increment('used_count');

        if($this->used_count >= $this->allowed_count && config('laravelinvites.delete_on_full', true))
            $this->delete();
    }

    public function scopeValid($query)
    {
        return $query->where('valid_from','<=', now())
            ->where('valid_upto', '>=', now())
            ->whereRaw('allowed_count > used_count');
    }

    public function scopeUseless($query)
    {
        return $query->where('valid_upto', '<', now())
            ->orWhereRaw('allowed_count <= used_count');
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