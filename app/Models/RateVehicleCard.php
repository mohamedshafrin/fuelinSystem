<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateVehicleCard extends Model
{
    use HasFactory;

    public function vehicleType()
    {
        return $this->hasOne(vehicleType::class,'id','vehicle_type');
    }
}
