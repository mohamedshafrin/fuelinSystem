<?php

namespace App\Http\Controllers\Fulein;

use App\Models\FuelRequest;
use App\Models\StationUser;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\StationFuelRequest;
use App\Http\Controllers\Controller;
use App\Models\FuelStation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class StationFuelRequestController extends Controller
{
    public function index()
    {
        $user = StationUser::where('user_id',Auth::user()->id)->first();

        $station = StationFuelRequest::where(['district_id' => $user->stationInfo->district->id, 'station_id' => $user->station_id])->whereIn('status',[0,1])->get();

        return view('fuelin.request_fuel.index',['station'=>$station]);
    }

    public function request_fuel_get()
    {
        $user = StationUser::where('user_id',Auth::user()->id)->first();

        $station = StationFuelRequest::where(['district_id' => $user->stationInfo->district->id, 'station_id' => $user->station_id])->orderBy('id','DESC');

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
                    return '<span class="badge badge-warning"> Dispatched </span>';
                }

                if ($item->status == '3') {
                    return '<span class="badge badge-success"> Pumbed </span>';
                }
            })
            ->addColumn('pum_petrol', function ($item) {
                $pum_petrol = '';
                if($item->status == 3)
                {
                    $pum_petrol = $item->petrol;
                }

                return $pum_petrol;
            })
            ->addColumn('pum_diesel', function ($item) {
                $pum_diesel = '';
                if($item->status == 3)
                {
                    $pum_diesel = $item->diesel;
                }

                return $pum_diesel;
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/request_fuel/update/'.Crypt::encrypt($item->id));
                if($item->status == 2)
                {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>';
                }
            })
            ->rawColumns(['status','pum_petrol', 'pum_diesel','action'])
            ->make(true);

        return $data;
    }

    public function create(Request $request)
    {
        $user = StationUser::where('user_id',Auth::user()->id)->first();

        $fuel_request = new StationFuelRequest();
        $fuel_request->district_id = $user->stationInfo->district->id;
        $fuel_request->station_id = $user->station_id;
        $fuel_request->request_date = date('Y-m-d');
        $fuel_request->save();

        return response()->json(['status'=>true, 'message'=>'Fuel Request Submittted Successfully']);
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);

        $fuel_request = StationFuelRequest::find($id);

        return view('fuelin.request_fuel.update',[
            'fuel_request'=>$fuel_request
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $fuel_request = StationFuelRequest::find($id);

        $user = StationUser::where('user_id',Auth::user()->id)->first();
        $station = FuelStation::find($user->station_id);
        $quota_petrol = $station->petrol;
        $av_petrol = $station->av_petrol;
        $balance_petrol = $quota_petrol - $av_petrol;

        $pum_av_petrol = $fuel_request->alloc_petrol >= $balance_petrol ? $fuel_request->alloc_petrol : $balance_petrol - $fuel_request->alloc_petrol;

        $quota_diesel = $station->diesel;
        $av_diesel = $station->av_diesel;
        $balance_diesel = $quota_diesel - $av_diesel;

        $pum_av_diesel = $fuel_request->alloc_diesel >= $balance_diesel ? $fuel_request->alloc_diesel : $balance_diesel - $fuel_request->alloc_diesel;

        $validator = Validator::make(
            $request->all(),
            [
                'amount_petrol' => 'required|numeric|between:0,999999999999999.99|max:'.$pum_av_petrol,
                'amount_diesel' => 'required|numeric|between:0,999999999999999.99|max:'.$pum_av_diesel,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }


        $fuel_request->pumbed_date = date('Y-m-d');
        $fuel_request->petrol = $request->amount_petrol;
        $fuel_request->diesel = $request->amount_diesel;
        $fuel_request->status = 3;
        $fuel_request->update();

        $station->av_petrol = $station->av_petrol + $request->amount_petrol;
        $station->av_diesel = $station->av_diesel + $request->amount_diesel;
        $station->update();

        return response()->json(['status'=>true, 'message'=>'Selected Fuel Request Updated Successfully']);
    }
}
