<?php

namespace App\Http\Controllers\Customer;

use App\Models\District;
use App\Models\FuelRequest;
use App\Models\FuelStation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerVehicle;
use App\Http\Controllers\Controller;
use App\Models\RateCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class QueRequestController extends Controller
{
    public function index()
    {
        return view('customers.que.index');
    }

    public function que_request_get()
    {
        $fuel_request = FuelRequest::with(['district','stationInfo','vehicleInfo'])->where('customer_id',Auth::guard('customer')->user()->id)->orderBy('id','DESC');

        $data =  Datatables::of($fuel_request)
            ->addIndexColumn()
            ->addColumn('fuel_type', function ($item) {
                if ($item->fuel_type == 1) {
                    return 'Petrol';
                }

                if ($item->fuel_type == 2) {
                    return 'Diesel';
                }
            })
            ->addColumn('district', function ($item) {

                return $item->district->name;
            })
            ->addColumn('station', function ($item) {

                return $item->stationInfo->name;
            })
            ->addColumn('vehicle', function ($item) {

                return $item->vehicleInfo->vehicle_no;
            })
            ->addColumn('fuel_price', function ($item) {
                $rate_card = RateCard::find(1);

                if ($item->fuel_type == 1) {
                    return $rate_card->amount_petrol * $item->fuel_value;
                }

                if ($item->fuel_type == 2) {
                    return $rate_card->amount_diesel * $item->fuel_value;
                }
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return '<span class="badge badge-danger">Pending</span>';
                }

                if ($item->status == 1) {
                    return '<span class="badge badge-success">Scheduled</span>';
                }
            })
            ->addColumn('paid_status', function ($item) {
                if ($item->pay_status == 0) {
                    return '<span class="badge badge-danger">Pending</span>';
                }

                if ($item->pay_status == 1) {
                    return '<span class="badge badge-success">Paid</span>';
                }
            })
            ->addColumn('action', function ($item) {
                if ($item->status == 0) {
                    return '<a href="#" class="btn btn-sm btn-danger deletebtn square-btn" onclick="deleteConfirmation('. $item->id . ')" data-id="'. $item->id . '"><svg id="delete_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_781" data-name="Path 781" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_782" data-name="Path 782" d="M5.653,13.452A1.31,1.31,0,0,0,6.96,14.758h5.226a1.31,1.31,0,0,0,1.306-1.306V6.919a1.31,1.31,0,0,0-1.306-1.306H6.96A1.31,1.31,0,0,0,5.653,6.919Zm7.839-9.8H11.859L11.4,3.189A.659.659,0,0,0,10.938,3H8.207a.659.659,0,0,0-.457.189l-.464.464H5.653a.653.653,0,0,0,0,1.306h7.839a.653.653,0,1,0,0-1.306Z" transform="translate(-1.734 -1.04)" fill="#fff"/></svg></a>';
                }
            })
            ->rawColumns(['district','station', 'district','vehicle','fuel_type','fuel_price','status','paid_status','action'])
            ->make(true);

        return $data;
    }

    public function add_form()
    {
        $stations = FuelStation::all();
        $district = District::all();
        $vehicles = CustomerVehicle::where('customer_id', Auth::guard('customer')->user()->id)->get();

        return view('customers.que.create', [
            'stations' => $stations,
            'district' => $district,
            'vehicles' => $vehicles
        ]);
    }

    public function av_fuel(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'Vechile Id field required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $id = $request->id;
        $vehicles = CustomerVehicle::find($id);

        $av_fuel = $vehicles->av_quota;

        $status = false;
        if($av_fuel > 0)
        {
            $status = true;
        }

        $type = $vehicles->fuel_type;

        return response()->json(['status'=>$status, 'av_fuel' => $av_fuel, 'type' => $type]);
    }

    public function av_fuel_check(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_id' => 'required|numeric',
                'fuel_amount' => 'required|numeric|between:0,9999999999999999999.99',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $fuel_amount = $request->fuel_amount;
        $id = $request->vehicle_id;

        $vehicles = CustomerVehicle::find($id);

        $av_fuel = $vehicles->av_quota;

        $status = false;
        if($av_fuel >=$fuel_amount)
        {
            $status = true;
        }

        return response()->json([
            'status'=>$status,
            'av_fuel' => $av_fuel,
            'message' => $status == false ? 'Fuel Amount can not be grater than '.$av_fuel : 'Success'
        ]);
    }

    public function get_station(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fuel_amount' => 'required|numeric|between:0,9999999999999999999.99',
                'vehicle' => 'required',
                'district' => 'required',
                'fuel_type' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $stations = FuelStation::where(['district_id'=> $request->district, 'status'=>1])->get();

        $data = [];
        foreach($stations as $item)
        {
            $rquested_fuel = $item->getQue->where('fuel_type',$request->fuel_type)->whereIn('status',[0,1])->sum('fuel_value');

            if ($request->fuel_type == 1) {
                $av_fuel = $item->petrol - $rquested_fuel;
            }

            if ($request->fuel_type == 2) {
                $av_fuel = $item->diesel - $rquested_fuel;
            }

            if($av_fuel >= $request->fuel_amount )
            {
                $data[] = $item;
            }
        }

        return response()->json(['stations'=>$data]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fuel_amount' => 'required|numeric|between:0,9999999999999999999.99',
                'vehicle' => 'required',
                'district' => 'required',
                'station' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $fuel_request = new FuelRequest();
        $fuel_request->token = strtoupper(Str::random(8));
        $fuel_request->district_id = $request->district;
        $fuel_request->station_id = $request->station;
        $fuel_request->customer_id = Auth::guard('customer')->user()->id;
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

    public function delete(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $id = $request->id;
        $fuel_request = FuelRequest::find($id);
        $vehicle = CustomerVehicle::find($fuel_request->vehicle_id);
        $vehicle->av_quota =$vehicle->av_quota + $fuel_request->fuel_value;
        $vehicle->update();

        FuelRequest::destroy($id);
        return response()->json(['status' => true,  'message' => 'Your Que Request Deleted Successfully']);
    }
}
