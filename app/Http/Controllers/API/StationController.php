<?php

namespace App\Http\Controllers\API;

use App\Models\RateCard;
use App\Models\FuelRequest;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Fulein\CustomerFuelRequestController;

class StationController extends Controller
{
    public function get_que(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'token' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $query = FuelRequest::with(['district','stationInfo','vehicleInfo']);
            if(isset($request->token) && !empty($request->token))
                $query = $query->where('token',$request->token);

            if (auth('api')->user()->hasRole('Manager'))
                $query = $query->where('station_id', auth('api')->user()->userStation->station_id);

        $fuel_request = $query->orderBy('id','DESC')->get();

        $data = [];
        foreach($fuel_request as $item)
        {
            $fuel_type = '';
            if ($item->fuel_type == 1) {
                $fuel_type = 'Petrol';
            }

            if ($item->fuel_type == 2) {
                $fuel_type = 'Diesel';
            }

            $rate_card = RateCard::find(1);

                if ($item->fuel_type == 1) {
                    $fuel_price = $rate_card->amount_petrol * $item->fuel_value;
                }

                if ($item->fuel_type == 2) {
                    $fuel_price = $rate_card->amount_diesel * $item->fuel_value;
                }

            $data[] = [
                'id' => $item->id,
                'token' => $item->token,
                'district_id' => $item->district_id,
                'district' => $item->district->name,
                'station_id' => $item->station_id,
                'station' => $item->stationInfo->name,
                'vehicle_id' => $item->vehicle_id,
                'vehicle' => $item->vehicleInfo->vehicle_no,
                'fuel_price' => $fuel_price,
                'fuel_type' => $fuel_type,
                'request_date' => $item->request_date,
                'schedule_date' => $item->schedule_date,
                'fuel_value' => $item->fuel_value,
                'fuel_amount' => $item->fuel_amount,
                'status' => $item->status == 0 ? 'Pending' : 'Scheduled',
                'paid_status' => $item->pay_status == 0 ? 'Pending' : 'Paid'
            ];
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update_que(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'Request Id field required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        //Get the Customer Fuel Request
        $fuel_request = FuelRequest::find($request->id);

        //Get the Fuel Station Details
        $station = FuelStation::find($fuel_request->station_id);

        $av_fuel = 0;

        $rate_card = RateCard::find(1);

        //Update the Petrol Available Fuel
        if ($fuel_request->fuel_type == 1) {
            $station->av_petrol = $station->av_petrol - $fuel_request->fuel_value;
            $av_fuel =  $station->av_petrol;
            $fuel_amount = $rate_card->amount_petrol * $fuel_request->fuel_value;
        }

        //Update the Diesel Available Fuel
        if ($fuel_request->fuel_type == 2) {
            $station->av_diesel = $station->av_diesel - $fuel_request->fuel_value;
            $av_fuel =  $station->av_diesel;
            $fuel_amount = $rate_card->amount_diesel * $fuel_request->fuel_value;
        }

        if ($av_fuel <= $fuel_request->fuel_value) {
            return response()->json(['status' => false, 'message' => 'Station do not have enough Fuel']);
        }

        $fuel_request->status = 2; //Pumbed
        $fuel_request->pay_status = 1; //Paid
        $fuel_request->fuel_amount = $fuel_amount;
        $fuel_request->update();

        //Update the Station
        $station->update();

        return response()->json(['status' => true, 'message' => 'Fuel Request Updated']);
    }
}
