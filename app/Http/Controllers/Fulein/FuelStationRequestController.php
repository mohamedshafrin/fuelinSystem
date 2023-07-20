<?php

namespace App\Http\Controllers\Fulein;

use App\Models\District;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\StationFuelRequest;
use App\Http\Controllers\Controller;
use App\Models\FuelRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class FuelStationRequestController extends Controller
{
    public function index()
    {
        $stations = FuelStation::all();
        $district = District::all();

        return view('fuelin.station_fuel_request.index', [
            'stations' => $stations,
            'district' => $district
        ]);
    }

    public function request_fuel_get(Request $request)
    {
        $query = StationFuelRequest::with(['district', 'stationInfo']);

        if (isset($request->station) && !empty($request->station))
            $query = $query->where('station_id', $request->station);

        if (isset($request->district) && !empty($request->district))
            $query = $query->where('district_id', $request->district);

        $station = $query->where('status', 0)->orderBy('id', 'DESC');

        $data =  Datatables::of($station)
            ->addIndexColumn()
            ->addColumn('status', function ($item) {
                if ($item->status == '0') {
                    return '<span class="badge badge-danger"> Not Accepted </span>';
                }

                if ($item->status == '1') {
                    return '<span class="badge badge-primary"> Accepted </span>';
                }

                if ($item->status == '2') {
                    return '<span class="badge badge-success"> Pumped </span>';
                }
            })
            ->addColumn('district', function ($item) {

                return $item->district->name;
            })
            ->addColumn('station', function ($item) {

                return $item->stationInfo->name;
            })
            ->addColumn('alloc_petrol', function ($item) {

                return $item->stationInfo->petrol;
            })
            ->addColumn('alloc_diesel', function ($item) {

                return $item->stationInfo->diesel;
            })
            ->addColumn('pum_petrol', function ($item) {
                $pum_petrol = '';
                if ($item->status == 2) {
                    $pum_petrol = $item->petrol;
                }

                return $pum_petrol;
            })
            ->addColumn('pum_diesel', function ($item) {
                $pum_diesel = '';
                if ($item->status == 2) {
                    $pum_diesel = $item->diesel;
                }

                return $pum_diesel;
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/fuel_station_request/update/' . Crypt::encrypt($item->id));
                return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>';
            })
            ->rawColumns(['status', 'pum_petrol', 'pum_diesel', 'action', 'alloc_petrol', 'alloc_diesel', 'district', 'station'])
            ->make(true);

        return $data;
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);

        $fuel_request = StationFuelRequest::find($id);

        return view('fuelin.station_fuel_request.update', [
            'fuel_request' => $fuel_request
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $fuel_request = StationFuelRequest::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'schedule_date' => 'required',
                'allocated_petrol' => 'required|numeric|between:0,9999999999999.99|max:' . $fuel_request->stationInfo->petrol,
                'allocated_diesel' => 'required|numeric|between:0,9999999999999.99|max:' . $fuel_request->stationInfo->diesel
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }


        $fuel_request->scheduled_date = $request->schedule_date;
        $fuel_request->alloc_petrol = $request->allocated_petrol;
        $fuel_request->alloc_diesel = $request->allocated_diesel;
        $fuel_request->status = 1;
        $fuel_request->update();

        //Update the Customer Request Schedule
        $customer_request = FuelRequest::where(['district_id' => $fuel_request->district_id, 'station_id' => $fuel_request->station_id, 'status' => 0])->get();

        $av_sch_petrol = $fuel_request->alloc_petrol;
        $av_sch_diesel = $fuel_request->alloc_diesel;

        foreach ($customer_request as $item) {
            if ($item->fuel_type == 1) {
                if ($av_sch_petrol >= $item->fuel_value) {
                    $av_sch_petrol = $av_sch_petrol - $item->fuel_value;

                    $this->updateCustomerRequest($item, $request, $id);
                }
            }

            if ($item->fuel_type == 2) {
                if ($av_sch_diesel >= $item->fuel_value) {
                    $av_sch_diesel = $av_sch_diesel - $item->fuel_value;

                    $this->updateCustomerRequest($item, $request, $id);
                }
            }
        }

        //Email Sending to Fuel Station
        $data["email"] = $fuel_request->stationInfo->email;
        $data["title"] = "Fuel Request Scheduled";
        $data["view"] = "fuelin.station_fuel_request.mail";
        $data["fuel_request"] = $fuel_request;

        Mail::send($data["view"], $data, function ($message) use ($data) {
            $message->to($data["email"])
                ->subject($data["title"]);
        });


        return response()->json(['status' => true, 'message' => 'Selected Fuel Request Updated Successfully']);
    }

    public function updateCustomerRequest($item, $request, $id)
    {
        $fuel_request_customer = FuelRequest::find($item->id);
        $fuel_request_customer->schedule_date = $request->schedule_date;
        $fuel_request_customer->schedule_id = $id;
        $fuel_request_customer->status = 1;
        $fuel_request_customer->update();

        $data_cust["name"] = $fuel_request_customer->customer->first_name . ' ' . $fuel_request_customer->customer->last_name;
        $data_cust["email"] = $fuel_request_customer->customer->email;
        $data_cust["title"] = "Fuel Request Scheduled";
        $data_cust["view"] = "fuelin.station_fuel_request.custmail";
        $data_cust["fuel_request_customer"] = $fuel_request_customer;

        Mail::send($data_cust["view"], $data_cust, function ($message) use ($data_cust) {
            $message->to($data_cust["email"])
                ->subject($data_cust["title"]);
        });

        return true;
    }
}
