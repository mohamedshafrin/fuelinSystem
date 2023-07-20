@extends('layouts.admin')

@section('title')
    Fuel Stations
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="col-lg-6 col-6">
            <div class="form-group mb-4">
                <a href="{{ url('fuelin/fuel_station') }}"
                    class="btn btn-success btn-max-200 text-uppercase font-weight-bold" style="width: 200px"><i
                        class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center">
                        <h3 class="p-4 font-weight-bold text-uppercase">Create New Fuel Station </h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 col-12 mt-5 ">
                        <div class="row">
                            <input type="hidden" name="id" value="{{$station->id}}">
                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Station Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="station_name" class="form-control"
                                        value="{{ $station->name }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_station_name"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">District</label>
                                <select name="district" class="form-control disabled-results district">
                                    <option value=""></option>
                                    @foreach ($districts as $item)
                                        <option value="{{ $item->id }}" {{$station->district_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger font-weight-bold error_district"></span>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Contact Number<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="contact_number" maxlength="10" class="form-control"
                                        value="{{ $station->contact }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_contact_number"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Email Address<span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email_address" class="form-control"
                                        value="{{ $station->email }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_email_address"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Allocate Petrol Quota<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="petrol_quota" class="form-control"
                                        value="{{ $station->petrol }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_petrol_quota"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Allocate Diesel Quota<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="diesel_quota" class="form-control"
                                        value="{{ $station->diesel }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_diesel_quota"></span>
                                </div>
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="formGroupExampleInput2">Active/Inactive</label>
                                <div>
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input type="checkbox" name="status" {{$station->status == 1 ? 'checked' : ''}} >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit"
                                        class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px">Update</button>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="disable_button" style="display: none">
                                <div class="form-group text-center text-sm-right">
                                    <button type="button" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px"><i class="fas fa-spinner fa-spin"></i> Updating ...</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#submitForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#submitForm')[0]);

                $.ajax({
                    type: "POST",
                    beforeSend: function() {
                        $('#submit_button').css('display', 'none');
                        $('#disable_button').css('display', 'block');
                    },
                    url: "{{ url('/fuelin/fuel_station/update') }}",
                    data: formData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $('#submit_button').css('display', 'block');
                        $('#disable_button').css('display', 'none');
                        clearError();
                        if (response.statuscode == 400) {
                            $.each(response.errors, function(key, item) {
                                if (key) {
                                    $('.error_' + key).text(item);
                                } else {
                                    $('.error_' + key).text('');
                                }
                            });
                        } else {
                            $.confirm({
                                theme: 'modern',
                                columnClass: 'col-md-6 col-12 col-md-offset-4',
                                title: 'Success! ',
                                content: response.message,
                                type: 'green',
                                buttons: {
                                    confirm: {
                                        text: 'OK',
                                        btnClass: 'btn-150',
                                        action: function() {
                                            location.href = "{{ url('/fuelin/fuel_station') }}";
                                        }
                                    },
                                }
                            });
                        }
                    }
                });
            });

            function clearError()
            {
                $('.error_station_name').text('');
                $('.error_district').text('');
                $('.error_contact_number').text('');
                $('.error_email_address').text('');
            }

        });
    </script>
@endsection
