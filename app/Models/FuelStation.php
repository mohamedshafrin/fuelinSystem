<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelStation extends Model
{
    use HasFactory, SoftDeletes;

    public function district()
    {
        return $this->hasOne(District::class, 'id','district_id');
    }

    public function getQue()
    {
        return $this->hasMany(FuelRequest::class, 'station_id','id');
    }

    public function getStationFuelRequest()
    {
        return $this->hasMany(StationFuelRequest::class,'station_id','id');
    }
}
