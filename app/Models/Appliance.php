<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    protected $fillable = [
        'user_id',
        'power_setup_id',
        'name',
        'voltage',
        'watts',
        'hours',
        'quantity',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function powerSetup()
    {
        return $this->belongsTo(PowerSetup::class);
    }

}
