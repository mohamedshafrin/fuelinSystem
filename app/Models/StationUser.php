<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StationUser extends Model
{
    use HasFactory, SoftDeletes;

    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function stationInfo()
    {
        return $this->hasOne(FuelStation::class, 'id', 'station_id');
    }
}
