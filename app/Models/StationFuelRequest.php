<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StationFuelRequest extends Model
{
    use HasFactory, SoftDeletes;

    public function district()
    {
        return $this->hasOne(District::class, 'id','district_id');
    }

    public function stationInfo()
    {
        return $this->hasOne(FuelStation::class, 'id','station_id');
    }
}
