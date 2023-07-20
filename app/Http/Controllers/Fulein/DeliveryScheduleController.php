<?php

namespace App\Http\Controllers\Fulein;

use App\Models\District;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use App\Models\StationFuelRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class DeliveryScheduleController extends Controller
{
    public function index()
    {
        $stations = FuelStation::all();
        $district = District::all();

        return view('fuelin.schedule.index', [
            'stations' => $stations,
            'district' => $district
        ]);
    }

    public function schedules_get(Request $request)
    {
        $query = StationFuelRequest::with(['district','stationInfo']);

        if (isset($request->station) && !empty($request->station))
            $query = $query->where('station_id', $request->station);

        if (isset($request->district) && !empty($request->district))
            $query = $query->where('district_id', $request->district);

        $station = $query->where('status',1)->orderBy('id','DESC');

        $data =  Datatables::of($station)
            ->addIndexColumn()
            ->addColumn('district', function ($item) {

                return $item->district->name;
            })
            ->addColumn('station', function ($item) {

                return $item->stationInfo->name;
            })
            ->addColumn('alloc_petrol', function ($item) {

                return $item->alloc_petrol;
            })
            ->addColumn('alloc_diesel', function ($item) {

                return $item->alloc_diesel;
            })
            ->addColumn('pum_petrol', function ($item) {
                $pum_petrol = '';
                if($item->status == 2)
                {
                    $pum_petrol = $item->petrol;
                }

                return $pum_petrol;
            })
            ->addColumn('pum_diesel', function ($item) {
                $pum_diesel = '';
                if($item->status == 2)
                {
                    $pum_diesel = $item->diesel;
                }

                return $pum_diesel;
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/fuel_station_request/update/'.Crypt::encrypt($item->id));
                return '<button type="button" class="btn btn-sm btn-primary deletebtn square-btn" onclick="deleteConfirmation('. $item->id . ')" data-id="'. $item->id . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></a>';
            })
            ->rawColumns(['status','pum_petrol', 'pum_diesel','action','alloc_petrol','alloc_diesel','district','station'])
            ->make(true);

        return $data;
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $fuel_request = StationFuelRequest::find($id);
        $fuel_request->status = 2;
        $fuel_request->update();


        //Email Sending
        $data["email"] = $fuel_request->stationInfo->email;
        $data["title"] = "Fuel Request Dispatched";
        $data["view"] = "fuelin.schedule.mail";
        $data["fuel_request"] = $fuel_request;

        Mail::send($data["view"], $data, function($message)use($data) {
            $message->to($data["email"])
                    ->subject($data["title"]);
        });


        return response()->json(['status'=>true, 'message'=>'Selected Scheduled Updated Successfully']);
    }
}
