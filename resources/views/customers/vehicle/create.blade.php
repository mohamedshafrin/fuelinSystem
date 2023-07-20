@extends('layouts.customer')

@section('title')
    Vechicle
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="col-lg-6 col-6">
            <div class="form-group mb-4">
                <a href="{{ url('customer') }}"
                    class="btn btn-success btn-max-200 text-uppercase font-weight-bold" style="width: 200px"><i
                        class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center">
                        <h3 class="p-4 font-weight-bold text-uppercase">Add New Vehicle </h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 col-12 mt-5 ">
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Vehicle Number<span
                                            class="text-danger">*</span> <small class="text-primary">(ABC-1111)</small></label>
                                    <input type="text" name="vehicle_number" class="form-control"
                                        value="{{ old('vehicle_number') }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_vehicle_number"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">Vehicle Type</label>
                                <select name="vehicle_type" class="form-control disabled-results vehicle_type">
                                    <option value=""></option>
                                    @foreach ($vehicle_type as $item)
                                        <option value="{{ $item->id }}">{{ $item->type }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger font-weight-bold error_vehicle_type"></span>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">Fuel Type</label>
                                <select name="fuel_type" class="form-control disabled-results fuel_type">
                                    <option value=""></option>
                                    <option value="1">Petrol</option>
                                    <option value="2">Diesel</option>
                                </select>

                                <span class="text-danger font-weight-bold error_fuel_type"></span>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit"
                                        class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px">Save</button>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="disable_button" style="display: none">
                                <div class="form-group text-center text-sm-right">
                                    <button type="button" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px"><i class="fas fa-spinner fa-spin"></i> Saving ...</button>
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
                    url: "{{ url('/customer/addvehicles') }}",
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
                                            location.href = "{{ url('/customer') }}";
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
                $('.error_vehicle_number').text('');
                $('.error_vehicle_type').text('');
                $('.error_fuel_type').text('');
            }

        });
    </script>
@endsection
