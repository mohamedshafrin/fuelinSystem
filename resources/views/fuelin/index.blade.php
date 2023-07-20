@extends('layouts.admin')

@section('title')
    Dashboard
@endsection


@section('content')
    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">

            <div class="col-lg-12 row pt-4 pb-4">

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading">No.of Fuel Stations</h5>
                        <h5 class="info-count">{{ $station_count }}</h5>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading">No.of Customers</h5>
                        <h5 class="info-count">{{ $customers_count }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1 ">
                    <div class="infobox-3">
                        <h5 class="info-heading">No.of Pending Fuel Request</h5>
                        <h5 class="info-count">{{ $pending_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading">No.of Scheduled Fuel Request</h5>
                        <h5 class="info-count">{{ $scheduled_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading">No.of Dispatched Fuel Request</h5>
                        <h5 class="info-count">{{ $dispatched_request }}</h5>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-12 col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Monthly Fuel Pumbed
                            Value Station wise -
                            {{ date('Y F') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartPumbedFuel" class=""></div>
            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-12 col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Monthly Customer Fuel Request Station Wise -
                            {{ date('Y F') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div id="chartCustomerFuel" class=""></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            chartPumbedFuel();
            chartCustomerRequest();

            function chartPumbedFuel() {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/fuelin/admin/monthly_fuel') }}",
                    dataType: "JSON",
                    success: function(response) {

                        var sCol = {
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
                                data: response.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.diesel
                            }],
                            xaxis: {
                                categories: response.stations,
                            },
                            colors:['#6a040f','#073b4c'],
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

                        var chart = new ApexCharts(
                            document.querySelector("#chartPumbedFuel"),
                            sCol
                        );

                        chart.render();
                    }
                });
            }

            function chartCustomerRequest() {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/fuelin/admin/monthly_cust_fuel') }}",
                    dataType: "JSON",
                    success: function(response) {

                        var sCol = {
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
                                data: response.petrol,
                            }, {
                                name: 'Diesel',
                                data: response.diesel
                            }],
                            xaxis: {
                                categories: response.stations,
                            },
                            colors:['#6a040f','#073b4c'],
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

                        var chart = new ApexCharts(
                            document.querySelector("#chartCustomerFuel"),
                            sCol
                        );

                        chart.render();
                    }
                });
            }
        });
    </script>
@endsection
