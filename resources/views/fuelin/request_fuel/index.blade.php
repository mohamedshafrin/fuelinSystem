@extends('layouts.admin')

@section('title')
    Request Fuel
@endsection

@section('content')

        @if (!count($station))
            <div class="col-lg-12 col-12  layout-spacing">
                <button type="button" id="request_button" class="btn btn-theme float-right text-uppercase">
                    <i class="fa fa-plus"></i> Request Fuel
                </button>
            </div>
        @endif

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
                                <th>Requested Date</th>
                                <th>Scheduled Date</th>
                                <th>Pumbed Date</th>
                                <th>Pumbed Petrol</th>
                                <th>Pumbed Diesel </th>
                                <th>Status</th>
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

            $(function() {
                $('#data_table').DataTable({
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
                    ajax: '{!! url('/fuelin/request_fuel_get') !!}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'request_date',
                            name: 'request_date'
                        },
                        {
                            data: 'scheduled_date',
                            name: 'scheduled_date'
                        },
                        {
                            data: 'pumbed_date',
                            name: 'pumbed_date',
                        },
                        {
                            data: 'pum_petrol',
                            name: 'pum_petrol',
                            searchable: false
                        },
                        {
                            data: 'pum_diesel',
                            name: 'pum_diesel',
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
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
            });

            $('#request_button').click(function (e) {
                e.preventDefault();

                $.confirm({
                    theme: 'modern',
                    columnClass: 'col-md-6 col-md-offset-4',
                    icon: 'fa fa-info-circle text-danger',
                    title: 'Are you Sure!',
                    content: 'Do you want to Make the Fuel Request?',
                    type: 'red',
                    autoClose: 'cancel|10000',
                    buttons: {
                        confirm: {
                            text: 'Yes',
                            btnClass: 'btn-150',
                            action: function() {
                                $.ajax({
                                    type: "POST",
                                    url: "{{ url('/fuelin/request_fuel_get/create') }}",
                                    dataType: 'JSON',
                                    success: function(response) {
                                        location.reload();
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
                content: 'Do you want to Delete the Selected Fuel Station?',
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
                                url: "{{ url('/fuelin/fuel_station/delete') }}",
                                data: data,
                                success: function(response) {
                                    location.href = "{{ url('/fuelin/fuel_station') }}";
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
