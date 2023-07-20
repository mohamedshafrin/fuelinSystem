<?php

namespace App\Http\Controllers\Fulein;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Models\CustomerVehicle;
use App\Models\RateVehicleCard;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CustomerVehicleController extends Controller
{
    public function index($id)
    {
        $id = Crypt::decrypt($id);

        return view('fuelin.cus_vehicle.index',['id'=>$id]);
    }

    public function vehicles_get($id)
    {
        $vehicles = CustomerVehicle::where('customer_id', $id);

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
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/customers/vehicles/update/'.Crypt::encrypt($item->id));

                if (Auth::user()->hasRole('Admin')) {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>
                    <a href="#" class="btn btn-sm btn-danger deletebtn square-btn" onclick="deleteConfirmation('. $item->id . ')" data-id="'. $item->id . '"><svg id="delete_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_781" data-name="Path 781" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_782" data-name="Path 782" d="M5.653,13.452A1.31,1.31,0,0,0,6.96,14.758h5.226a1.31,1.31,0,0,0,1.306-1.306V6.919a1.31,1.31,0,0,0-1.306-1.306H6.96A1.31,1.31,0,0,0,5.653,6.919Zm7.839-9.8H11.859L11.4,3.189A.659.659,0,0,0,10.938,3H8.207a.659.659,0,0,0-.457.189l-.464.464H5.653a.653.653,0,0,0,0,1.306h7.839a.653.653,0,1,0,0-1.306Z" transform="translate(-1.734 -1.04)" fill="#fff"/></svg></a>';

                } else {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>';
                }

            })
            ->rawColumns(['vehicle_type', 'fuel_type','action'])
            ->make(true);

        return $data;
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);
        $vehicle_type = VehicleType::all();
        $vehicles = CustomerVehicle::find($id);

        return view('fuelin.cus_vehicle.update',[
            'vehicle_type'=>$vehicle_type,
            'vehicles' => $vehicles
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_number' => 'required|unique:customer_vehicles,vehicle_no,'.$id.',id,deleted_at,NULL',
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

        $vehicle = CustomerVehicle::find($id);
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_no = $request->vehicle_number;
        $vehicle->fuel_type = $request->fuel_type;
        $vehicle->quota = $allocated_quota->amount;
        $vehicle->update();

        return response()->json(['status' => true,  'message' => 'Selected Vehicle Updated Successfully']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        CustomerVehicle::destroy($id);
        return response()->json(['status' => true,  'message' => 'Selected Vehicle Deleted Successfully']);
    }
}
