<?php

namespace App\Http\Controllers\Fulein;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\CustomerVehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        return view('fuelin.customer.index');
    }

    public function customers_get()
    {
        $customers = Customer::all();

        $data =  Datatables::of($customers)
            ->addIndexColumn()
            ->addColumn('status', function ($item) {
                if ($item->status == '0') {
                    return '<span class="badge badge-danger"> Inactive </span>';
                } else {
                    return '<span class="badge badge-success"> Active </span>';
                }
            })
            ->addColumn('name', function ($item) {
                return $item->first_name .' '. $item->last_name;
            })
            ->addColumn('action', function ($item) {
                $editurl = url('fuelin/customers/update/'.Crypt::encrypt($item->id));
                $vehicle = url('fuelin/customers/vehicles/'.Crypt::encrypt($item->id));

                if (Auth::user()->hasRole('Admin')) {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>
                    <a href="' . $vehicle . '" class="btn btn-sm btn-success editbtn square-btn" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M5.275 18.35v1.4q0 .25-.187.425-.188.175-.463.175H4.25q-.275 0-.462-.175Q3.6 20 3.6 19.75V12.1l2.075-5.85q.1-.275.35-.438.25-.162.55-.162H17.5q.275 0 .5.162.225.163.325.438L20.4 12.1v7.65q0 .25-.187.425-.188.175-.463.175h-.375q-.275 0-.462-.175-.188-.175-.188-.425v-1.4Zm.1-7.5h13.25L17.2 6.9H6.8ZM4.85 12.1v5Zm2.525 3.7q.5 0 .863-.362.362-.363.362-.863t-.375-.863q-.375-.362-.85-.362-.5 0-.862.375-.363.375-.363.85 0 .5.363.863.362.362.862.362Zm9.25 0q.5 0 .863-.362.362-.363.362-.863t-.362-.863q-.363-.362-.863-.362t-.863.375q-.362.375-.362.85 0 .5.375.863.375.362.85.362ZM4.85 17.1h14.3v-5H4.85Z" fill="#fff"/></svg></a>
                    <a href="#" class="btn btn-sm btn-danger deletebtn square-btn" onclick="deleteConfirmation('. $item->id . ')" data-id="'. $item->id . '"><svg id="delete_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_781" data-name="Path 781" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_782" data-name="Path 782" d="M5.653,13.452A1.31,1.31,0,0,0,6.96,14.758h5.226a1.31,1.31,0,0,0,1.306-1.306V6.919a1.31,1.31,0,0,0-1.306-1.306H6.96A1.31,1.31,0,0,0,5.653,6.919Zm7.839-9.8H11.859L11.4,3.189A.659.659,0,0,0,10.938,3H8.207a.659.659,0,0,0-.457.189l-.464.464H5.653a.653.653,0,0,0,0,1.306h7.839a.653.653,0,1,0,0-1.306Z" transform="translate(-1.734 -1.04)" fill="#fff"/></svg></a>';

                } else {
                    return '<a href="' . $editurl . '" class="btn btn-sm btn-primary editbtn square-btn" title="Edit"><svg id="edit_black_24dp" xmlns="http://www.w3.org/2000/svg" width="15.678" height="15.678" viewBox="0 0 15.678 15.678"><path id="Path_783" data-name="Path 783" d="M0,0H15.678V15.678H0Z" fill="none"/><path id="Path_784" data-name="Path 784" d="M3,12.445v1.986a.323.323,0,0,0,.327.327H5.312a.306.306,0,0,0,.229-.1l7.133-7.127-2.45-2.45L3.1,12.21a.321.321,0,0,0-.1.235ZM14.569,5.638a.651.651,0,0,0,0-.921L13.04,3.189a.651.651,0,0,0-.921,0l-1.2,1.2,2.45,2.45,1.2-1.2Z" transform="translate(-1.04 -1.039)" fill="#fff"/></svg></a>
                    <a href="' . $vehicle . '" class="btn btn-sm btn-success editbtn square-btn" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M5.275 18.35v1.4q0 .25-.187.425-.188.175-.463.175H4.25q-.275 0-.462-.175Q3.6 20 3.6 19.75V12.1l2.075-5.85q.1-.275.35-.438.25-.162.55-.162H17.5q.275 0 .5.162.225.163.325.438L20.4 12.1v7.65q0 .25-.187.425-.188.175-.463.175h-.375q-.275 0-.462-.175-.188-.175-.188-.425v-1.4Zm.1-7.5h13.25L17.2 6.9H6.8ZM4.85 12.1v5Zm2.525 3.7q.5 0 .863-.362.362-.363.362-.863t-.375-.863q-.375-.362-.85-.362-.5 0-.862.375-.363.375-.363.85 0 .5.363.863.362.362.862.362Zm9.25 0q.5 0 .863-.362.362-.363.362-.863t-.362-.863q-.363-.362-.863-.362t-.863.375q-.362.375-.362.85 0 .5.375.863.375.362.85.362ZM4.85 17.1h14.3v-5H4.85Z" fill="#fff"/></svg></a>';
                }

            })
            ->rawColumns(['status', 'name','action'])
            ->make(true);

        return $data;
    }

    public function update_form($id)
    {
        $id = Crypt::decrypt($id);

        $customers = Customer::find($id);

        return view('fuelin.customer.update',['customers'=>$customers]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z]+$/u',
                'last_name' => 'required|regex:/^[a-z A-Z]+$/u',
                'contact_number' => 'required|unique:customers,contact,'.$id.',id,deleted_at,NULL',
                'email_address' => 'required|email|unique:customers,email,'.$id.',id,deleted_at,NULL',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statuscode' => 400, 'errors' => $validator->errors()]);
        }

        $customers = Customer::find($id);
        $customers->first_name = $request->first_name;
        $customers->last_name = $request->last_name;
        $customers->email = $request->email_address;
        $customers->contact = $request->contact_number;
        $customers->status = $request->status == true ? 1 : 0;
        $customers->update();

        return response()->json(['status'=>true, 'message'=> 'Selected Customer Updated Successfully']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        CustomerVehicle::where('customer_id',$id)->delete();
        Customer::destroy($id);

        return response()->json(['status'=>true, 'message'=> 'Selected Customer Delete Successfully']);
    }
}
