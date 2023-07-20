<?php

namespace Database\Seeders;

use App\Models\RateVehicleCard;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class RateVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $veh_types = VehicleType::all();

        $amount = 0;
        foreach($veh_types as $item)
        {
            $amount = $amount + 10;
            $type_arr = explode(' ', $item->type);

            $type = implode('_',$type_arr);

            $rate = new RateVehicleCard();
            $rate->vehicle_type = strtolower($type);
            $rate->amount = $amount;
            $rate->save();
        }
    }
}
