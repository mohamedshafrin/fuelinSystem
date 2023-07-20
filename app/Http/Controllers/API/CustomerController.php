<?php

namespace App\Http\Controllers\API;

use App\Models\District;
use App\Models\RateCard;
use App\Models\FuelRequest;
use App\Models\VehicleType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerVehicle;
use App\Models\RateVehicleCard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function vechicle_type()
    {
        $vehicle_type = VehicleType::all();

        return response()->json(['status'=>true, 'vehicle_type' => $vehicle_type]);
    }

    public function district_list()
    {
        $district = District::all();

        return response()->json(['status'=>true, 'district' => $district]);
    }

    public function addvehicles(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_number' => 'required|unique:customer_vehicles,vehicle_no,NULL,id,deleted_at,NULL',
                'vehicle_type' => 'required',
                'fuel_type' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $vehicle_type = VehicleType::find($request->vehicle_type);
        $type_arr = explode(' ', $vehicle_type->type);
        $type = implode('_',$type_arr);

        $allocated_quota = RateVehicleCard::where('vehicle_type',strtolower($type))->first();

        $vehicle = new CustomerVehicle();
        $vehicle->customer_id = auth('api_customer')->user()->id;
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_no = $request->vehicle_number;
        $vehicle->fuel_type = $request->fuel_type;
        $vehicle->quota = $allocated_quota->amount;
        $vehicle->av_quota = $allocated_quota->amount;
        $vehicle->save();

        return response()->json(['status' => true,  'message' => 'Your Vehicle Added Successfully']);
    }

    public function vehicles()
    {
        $vehicles = CustomerVehicle::with('vehicleType')->where('customer_id', auth('api_customer')->user()->id)->get();

        $data = [];

        foreach($vehicles as $item)
        {
            $data[] = [
                'id' => $item->id,
                'vehicle_type_id' => $item->vehicle_type,
                'vehicle_type' => $item->vehicleType->type,
                'vehicle_number' => $item->vehicle_no,
                'fuel_type' => $item->fuel_type == 1 ? 'Petrol' : 'Diesel',
                'allocated_quota' => $item->quota,
                'available_quota' => $item->av_quota
            ];
        }

        return response()->json(['status' => true, 'data'=>$data]);
    }

    public function create_que(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fuel_amount' => 'required|numeric|between:0,9999999999999999999.99',
                'vehicle' => 'required',
                'district' => 'required',
                'station' => 'required',
                'fuel_type' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $fuel_request = new FuelRequest();
        $fuel_request->token = strtoupper(Str::random(8));
        $fuel_request->district_id = $request->district;
        $fuel_request->station_id = $request->station;
        $fuel_request->customer_id = auth('api_customer')->user()->id;
        $fuel_request->vehicle_id = $request->vehicle;
        $fuel_request->fuel_type = $request->fuel_type;
        $fuel_request->request_date = date('Y-m-d');
        $fuel_request->fuel_value = $request->fuel_amount;
        $fuel_request->save();

        $vehicles = CustomerVehicle::find($request->vehicle);
        $vehicles->av_quota = $vehicles->av_quota - $request->fuel_amount;
        $vehicles->update();

        return response()->json(['status' => true,  'message' => 'Your Que Request Submitted Successfully']);
    }

    public function request_list(Request $request)
    {
        $query = FuelRequest::with(['district','stationInfo','vehicleInfo']);
            if(isset($request->token) && !empty($request->token))
                $query = $query->where('token',$request->token);

        $fuel_request = $query->where('customer_id',auth('api_customer')->user()->id)->orderBy('id','DESC')->get();

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
}
