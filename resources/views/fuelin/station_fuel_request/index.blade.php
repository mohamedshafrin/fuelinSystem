@extends('layouts.admin')

@section('title')
    Request Fuel
@endsection

@section('content')
    <div class="col-lg-12 col-12  layout-spacing">
        <form action="">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-12 form-group">
                    <label for="">District</label>
                    <select name="district" class="form-control disabled-results district">
                        <option value=""></option>
                        @foreach ($district as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-6 col-md-6 col-12 form-group">
                    <label for="">Fuel Station</label>
                    <select name="station" class="form-control disabled-results station">
                        <option value=""></option>
                        @foreach ($stations as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-12 col-12 form-group">
                    <button type="button" class="btn btn-primary float-right ml-5 btn_filter"
                        style="width: 150px">Filter</button>
                    <button type="button" class="btn btn-dark float-right btn_reset" style="width: 150px">Reset</button>
                </div>

            </div>
        </form>
    </div>

    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 ">
                        <h3 class="font-weight-bold pt-2 pb-2 text-uppercase">Fuel Request</h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area br-6 m-2">
                <table id="data_table" class="table table-striped" style="width:100%">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>District</th>
                            <th>Station</th>
                            <th>Requested Date</th>
                            <th>Petrol Quota (Liter)</th>
                            <th>Diesel Quota (Liter) </th>
                            <th class="no-content">Actions</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadData();
            var table;

            function loadData()
            {
                table = $('#data_table').DataTable({
                    "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                        "<'table-responsive'tr>" +
                        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                    "oLanguage": {
                        "oPaginate": {
                            "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                            "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                        },
                        "sInfo": "Showing page _PAGE_ of _PAGES_",
                        "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                        "sSearchPlaceholder": "Search...",
                        "sLengthMenu": "Results :  _MENU_",
                    },
                    "stripeClasses": [],
                    "lengthMenu": [10, 20, 50],
                    "pageLength": 10,

                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('/fuelin/fuel_station_request_get') }}",
                        data: function (d) {
                                d.station = $('.station').val(),
                                d.district = $('.district').val()
                            }
                        },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'district',
                            name: 'district'
                        },
                        {
                            data: 'station',
                            name: 'station'
                        },
                        {
                            data: 'request_date',
                            name: 'request_date'
                        },
                        {
                            data: 'alloc_petrol',
                            name: 'alloc_petrol',
                            searchable: false
                        },
                        {
                            data: 'alloc_diesel',
                            name: 'alloc_diesel',
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            $('.btn_filter').click(function (e) {
                e.preventDefault();

                //reload the table
                table.clear();
                table.ajax.reload();
                table.draw();
            });

            $('.btn_reset').click(function (e) {
                e.preventDefault();

                $('.station').val('').change();
                $('.district').val('').change();
                //reload the table
                table.clear();
                table.ajax.reload();
                table.draw();
            });

            $('.district').change(function (e) {
                e.preventDefault();

                var id = $(this).val();

                var data = {
                    'id' : id
                }

                $.ajax({
                    type: "POST",
                    url: "{{url('/fuelin/station_user/get_station')}}",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        $('.station').html('');
                        $('.station').append('<option value=""></option>');
                        $.each(response.stations, function (key, item) {
                            $('.station').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    }
                });

            });

        });

    </script>

    <script>
        function deleteConfirmation(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.confirm({
                theme: 'modern',
                columnClass: 'col-md-6 col-md-offset-4',
                icon: 'fa fa-info-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Fuel Station User?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-150',
                        action: function() {
                            var data = {
                                "_token": $('input[name=_token]').val(),
                                "id": id,
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ url('/fuelin/station_user/delete') }}",
                                data: data,
                                success: function(response) {
                                    location.href = "{{ url('/fuelin/station_user') }}";
                                }
                            });
                        }
                    },

                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-150-danger',
                        action: function() {

                        }
                    },
                }
            });
        }
    </script>
@endsection
