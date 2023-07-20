<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelRequest extends Model
{
    use HasFactory;

    public function district()
    {
        return $this->hasOne(District::class, 'id','district_id');
    }

    public function stationInfo()
    {
        return $this->hasOne(FuelStation::class, 'id','station_id');
    }

    public function vehicleInfo()
    {
        return $this->hasOne(CustomerVehicle::class, 'id','vehicle_id')->withTrashed();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id','customer_id');
    }
}
