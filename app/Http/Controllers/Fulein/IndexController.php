<?php

namespace App\Http\Controllers\Fulein;

use App\Models\FuelStation;
use App\Models\StationUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\StationFuelRequest;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('Manager'))
        {
            return $this->managerDashboard();
        }
        else
        {
            return $this->adminDashboard();
        }

    }

    public function adminDashboard()
    {
        $station_count = FuelStation::where('status',1)->count();
        $customers_count = Customer::where('status',1)->count();
        $pending_request = StationFuelRequest::where('status',0)->count();
        $scheduled_request = StationFuelRequest::where('status',1)->count();
        $dispatched_request = StationFuelRequest::whereIn('status',[2,3])->count();

        return view('fuelin.index',[
            'station_count' => $station_count,
            'customers_count' => $customers_count,
            'pending_request' => $pending_request,
            'scheduled_request' => $scheduled_request,
            'dispatched_request' => $dispatched_request
        ]);
    }

    public function managerDashboard()
    {
        $start_date = date('Y-m-').'01';
        $end_date = date('Y-m-t');

        $user = StationUser::where('user_id',Auth::user()->id)->first();
        $station = FuelStation::find($user->station_id);

        $customers_count = Customer::where('status',1)->count();
        $pending_request = $station->getStationFuelRequest()->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('pumbed_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[0,1,2])->count();
        $completed_request = $station->getStationFuelRequest()->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('pumbed_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[3])->count();

        $pending_customer_request = $station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[0,1])->count();
        $completed_customer_request = $station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->count();

        $total_fuel_amount = floatval($station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->sum('fuel_value'));

        return view('fuelin.managerDashboard',[
            'station' =>$station,
            'customers_count' => $customers_count,
            'pending_request' => $pending_request,
            'completed_request' => $completed_request,
            'pending_customer_request' => $pending_customer_request,
            'completed_customer_request' => $completed_customer_request,
            'total_fuel_amount' => $total_fuel_amount
        ]);
    }

    public function admin_monthly_fuel()
    {
        $start_date = date('Y-m-').'01';
        $end_date = date('Y-m-t');

        $station = FuelStation::with('getStationFuelRequest')->get();

        $petrol = [];
        $diesel = [];
        $stations = [];
        foreach($station as $item)
        {
            $petrol[] = floatval($item->getStationFuelRequest()->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('pumbed_date' ,'<=',date('Y-m-d', strtotime($end_date)))->where('status',3)->sum('petrol'));
            $diesel[] = floatval($item->getStationFuelRequest()->whereDate('pumbed_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('pumbed_date' ,'<=',date('Y-m-d', strtotime($end_date)))->where('status',3)->sum('diesel'));
            $stations[] = $item->name;
        }

        return response()->json([
            'petrol' => $petrol,
            'diesel' => $diesel,
            'stations' => $stations
        ]);
    }

    public function admin_monthly_cust_fuel()
    {
        $start_date = date('Y-m-').'01';
        $end_date = date('Y-m-t');

        $station = FuelStation::with('getStationFuelRequest')->get();

        $petrol = [];
        $diesel = [];
        $stations = [];
        foreach($station as $item)
        {
            $petrol[] = floatval($item->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->where('fuel_type',1)->sum('fuel_value'));
            $diesel[] = floatval($item->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->where('fuel_type',2)->sum('fuel_value'));
            $stations[] = $item->name;
        }

        return response()->json([
            'petrol' => $petrol,
            'diesel' => $diesel,
            'stations' => $stations
        ]);
    }

    public function manager_monthly_fuel()
    {
        $start_date = date('Y-m-').'01';
        $end_date = date('Y-m-t');

        $user = StationUser::where('user_id',Auth::user()->id)->first();
        $station = FuelStation::find($user->station_id);

        $petrol_count = $station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->where('fuel_type',1)->count();
        $diesel_count = $station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->where('fuel_type',2)->count();

        $petrol_amount = floatval($station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->where('fuel_type',1)->sum('fuel_amount'));
        $diesel_amount = floatval($station->getQue()->whereDate('request_date', '>=',date('Y-m-d', strtotime($start_date)))->whereDate('request_date' ,'<=',date('Y-m-d', strtotime($end_date)))->whereIn('status',[2])->where('fuel_type',2)->sum('fuel_amount'));

        return response()->json([
            'data_count' => [$petrol_count,$diesel_count],
            'data_amount' => [$petrol_amount,$diesel_amount]
        ]);
    }
}
