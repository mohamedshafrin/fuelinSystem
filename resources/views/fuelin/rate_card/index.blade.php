@extends('layouts.admin')

@section('title')
    Rate Card
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">

        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center">
                        <h3 class="p-4 font-weight-bold text-uppercase">Update the Rate Card </h3>
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
                                    <label for="exampleFormControlInput2">Petrol Amount (Per liter)<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="amount_petrol" class="form-control"
                                        value="{{ $rate_card->amount_petrol }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_amount_petrol"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Diesel Amount (Per liter)<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="amount_diesel" class="form-control"
                                        value="{{ $rate_card->amount_diesel }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_amount_diesel"></span>
                                </div>
                            </div>

                            @foreach ($rate_card_veh as $item)
                                <div class="col-lg-6 col-12">
                                    <div class="form-group mb-4">
                                        <label for="exampleFormControlInput2">Allocate Week Quota for
                                            {{ $item['type'] }} (l)<span class="text-danger">*</span></label>
                                        <input type="text" name="{{ $item['type_name'] }}" class="form-control"
                                            value="{{ (int)$item['amount'] }}" id="exampleFormControlInput2">

                                        <span class="text-danger font-weight-bold error_{{ $item['type_name'] }}"></span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
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
                    url: "{{ url('/fuelin/rate_card/update') }}",
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
                                                "{{ url('/fuelin/rate_card') }}";
                                        }
                                    },
                                }
                            });
                        }
                    }
                });
            });

            function clearError() {
                $('.error_amount_petrol').text('');
                $('.error_amount_diesel').text('');
                $('.error_motor_cycles').text('');
                $('.error_three_wheelers').text('');
                $('.error_passenger_cars').text('');
                $('.error_tractors_and_engines').text('');
                $('.error_lorries').text('');
                $('.error_dual_purpose_vehicles').text('');
                $('.error_buses').text('');
                $('.error_ambulances_and_hearses').text('');
                $('.error_quadricycle').text('');
            }

        });
    </script>
@endsection
