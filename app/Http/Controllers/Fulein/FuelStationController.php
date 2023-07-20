<?php

namespace App\Http\Controllers\Fulein;

use App\Models\District;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Laravel\Ui\Presets\React;

class FuelStationController extends Controller
{
    public function index()
    {
        return view('fuelin.stations.index');
    }

    public function fuel_station_get()
    {
        $station = FuelStation::with(['district'])->orderBy('id','DESC');

        $data =  Datatables::of($station)
            ->addIndexColumn()
            ->addColumn('status', function ($item) {
                if ($item->status == '0') {
                    return '<span class="badge badge-danger"> Inactive </span>';
                } else {
                    return '<span class="badge badge-success"> Active </span>';
                }
            })
            ->addColumn('district_name', function ($item) {
                return $item->district->name;
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/fuel_station/update/'.Crypt::encrypt($item->id));

                if (Auth::user()->hasRole('Admin')) {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>
                    <a href="#" class="btn btn-sm btn-danger deletebtn square-btn" onclick="deleteConfirmation('. $item->id . ')" data-id="'. $item->id . '"><svg id="delete_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_781" data-name="Path 781" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_782" data-name="Path 782" d="M5.653,13.452A1.31,1.31,0,0,0,6.96,14.758h5.226a1.31,1.31,0,0,0,1.306-1.306V6.919a1.31,1.31,0,0,0-1.306-1.306H6.96A1.31,1.31,0,0,0,5.653,6.919Zm7.839-9.8H11.859L11.4,3.189A.659.659,0,0,0,10.938,3H8.207a.659.659,0,0,0-.457.189l-.464.464H5.653a.653.653,0,0,0,0,1.306h7.839a.653.653,0,1,0,0-1.306Z" transform="translate(-1.734 -1.04)" fill="#fff"/></svg></a>';

                } else {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>';
                }

            })
            ->rawColumns(['status', 'district_name','action'])
            ->make(true);

        return $data;
    }

    public function add_form()
    {
        $districts = District::all();

        return view('fuelin.stations.create', ['districts'=>$districts]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'station_name' => ['required','unique:fuel_stations,contact,NULL,id,deleted_at,NULL','regex:/^[a-z A-Z]+$/u','max:50'],
                'district' => 'required',
                'contact_number' => 'required|digits:10|unique:fuel_stations,contact,NULL,id,deleted_at,NULL',
                'email_address' => 'required|email:rfc,dns|unique:fuel_stations,email,NULL,id,deleted_at,NULL',
                'petrol_quota' => 'required|numeric|between:1,999999999.99',
                'diesel_quota' => 'required|numeric|between:1,999999999.99',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $station = new FuelStation();
        $station->name = $request->station_name;
        $station->district_id = $request->district;
        $station->contact = $request->contact_number;
        $station->email = $request->email_address;
        $station->petrol = $request->petrol_quota;
        $station->diesel = $request->diesel_quota;
        $station->status = $request->status == true ? 1 : 0;
        $station->save();

        return response()->json(['status' => true,  'message' => 'New Fuel Station Created Successfully']);
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);

        $station = FuelStation::find($id);
        $districts = District::all();

        return view('fuelin.stations.update', [
            'districts'=>$districts,
            'station' => $station
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'station_name' => ['required','unique:fuel_stations,contact,'.$id.',id,deleted_at,NULL','regex:/^[a-z A-Z]+$/u', 'max:50'],
                'district' => 'required',
                'contact_number' => 'required|digits:10|unique:fuel_stations,contact,'.$id.',id,deleted_at,NULL',
                'email_address' => 'required|email:rfc,dns|unique:fuel_stations,email,'.$id.',id,deleted_at,NULL',
                'petrol_quota' => 'required|numeric|between:1,999999999.99',
                'diesel_quota' => 'required|numeric|between:1,999999999.99',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $station = FuelStation::find($id);
        $station->name = $request->station_name;
        $station->district_id = $request->district;
        $station->contact = $request->contact_number;
        $station->email = $request->email_address;
        $station->petrol = $request->petrol_quota;
        $station->diesel = $request->diesel_quota;
        $station->status = $request->status == true ? 1 : 0;
        $station->update();

        return response()->json(['status' => true,  'message' => 'Selected Fuel Station Updated Successfully']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        FuelStation::destroy($id);
        return response()->json(['status' => true,  'message' => 'Selected Fuel Station deleted Successfully']);
    }
}
