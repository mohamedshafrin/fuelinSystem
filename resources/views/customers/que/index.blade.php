@extends('layouts.customer')

@section('title')
    Que Request
@endsection


@section('content')

<div class="col-lg-12 col-12  layout-spacing">
    <a href="{{ url('customer/que_request/join') }}" class="btn btn-theme float-right text-uppercase">
        <i class="fa fa-plus"></i> Join New Que
    </a>
</div>

<div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12 ">
                    <h3 class="font-weight-bold pt-2 pb-2 text-uppercase">Que Request</h3>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area br-6 m-2">
            <table id="data_table" class="table table-striped" style="width:100%">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Token</th>
                        <th>District</th>
                        <th>Station</th>
                        <th>Vehicle Number </th>
                        <th>Requested Date</th>
                        <th>Scheduled Date</th>
                        <th>Rquested Fuel (L)</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid Status</th>
                        <th></th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @if (session('success'))
        <script>
            $(document).ready(function() {
                $.confirm({
                    theme: 'modern',
                    columnClass: 'col-md-6 col-12 col-md-offset-4',
                    title: 'Success! ',
                    content: '{{ session('success') }}',
                    type: 'green',
                    buttons: {
                        confirm: {
                            text: 'OK',
                            btnClass: 'btn-150',
                            action: function() {
                                ;
                            }
                        },
                    }
                });
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
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
                    ajax: '{!! url('/customer/que_request_get') !!}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'token',
                            name: 'token'
                        },
                        {
                            data: 'district',
                            name: 'district'
                        },
                        {
                            data: 'station',
                            name: 'station',
                        },
                        {
                            data: 'vehicle',
                            name: 'vehicle',
                            searchable: false
                        },
                        {
                            data: 'request_date',
                            name: 'request_date',
                            searchable: false
                        },
                        {
                            data: 'schedule_date',
                            name: 'schedule_date',
                            searchable: false
                        },
                        {
                            data: 'fuel_value',
                            name: 'fuel_value',
                            searchable: false
                        },
                        {
                            data: 'fuel_price',
                            name: 'fuel_price',
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            searchable: false
                        },
                        {
                            data: 'paid_status',
                            name: 'paid_status',
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        }
                    ]
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
            content: 'Do you want to Delete the Selected Que Request?',
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
                            url: "{{ url('/customer/que_request/delete') }}",
                            data: data,
                            success: function(response) {
                                location.href = "{{ url('/customer/que_request') }}";
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
