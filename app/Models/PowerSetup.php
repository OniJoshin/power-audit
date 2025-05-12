<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerSetup extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'system_voltage',
        'inverter_efficiency',
        'battery_type',
        'autonomy_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appliances()
    {
        return $this->hasMany(Appliance::class);
    }

}
