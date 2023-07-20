@extends('layouts.admin')

@section('title')
    Report
@endsection


@section('content')
    <div class="col-lg-12 col-12 layout-spacing">


        <div class="statbox widget box box-shadow">

            <div class="widget-content widget-content-area">
                <form method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 col-12 mt-5 ">
                        <div class="row">

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Select Fuel Stations<span
                                            class="text-danger">*</span></label>
                                    <select name="fuel_stations" class="form-control text-center station disabled-results">
                                        <option value="0" selected>All Station</option>
                                        @foreach ($stations as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger font-weight-bold error_station"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Select Start Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control text-center start_date"
                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_start_date"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlInput2">Select End Date<span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control text-center end_date"
                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" id="exampleFormControlInput2">

                                    <span class="text-danger font-weight-bold error_end_date"></span>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="submit_button">
                                <div class="form-group text-center text-sm-right">
                                    <button type="submit" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px">Generate</button>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 mb-5" id="disable_button" style="display: none">
                                <div class="form-group text-center text-sm-right">
                                    <button type="button" class="btn btn-theme btn-max-200 text-uppercase font-weight-bold"
                                        style="width: 200px"><i class="fas fa-spinner fa-spin"></i> Generating ...</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-12 col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">
                            pumbed fuel for stations
                        </h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartStationPumbedFuel" class=""></div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">
                            Sales Revenue
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 row pt-4 pb-4">

                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mt-1"></div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12 mt-1">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
                            <div class="infobox-3">
                                <h5 class="info-heading text-uppercase" style="font-size: 20px !important">Petrol</h5>
                                <h5 class="info-count" id="petrol_total"></h5>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12  ">
                            <div class="infobox-3">
                                <h5 class="info-heading text-uppercase" style="font-size: 20px !important">Diesel</h5>
                                <h5 class="info-count" id="diesel_total"></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mt-1"></div>

                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mt-1"></div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-uppercase" style="font-size: 20px !important">Revenue</h5>
                        <h5 class="info-count" id="total_revenue"></h5>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mt-1"></div>

            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-6 col-lg-6 col-6 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Station Fuel
                            Request</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartStationRequest" class=""></div>
            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-6 col-lg-6 col-6 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Customer Fuel
                            Request</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartCustomerRequest" class=""></div>
            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-12 col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">
                            Customer Fuel Request
                        </h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartCustomerFuelRequest" class=""></div>
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

            loadReport();

            function loadReport() {
                var data = {
                    'station': $('.station').val(),
                    'start_date': $('.start_date').val(),
                    'end_date': $('.end_date').val()
                }

                $.ajax({
                    type: "POST",
                    url: "{{ url('/fuelin/get_admin_report') }}",
                    data: data,
                    dataType: "JSON",
                    success: function(response) {
                        console.log(response);

                        //Pumbed Station Fuels
                        var station_fules = {
                            chart: {
                                height: 350,
                                type: 'bar',
                                toolbar: {
                                    show: false,
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%'
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            series: [{
                                name: 'Petrol',
                                data: response.pumb_stations_value.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.pumb_stations_value.diesel
                            }],
                            xaxis: {
                                categories: response.pumb_stations_value.stations,
                            },
                            colors: ['#6a040f', '#073b4c'],
                            yaxis: {
                                title: {
                                    text: 'Litre'
                                }
                            },
                            fill: {
                                opacity: 1

                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + " Litre"
                                    }
                                }
                            }
                        }

                        var chartStationPumbedFuel = new ApexCharts(
                            document.querySelector("#chartStationPumbedFuel"),
                            station_fules
                        );

                        chartStationPumbedFuel.render();

                        chartStationPumbedFuel.updateOptions({
                            xaxis: {
                                categories: response.pumb_stations_value.stations
                            },
                            series: [{
                                name: 'Petrol',
                                data: response.pumb_stations_value.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.pumb_stations_value.diesel
                            }],
                        });
                        //END--------------------------------------

                        //----- Sales Revenue -----------------------
                        $('#petrol_total').text(response.pumb_stations_value.petrol_total);
                        $('#diesel_total').text(response.pumb_stations_value.diesel_total);
                        $('#total_revenue').text(response.pumb_stations_value.total_revenue);
                        //------------ end -------------------------------

                        //Station Request Donut Chart
                        var chartStationRequest = {
                            chart: {
                                height: 350,
                                type: 'donut',
                                toolbar: {
                                    show: false,
                                }
                            },
                            series: response.station_request,
                            labels:['Pending','Accepted','Dispatched','Pumbed'],
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        }

                        var stationRequest = new ApexCharts(
                            document.querySelector("#chartStationRequest"),
                            chartStationRequest
                        );

                        stationRequest.render();
                        //End --------------------------------

                        // ------------ Customer Request Pie Chart ---
                        var chartCustomerRequest = {
                            chart: {
                                height: 350,
                                type: 'donut',
                                toolbar: {
                                    show: false,
                                }
                            },
                            series: response.customer_request,
                            labels:['Pending','Scheduled','Pumbed'],
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        }

                        var customerRequest = new ApexCharts(
                            document.querySelector("#chartCustomerRequest"),
                            chartCustomerRequest
                        );

                        customerRequest.render();
                        //------------ END ----------------------------------

                        //Customer Fuel Request
                        var chartCustomerFuelRequest = {
                            chart: {
                                height: 350,
                                type: 'bar',
                                toolbar: {
                                    show: false,
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%'
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            series: [{
                                name: 'Petrol',
                                data: response.customer_pumbed_value.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.customer_pumbed_value.diesel
                            }],
                            xaxis: {
                                categories: response.customer_pumbed_value.stations,
                            },
                            colors: ['#6a040f', '#073b4c'],
                            yaxis: {
                                title: {
                                    text: 'Litre'
                                }
                            },
                            fill: {
                                opacity: 1

                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + " Litre"
                                    }
                                }
                            }
                        }

                        var CustomerFuelRequest = new ApexCharts(
                            document.querySelector("#chartCustomerFuelRequest"),
                            chartCustomerFuelRequest
                        );

                        CustomerFuelRequest.render();

                        CustomerFuelRequest.updateOptions({
                            xaxis: {
                                categories: response.customer_pumbed_value.stations
                            },
                            series: [{
                                name: 'Petrol',
                                data: response.customer_pumbed_value.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.customer_pumbed_value.diesel
                            }],
                        });
                        //END--------------------------------------
                    }
                });
            }

            $('#submitForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#submitForm')[0]);

                $.ajax({
                    type: "POST",
                    beforeSend: function() {
                        $('#submit_button').css('display', 'none');
                        $('#disable_button').css('display', 'block');
                    },
                    url: "{{ url('/fuelin/report_validation') }}",
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
                            loadReport();
                        }
                    }
                });
            });

            function clearError() {
                $('.error_station').text('');
                $('.error_start_date').text('');
                $('.error_end_date').text('');
            }
        });
    </script>
@endsection
