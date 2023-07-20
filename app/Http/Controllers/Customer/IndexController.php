<?php

namespace App\Http\Controllers\Customer;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Models\CustomerVehicle;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\RateVehicleCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function index()
    {
        return view('customers.vehicle.index');
    }

    public function get_vehicles()
    {
        $vehicles = CustomerVehicle::where('customer_id', Auth::guard('customer')->user()->id);

        $data =  Datatables::of($vehicles)
            ->addIndexColumn()
            ->addColumn('fuel_type', function ($item) {
                if ($item->fuel_type == 1) {
                    return 'Petrol';
                }

                if ($item->fuel_type == 2) {
                    return 'Diesel';
                }
            })
            ->addColumn('vehicle_type', function ($item) {
                return $item->vehicleType->type;
            })
            ->rawColumns(['vehicle_type', 'fuel_type'])
            ->make(true);

        return $data;
    }

    public function add_form()
    {
        $vehicle_type = VehicleType::all();

        return view('customers.vehicle.create',['vehicle_type'=>$vehicle_type]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_number' => ['required','max:8','min:7','unique:customer_vehicles,vehicle_no,NULL,id,deleted_at,NULL','regex:/^([A-Z]{3}[-][0-9]{4}|[A-Z]{2}[-][0-9]{4})$/u'],
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
        $vehicle->customer_id = Auth::guard('customer')->user()->id;
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_no = $request->vehicle_number;
        $vehicle->fuel_type = $request->fuel_type;
        $vehicle->quota = $allocated_quota->amount;
        $vehicle->av_quota = $allocated_quota->amount;
        $vehicle->save();

        return response()->json(['status' => true,  'message' => 'Your Vehicle Added Successfully']);
    }
}
