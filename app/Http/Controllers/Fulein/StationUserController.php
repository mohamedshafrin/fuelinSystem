<?php

namespace App\Http\Controllers\Fulein;

use App\Models\User;
use App\Models\District;
use App\Models\FuelStation;
use App\Models\StationUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class StationUserController extends Controller
{
    public function index()
    {
        $stations = FuelStation::all();
        $district = District::all();

        return view('fuelin.station_user.index', [
            'stations' => $stations,
            'district' => $district
        ]);
    }

    public function station_user_get(Request $request)
    {
        if (isset($request->district) && !empty($request->district)) {
            $stations = FuelStation::where('district_id', $request->district)->get();

            $station_id = [];

            foreach ($stations as $item) {
                $station_id[] = $item->id;
            }
        }

        $query = StationUser::with(['userInfo', 'stationInfo']);

        if (isset($request->station) && !empty($request->station))
            $query = $query->where('station_id', $request->station);

        if (isset($request->district) && !empty($request->district))
            $query = $query->whereIn('station_id', $station_id);

        if (Auth::user()->hasRole('Manager'))
            $query = $query->where('station_id', Auth::user()->userStation->station_id)->where('user_id', '!=', Auth::user()->id);

        $users = $query->orderBy('id', 'DESC');

        $data =  Datatables::of($users)
            ->addIndexColumn()
            ->addColumn('user_image', function ($item) {
                $image = asset($item->userInfo->image);
                return '<img src="' . $image . '" style="height:45px; width:45px" >';
            })
            ->addColumn('station_name', function ($item) {
                return $item->stationInfo->name;
            })
            ->addColumn('user_name', function ($item) {
                return $item->userInfo->name;
            })
            ->addColumn('user_email', function ($item) {
                return $item->userInfo->email;
            })
            ->addColumn('user_contact', function ($item) {
                return $item->userInfo->contact;
            })
            ->addColumn('status', function ($item) {
                if ($item->status == '0') {
                    return '<span class="badge badge-danger"> Inactive </span>';
                } else {
                    return '<span class="badge badge-success"> Active </span>';
                }
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/station_user/update/' . Crypt::encrypt($item->id));

                if (Auth::user()->hasRole('Admin')) {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>
                <a href="#" class="btn btn-sm btn-danger deletebtn square-btn" onclick="deleteConfirmation(' . $item->id . ')" data-id="' . $item->id . '"><svg id="delete_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_781" data-name="Path 781" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_782" data-name="Path 782" d="M5.653,13.452A1.31,1.31,0,0,0,6.96,14.758h5.226a1.31,1.31,0,0,0,1.306-1.306V6.919a1.31,1.31,0,0,0-1.306-1.306H6.96A1.31,1.31,0,0,0,5.653,6.919Zm7.839-9.8H11.859L11.4,3.189A.659.659,0,0,0,10.938,3H8.207a.659.659,0,0,0-.457.189l-.464.464H5.653a.653.653,0,0,0,0,1.306h7.839a.653.653,0,1,0,0-1.306Z" transform="translate(-1.734 -1.04)" fill="#fff"/></svg></a>';
                } else {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>';
                }
            })
            ->rawColumns(['status', 'user_image', 'action', 'station_name', 'user_name', 'user_email', 'user_contact'])
            ->make(true);

        return $data;
    }

    public function add_form()
    {
        $stations = FuelStation::where('status',1)->get();
        $district = District::all();

        if (Auth::user()->hasRole('Admin')) {
            return view('fuelin.station_user.create', [
                'stations' => $stations,
                'district' => $district
            ]);

        } else {
            $station_user = StationUser::where('user_id',Auth::user()->id)->first();

            return view('fuelin.station_user.create', [
                'stations' => $stations,
                'district' => $district,
                'user' => $station_user
            ]);
        }


    }

    public function get_station(Request $request)
    {
        $id = $request->id;

        $stations = FuelStation::where(['district_id'=> $id, 'status'=>1])->get();

        return response()->json(['stations'=>$stations]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'station' => 'required',
                'district' => 'required',
                'first_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
                'last_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
                'contact_number' => 'required|digits:10|unique:users,contact,NULL,id,deleted_at,NULL',
                'email_address' => 'required|max:50|email:rfc,dns|unique:users,email,NULL,id,deleted_at,NULL',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $password = Str::random(8);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '.$request->last_name;
        $user->email = $request->email_address;
        $user->contact = $request->contact_number;
        $user->password = Hash::make($password);
        $user->image = 'upload/users/user.png';
        $user->status = $request->status == true ? 1 : 0;
        $user->save();

        $user->assignRole('Manager');

        $user_station = new StationUser();
        $user_station->station_id = $request->station;
        $user_station->user_id = $user->id;
        $user_station->save();

        //Email Sending
        $data["email"] = $user->email;
        $data["name"] = $user->name;
        $data["password"] = $password;
        $data["title"] = "Welcome To Fuel In";
        $data["view"] = "fuelin.station_user.welcome";

        Mail::send($data["view"], $data, function($message)use($data) {
            $message->to($data["email"])
                    ->subject($data["title"]);
        });

        //End

        return response()->json(['status'=>true, 'message'=> 'New Station User Created Successfully']);
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);

        $station_user = StationUser::find($id);
        $stations = FuelStation::where(['district_id'=> $station_user->stationInfo->district->id, 'status'=>1])->get();
        $district = District::all();

        return view('fuelin.station_user.update', [
            'stations' => $stations,
            'district' => $district,
            'user' => $station_user
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $station_user = StationUser::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'station' => 'required',
                'district' => 'required',
                'first_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
                'last_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
                'contact_number' => 'required|digits:10|unique:users,contact,'.$station_user->userInfo->id.',id,deleted_at,NULL',
                'email_address' => 'required|max:50|email:rfc,dns|unique:users,email,'.$station_user->userInfo->id.',id,deleted_at,NULL',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $station_user->station_id = $request->station;
        $station_user->update();

        $user = User::find($station_user->user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '.$request->last_name;
        $user->email = $request->email_address;
        $user->contact = $request->contact_number;
        $user->status = $request->status == true ? 1 : 0;
        $user->update();

        return response()->json(['status'=>true, 'message'=> 'Selected Station User Updated Successfully']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $station_user = StationUser::find($id);
        User::destroy($station_user->user_id);
        StationUser::destroy($id);

        return response()->json(['status'=>true, 'message'=> 'Selected Station User Deleted Successfully']);
    }
}
