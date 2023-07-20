<?php

namespace App\Http\Controllers\Fulein;

use App\Models\FuelRequest;
use App\Models\FuelStation;
use App\Models\StationUser;
use Illuminate\Http\Request;
use App\Models\StationFuelRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('Manager'))
        {
            $user = StationUser::where('user_id',Auth::user()->id)->first();
            $station = FuelStation::find($user->station_id);
            return view('fuelin.report.manager',['station'=>$station]);
        }
        else
        {
            $stations = FuelStation::where('status',1)->get();

            return view('fuelin.report.admin',[
                'stations' => $stations
            ]);
        }
    }

    public function getAdminReport(Request $request)
    {
        $pumb_stations_value = $this->pumbedStationValue($request);
        $station_request = $this->stationRequestCount($request);
        $customer_request = $this->customerRequestCount($request);
        $customer_pumbed_value = $this->pumbedCustomerValue($request);

        return response()->json([
            'pumb_stations_value' => $pumb_stations_value,
            'station_request' => $station_request,
            'customer_request' => $customer_request,
            'customer_pumbed_value' => $customer_pumbed_value
        ]);
    }

    function pumbedStationValue($request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = FuelStation::with('getStationFuelRequest');
                if ($request->station != 0)
                    $query = $query->where('id',$request->station);

        $station = $query->get();

        $petrol = [];
        $diesel = [];
        $stations = [];
        $petrol_total = 0;
        $diesel_total = 0;

        foreach($station as $item)
        {
            $query_petrol = $item->getStationFuelRequest();
                    if (isset($start_date) && !empty($start_date))
                    $query_petrol = $query_petrol->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)));

                    if (isset($start_date) && !empty($start_date))
                    $query_petrol = $query_petrol->whereDate('pumbed_date', '<=',date('Y-m-d', strtotime($end_date)));

            $petrol_val = $query_petrol->where('status',3)->sum('petrol');

            $query_diesel = $item->getStationFuelRequest();
                    if (isset($start_date) && !empty($start_date))
                    $query_diesel = $query_diesel->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)));

                    if (isset($end_date) && !empty($end_date))
                    $query_diesel = $query_diesel->whereDate('pumbed_date', '<=',date('Y-m-d', strtotime($end_date)));

            $diesel_val = $query_diesel->where('status',3)->sum('petrol');

            $petrol_total = floatval($petrol_total + $petrol_val);
            $diesel_total = floatval($diesel_total + $diesel_val);

            $petrol[] = floatval($petrol_val);
            $diesel[] = floatval($diesel_val);

            $stations[] = $item->name;
        }

        $data = [
            'petrol' => $petrol,
            'diesel' => $diesel,
            'petrol_total' => $petrol_total,
            'diesel_total' => $diesel_total,
            'total_revenue' => floatval($diesel_total + $petrol_total),
            'stations' => $stations
        ];

        return $data;
    }

    public function stationRequestCount($request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $status = [
            0 => 'Pending',
            1 => 'Accepted',
            2 => 'Dispatched',
            3 => 'Pumbed'
        ];

        $countval = [];

        foreach ($status as $key => $value) {
            $query = StationFuelRequest::with('stationInfo');

            if ($request->station != 0)
                $query = $query->where('station_id',$request->station);

            if (isset($start_date) && !empty($start_date))
                $query = $query->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)));

            if (isset($end_date) && !empty($end_date))
                $query = $query->whereDate('pumbed_date', '<=',date('Y-m-d', strtotime($end_date)));

            $count = $query->where('status',$key)->count();

            $countval[] = $count;
        }

        return $countval;
    }

    public function customerRequestCount($request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $status = [
            0 => 'Pending',
            1 => 'Accepted',
            2 => 'Pumbed'
        ];

        $countval = [];

        foreach ($status as $key => $value) {
            $query = FuelRequest::with('stationInfo');

            if ($request->station != 0)
                $query = $query->where('station_id',$request->station);

            if (isset($start_date) && !empty($start_date))
                $query = $query->whereDate('updated_at', '>=',date('Y-m-d', strtotime($start_date)));

            if (isset($end_date) && !empty($end_date))
                $query = $query->whereDate('updated_at', '<=',date('Y-m-d', strtotime($end_date)));

            $count = $query->where('status',$key)->count();

            $countval[] = $count;
        }

        return $countval;
    }

    function pumbedCustomerValue($request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = FuelStation::with('getQue');
                if ($request->station != 0)
                    $query = $query->where('id',$request->station);

        $station = $query->get();

        $petrol = [];
        $diesel = [];
        $stations = [];
        $petrol_total = 0;
        $diesel_total = 0;

        foreach($station as $item)
        {
            $query_petrol = $item->getQue();
                    if (isset($start_date) && !empty($start_date))
                    $query_petrol = $query_petrol->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)));

                    if (isset($start_date) && !empty($start_date))
                    $query_petrol = $query_petrol->whereDate('request_date', '<=',date('Y-m-d', strtotime($end_date)));

            $petrol_val = $query_petrol->where('status',2)->where('fuel_type',1)->sum('fuel_value');

            $query_diesel = $item->getQue();
                    if (isset($start_date) && !empty($start_date))
                    $query_diesel = $query_diesel->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)));

                    if (isset($end_date) && !empty($end_date))
                    $query_diesel = $query_diesel->whereDate('request_date', '<=',date('Y-m-d', strtotime($end_date)));

            $diesel_val = $query_diesel->where('status',2)->where('fuel_type',2)->sum('fuel_value');

            $petrol_total = floatval($petrol_total + $petrol_val);
            $diesel_total = floatval($diesel_total + $diesel_val);

            $petrol[] = floatval($petrol_val);
            $diesel[] = floatval($diesel_val);

            $stations[] = $item->name;
        }

        $data = [
            'petrol' => $petrol,
            'diesel' => $diesel,
            'petrol_total' => $petrol_total,
            'diesel_total' => $diesel_total,
            'total_revenue' => floatval($diesel_total + $petrol_total),
            'stations' => $stations
        ];

        return $data;
    }

    public function report_validation(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'start_date' => 'required',
                'end_date' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        return response()->json(['status'=>true,'message'=>'success']);
    }
}
