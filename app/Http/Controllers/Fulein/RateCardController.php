<?php

namespace App\Http\Controllers\Fulein;

use App\Models\RateCard;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Models\RateVehicleCard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RateCardController extends Controller
{
    public function index()
    {
        $rate_card = RateCard::find(1);
        $rate_card_veh = RateVehicleCard::all();

        $data = [];
        foreach($rate_card_veh as $item)
        {
            $type_arr = explode('_', $item->vehicle_type);

            $type = implode(' ',$type_arr);
            $data[] = [
                'type'=> ucwords($type),
                'type_name'=>strtolower($item->vehicle_type),
                'amount' => $item->amount
            ];
        }

        return view('fuelin.rate_card.index',[
            'rate_card' => $rate_card,
            'rate_card_veh' => $data
        ]);
    }

    public function update(Request $request)
    {
        $veh_types = VehicleType::all();

        $validator = Validator::make(
            $request->all(),
            [
                'amount_petrol' => 'required|numeric|between:0,999999999999999.99',
                'amount_diesel' => 'required|numeric|between:0,999999999999999.99',
                'motor_cycles' => 'required|numeric',
                'three_wheelers' => 'required|numeric',
                'passenger_cars' => 'required|numeric',
                'tractors_and_engines' => 'required|numeric',
                'lorries' => 'required|numeric',
                'dual_purpose_vehicles' => 'required|numeric',
                'buses' => 'required|numeric',
                'ambulances_and_hearses' => 'required|numeric',
                'quadricycle' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $rate_card = RateCard::find(1);
        $rate_card->amount_petrol = $request->amount_petrol;
        $rate_card->amount_diesel = $request->amount_diesel;
        $rate_card->update();

        foreach($veh_types as $item)
        {
            $type_arr = explode(' ', $item->type);

            $type = implode('_',$type_arr);

            $rate_card_veh = RateVehicleCard::where('vehicle_type',strtolower($type))->first();
            $rate_card_veh->amount = $request->input(strtolower($type));
            $rate_card_veh->update();
        }

        return response()->json(['status'=>true, 'message'=>'Rate Card Updated Successfully']);
    }

    public function get_vechile_type()
    {
        $veh_types = VehicleType::all();

        $data = [];
        foreach($veh_types as $item)
        {
            $type_arr = explode(' ', $item->type);

            $type = implode('_',$type_arr);
            $data[] = strtolower($type);
        }
        return response()->json(['data'=>$data]);
    }
}
