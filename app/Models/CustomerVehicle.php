<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVehicle extends Model
{
    use HasFactory, SoftDeletes;

    public function vehicleType()
    {
        return $this->hasOne(VehicleType::class, 'id', 'vehicle_type');
    }
}
