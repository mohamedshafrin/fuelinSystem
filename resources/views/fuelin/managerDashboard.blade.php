@extends('layouts.admin')

@section('title')
    Dashboard
@endsection


@section('content')
    @if (Auth::user()->hasRole('Manager'))
        @php
            $quota_petrol = $station->petrol;
            $balance_petrol = $station->av_petrol;
            $percentage_petrol = ($balance_petrol / $quota_petrol) * 100;

            $quota_diesel = $station->diesel;
            $balance_diesel = $station->av_diesel;

            $percentage_diesel = ($balance_diesel / $quota_diesel) * 100;
        @endphp
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget-four">
                <div class="widget-heading">
                    <h3 class="text-center font-weight-bold text-uppercase">Available Fuels</h3>
                </div>
                <div class="widget-content">
                    <div class="vistorsBrowser">
                        <div class="browser-list">
                            <div class="w-browser-details">
                                <div class="w-browser-info">
                                    <h6>Petrol ({{ $quota_petrol }} Liter)</h6>
                                    <p class="browser-count">{{ $balance_petrol }} Liter</p>
                                </div>
                                <div class="w-browser-stats">
                                    <div class="progress">
                                        <div class="progress-bar bg-gradient-primary" role="progressbar"
                                            style="width: {{ $percentage_petrol }}%" aria-valuenow="90" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="browser-list">
                            <div class="w-browser-details">

                                <div class="w-browser-info">
                                    <h6>Diesel ({{ $quota_diesel }} Liter)</h6>
                                    <p class="browser-count">{{ $balance_diesel }} Liter</p>
                                </div>

                                <div class="w-browser-stats">
                                    <div class="progress">
                                        <div class="progress-bar bg-gradient-danger" role="progressbar"
                                            style="width: {{ $percentage_diesel }}%" aria-valuenow="65" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endif


    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Monthly Data -
                            {{ date('Y') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 row pb-4">

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1 ">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">No.of Customers</h5>
                        <h5 class="info-count">{{ $customers_count }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">No.of Pending Fuel Request</h5>
                        <h5 class="info-count">{{ $pending_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">No.of Completed Fuel Request</h5>
                        <h5 class="info-count">{{ $completed_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">No.of Customer Pending Fuel Request</h5>
                        <h5 class="info-count">{{ $pending_customer_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">No.of Customer Completed Fuel Request</h5>
                        <h5 class="info-count">{{ $completed_customer_request }}</h5>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mt-1">
                    <div class="infobox-3">
                        <h5 class="info-heading text-center">Total Pumbed Fuel Amount</h5>
                        <h5 class="info-count">{{ $total_fuel_amount }} L</h5>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-6 col-lg-6 col-6 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Monthly Customer
                            Fuel Request -
                            {{ date('Y F') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget widget-chart-two">
                <div class="widget-content">
                    <div id="customerFuelCount" class=""></div>
                </div>
            </div>
        </div>
    </div>

    <div id="chartDonut" class="col-xl-6 col-lg-6 col-6 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="text-center font-weight-bold text-uppercase" style="font-size: 24px">Monthly
                            Fuel Income -
                            {{ date('Y F') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget widget-chart-two">
                <div class="widget-content">
                    <div id="customerFuelAmount" class=""></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            customerFuelCount();
            customerFuelAmount();

            function customerFuelCount() {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/fuelin/manger/monthly_fuel') }}",
                    dataType: "JSON",
                    success: function(response) {

                        var options = {
                            chart: {
                                type: 'donut',
                                width: 380
                            },
                            colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
                            dataLabels: {
                                enabled: false
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center',
                                fontSize: '14px',
                                markers: {
                                    width: 10,
                                    height: 10,
                                },
                                itemMargin: {
                                    horizontal: 0,
                                    vertical: 8
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '65%',
                                        background: 'transparent',
                                        labels: {
                                            show: true,
                                            name: {
                                                show: true,
                                                fontSize: '29px',
                                                fontFamily: 'Nunito, sans-serif',
                                                color: undefined,
                                                offsetY: -10
                                            },
                                            value: {
                                                show: true,
                                                fontSize: '26px',
                                                fontFamily: 'Nunito, sans-serif',
                                                color: '20',
                                                offsetY: 16,
                                                formatter: function(val) {
                                                    return val
                                                }
                                            },
                                            total: {
                                                show: true,
                                                showAlways: true,
                                                label: 'Total',
                                                color: '#888ea8',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce(
                                                        function(a, b) {
                                                            return a + b
                                                        }, 0)
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            stroke: {
                                show: true,
                                width: 25,
                            },
                            series: response.data_count,
                            labels: ['Petrol', 'Diesel'],
                            responsive: [{
                                breakpoint: 1599,
                                options: {
                                    chart: {
                                        width: '350px',
                                        height: '400px'
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                },

                                breakpoint: 1439,
                                options: {
                                    chart: {
                                        width: '250px',
                                        height: '390px'
                                    },
                                    legend: {
                                        position: 'bottom'
                                    },
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                size: '65%',
                                            }
                                        }
                                    }
                                },
                            }]
                        }

                        var chart = new ApexCharts(
                            document.querySelector("#customerFuelCount"),
                            options
                        );

                        chart.render();
                    }
                });
            }

            function customerFuelAmount() {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/fuelin/manger/monthly_fuel') }}",
                    dataType: "JSON",
                    success: function(response) {

                        var options = {
                            chart: {
                                type: 'donut',
                                width: 380
                            },
                            colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
                            dataLabels: {
                                enabled: false
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center',
                                fontSize: '14px',
                                markers: {
                                    width: 10,
                                    height: 10,
                                },
                                itemMargin: {
                                    horizontal: 0,
                                    vertical: 8
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '65%',
                                        background: 'transparent',
                                        labels: {
                                            show: true,
                                            name: {
                                                show: true,
                                                fontSize: '29px',
                                                fontFamily: 'Nunito, sans-serif',
                                                color: undefined,
                                                offsetY: -10
                                            },
                                            value: {
                                                show: true,
                                                fontSize: '26px',
                                                fontFamily: 'Nunito, sans-serif',
                                                color: '20',
                                                offsetY: 16,
                                                formatter: function(val) {
                                                    return val
                                                }
                                            },
                                            total: {
                                                show: true,
                                                showAlways: true,
                                                label: 'Total',
                                                color: '#888ea8',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce(
                                                        function(a, b) {
                                                            return a + b
                                                        }, 0)
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            stroke: {
                                show: true,
                                width: 25,
                            },
                            series: response.data_amount,
                            labels: ['Petrol', 'Diesel'],
                            responsive: [{
                                breakpoint: 1599,
                                options: {
                                    chart: {
                                        width: '350px',
                                        height: '400px'
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                },

                                breakpoint: 1439,
                                options: {
                                    chart: {
                                        width: '250px',
                                        height: '390px'
                                    },
                                    legend: {
                                        position: 'bottom'
                                    },
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                size: '65%',
                                            }
                                        }
                                    }
                                },
                            }]
                        }

                        var chart = new ApexCharts(
                            document.querySelector("#customerFuelAmount"),
                            options
                        );

                        chart.render();
                    }
                });
            }
        });
    </script>
@endsection
