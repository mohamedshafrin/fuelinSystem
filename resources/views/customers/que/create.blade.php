@extends('layouts.customer')

@section('title')
    Que Request
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="col-lg-6 col-6">
            <div class="form-group mb-4">
                <a href="{{ url('customer/que_request') }}"
                    class="btn btn-success btn-max-200 text-uppercase font-weight-bold" style="width: 200px"><i
                        class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center">
                        <h3 class="p-4 font-weight-bold text-uppercase">Join New Fuel Que </h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 col-12 mt-5 ">
                        <div class="row">

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">Select Your Vehicle</label>
                                <select name="vehicle" class="form-control disabled-results vehicle">
                                    <option value=""></option>
                                    @foreach ($vehicles as $item)
                                        <option value="{{ $item->id }}">{{ $item->vehicle_no }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="fuel_type" class="fuel_type" value="">
                                <span class="text-danger font-weight-bold error_vehicle"></span>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">Requesting Fuel Amount (Liter)</label>
                                <input type="text" name="fuel_amount" readonly class="form-control fuel_amount">

                                <span class="text-danger font-weight-bold error_fuel_amount"></span>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">District</label>
                                <select name="district" class="form-control disabled-results district">
                                    <option value=""></option>
                                    @foreach ($district as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger font-weight-bold error_district"></span>
                            </div>

                            <div class="col-lg-6 col-12 form-group">
                                <label for="">Fuel Station</label>
                                <select name="station" class="form-control disabled-results station">
                                    <option value=""></option>
                                </select>

                                <span class="text-danger font-weight-bold error_station"></span>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px">Join</button>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="disable_button" style="display: none">
                                <div class="form-group text-center text-sm-right">
                                    <button type="button" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px"><i class="fas fa-spinner fa-spin"></i> Joining ...</button>
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
                    url: "{{ url('/customer/que_request/join') }}",
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
                                                "{{ url('/customer/que_request') }}";
                                        }
                                    },
                                }
                            });
                        }
                    }
                });
            });

            function clearError() {
                $('.error_vehicle').text('');
                $('.error_fuel_amount').text('');
                $('.error_district').text('');
                $('.error_station').text('');
            }

            $('.vehicle').change(function(e) {
                e.preventDefault();
                var id = $(this).val();

                var data = {
                    'id': id
                }

                if (id == '') {
                    $('.fuel_amount').val('');
                    $('.fuel_amount').attr('readonly', true);
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/customer/que_request/av_fuel') }}",
                        data: data,
                        dataType: "JSON",
                        success: function(response) {
                            console.log(response);
                            $('.fuel_amount').val('');
                            if (response.status == false) {
                                $('#submit_button').css('display', 'none');
                                $('.error_fuel_amount').text(
                                    'Sorry You do not have available fuel quota. Please try next week'
                                    );
                                $.confirm({
                                    theme: 'modern',
                                    columnClass: 'col-md-6 col-12 col-md-offset-4',
                                    title: 'Failed! ',
                                    content: 'Sorry You do not have available fuel quota. Please try next week',
                                    type: 'red',
                                    buttons: {
                                        confirm: {
                                            text: 'OK',
                                            btnClass: 'btn-150',
                                            action: function() {
                                                location.href =
                                                    "{{ url('/customer/que_request') }}";
                                            }
                                        },
                                    }
                                });
                            } else {
                                $('#submit_button').css('display', 'block');
                                $('.fuel_amount').val(response.av_fuel);
                                $('.fuel_amount').attr('readonly', false);
                                $('.fuel_type').val(response.type);
                            }
                        }
                    });
                }
            });

            $('.fuel_amount').keyup(function (e) {
                e.preventDefault();

                var fuel_amount = $(this).val();
                var vehicle_id = $('.vehicle').val();


                var data = {
                    'fuel_amount': fuel_amount,
                    'vehicle_id' : vehicle_id
                }

                if (fuel_amount == '') {
                    $('.error_fuel_amount').text('Fuel amount field is required');
                } else {
                    $('.error_fuel_amount').text('');

                    $.ajax({
                        type: "POST",
                        url: "{{url('/customer/que_request/av_fuel_check')}}",
                        data: data,
                        dataType: "JSON",
                        success: function (response) {
                            if (response.statuscode == 400) {
                                $.each(response.errors, function(key, item) {
                                    if (key) {
                                        $('.error_' + key).text(item);
                                    } else {
                                        $('.error_' + key).text('');
                                    }
                                });
                            } else {
                                if (response.status == false) {
                                    $('#submit_button').css('display', 'none');
                                    $('.error_fuel_amount').text('Fuel Amount can not be grater than '+response.av_fuel);
                                } else {
                                    $('#submit_button').css('display', 'block');
                                    $('.error_fuel_amount').text('');
                                }
                            }
                        }
                    });
                }
            });

            $('.district').change(function(e) {
                e.preventDefault();

                var district = $(this).val();
                var fuel_amount = $('.fuel_amount').val();
                var fuel_type = $('.fuel_type').val();
                var vehicle = $('.vehicle').val();

                var data = {
                    'district': district,
                    'fuel_amount' : fuel_amount,
                    'fuel_type' : fuel_type,
                    'vehicle' : vehicle
                }

                $.ajax({
                    type: "POST",
                    url: "{{ url('/customer/que_request/get_station') }}",
                    data: data,
                    dataType: "JSON",
                    success: function(response) {
                        $('.error_fuel_amount').text('');
                        $('.error_vehicle').text('');

                        if (response.statuscode == 400) {
                            $.each(response.errors, function(key, item) {
                                if (key) {
                                    $('.error_' + key).text(item);
                                }
                            });
                        }
                        else
                        {
                            $('.station').html('');
                            $('.station').append('<option value=""></option>');
                            $.each(response.stations, function(key, item) {
                                $('.station').append('<option value="' + item.id + '">' +
                                    item.name + '</option>');
                            });
                        }
                    }
                });

            });

        });
    </script>
@endsection
