<?php

namespace App\Http\Controllers\Fulein;

use App\Models\District;
use App\Models\RateCard;
use App\Models\FuelRequest;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerFuelRequestController extends Controller
{
    public function index()
    {
        $stations = FuelStation::all();
        $district = District::all();

        return view('fuelin.customer_request.index', [
            'stations' => $stations,
            'district' => $district
        ]);
    }

    public function request_fuel_cus_get(Request $request)
    {
        $query = FuelRequest::with(['district', 'stationInfo', 'vehicleInfo', 'customer']);

        if (isset($request->station) && !empty($request->station))
            $query = $query->where('station_id', $request->station);

        if (isset($request->district) && !empty($request->district))
            $query = $query->where('district_id', $request->district);

        if (Auth::user()->hasRole('Manager'))
            $query = $query->where('station_id', Auth::user()->userStation->station_id);

        $fuel_request = $query->orderBy('id', 'DESC');

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
            ->addColumn('customer', function ($item) {

                return $item->customer->first_name . ' ' . $item->customer->last_name;
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
                    return '<span class="badge badge-primary">Scheduled</span>';
                }

                if ($item->status == 2) {
                    return '<span class="badge badge-success">Pumped</span>';
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
                if ($item->status == 1 && $item->pay_status == 0) {
                    return '<button type="button" class="btn btn-sm btn-primary deletebtn square-btn" onclick="deleteConfirmation(' . $item->id . ')" data-id="' . $item->id . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></a>';
                }
            })
            ->rawColumns(['district', 'station', 'district', 'vehicle', 'fuel_type', 'fuel_price', 'status', 'paid_status', 'customer', 'action'])
            ->make(true);

        return $data;
    }

    public function update(Request $request)
    {
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
