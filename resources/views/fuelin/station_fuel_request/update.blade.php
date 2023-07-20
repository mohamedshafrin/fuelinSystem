@extends('layouts.admin')

@section('title')
    Request Fuel
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">

        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center">
                        <h3 class="p-4 font-weight-bold text-uppercase">Update the Requested Fuel </h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 col-12 mt-5 ">
                        <div class="row">
                            <input type="hidden" name="id" value="{{$fuel_request->id}}">

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Schedule Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="schedule_date" class="form-control"
                                        min="{{date('Y-m-d',strtotime('+2days', strtotime(date('Y-m-d'))))}}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_schedule_date"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Allocated Petrol<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="allocated_petrol" value="{{$fuel_request->stationInfo->petrol}}" class="form-control"
                                        id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_allocated_petrol"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Allocated Diesel<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="allocated_diesel" value="{{$fuel_request->stationInfo->diesel}}" class="form-control"
                                        id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_allocated_diesel"></span>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px">Update to Schedule</button>
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
                    url: "{{ url('/fuelin/fuel_station_request/update') }}",
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
                                            location.href =
                                                "{{ url('/fuelin/fuel_station_request') }}";
                                        }
                                    },
                                }
                            });
                        }
                    }
                });
            });

            function clearError() {
                $('.error_schedule_date').text('');
                $('.error_amount_diesel').text('');
            }

        });
    </script>
@endsection
