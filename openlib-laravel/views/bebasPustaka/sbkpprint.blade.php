@extends('layouts/layoutMaster')

@section('title', __('sbkps.title'))

@section('vendor-style')
@endsection

@section('page-style')
    <style>
    </style>
@endsection

@section('content')

    <div class="card">
        <h5 class="card-header">{{ __('sbkps.advanced_search') }}</h5>
        <!--Search Form -->
        <div class="card-body">
            <form class="dt_adv_search">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="bs-datepicker-daterange" class="form-label">{{ __('sbkps.choose_month_year') }}</label>
                                <input type="text" id="bs-datepicker-daterange" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <hr class="mt-0">

        <div class="card-datatable table-responsive pt-0">
            <table class="dt-row-grouping table border-top" id="table">
                <thead>
                    <tr>
                        <th>{{ __('sbkps.letter_number') }}</th>
                        <th>{{ __('config.member.number') }}</th>
                        <th style="width: 15%;">{{ __('config.holiday.input.name') }}</th>
                        <th style="width: 15%;">{{ __('config.notification_format.input.name') }}</th>
                        <th style="width: 15%;">{{ __('sbkps.author') }}</th>
                        <th style="width: 15%;">{{ __('sbkps.created_date') }}</th>
                        <th style="width: 15%;">{{ __('common.action') }}</th>
                    </tr>
                </thead>



            </table>
        </div>
    </div>

    <div class="modal fade" id="frmbox" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('sbkps.form_add') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm" class="form-validate">
                        @csrf
                        <input type="hidden" id="member_id" name="inp[member_id]">
                        <input type="hidden" id="username" name="inp[username]">

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6>{{ __('sbkps.title') }}</h6>
                            </div>

                            <div class="card-body">
                                <div class="form-group row mb-4" id="typeaheadContainer">
                                    <label for="TypeaheadBasic" class="col-md-3 col-form-label">Username</label>
                                    <div class="col-md-9">
                                        <input id="TypeaheadBasic" class="form-control" type="text" autocomplete="off"
                                            placeholder="{{ __('sbkps.find_username') }}..." />
                                        <small class="text-secondary">{{ __('sbkps.type_at_least') }}</small>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">{{ __('config.notification_format.input.name') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="inp[judul]" id="judul"
                                            data-rule-required="true" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">{{ __('sbkps.author') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="inp[pengarang]" id="pengarang"
                                            data-rule-required="true" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="save()">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script>
        let dTable = null;
        let url = '{{ url('bebas-pustaka/sbkpprint') }}';
        let bsDatepickerRange = null;

        var startDate = moment().startOf('month');
        var endDate = moment().endOf('month');

        $(function () {

            dTable = $('#table').DataTable({
                pageLength: 25,
                ajax: {
                    url: url + '/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.startDate = startDate.format('YYYY-MM-DD');
                        d.endDate = endDate.format('YYYY-MM-DD');
                    },
                },
                columns: [
                    { data: 'letter_number', name: 'letter_number', orderable: true, searchable: true },
                    { data: 'member_number', name: 'member_number', orderable: true, searchable: true },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'donated_item_title', name: 'donated_item_title', orderable: true, searchable: true },
                    { data: 'donated_item_author', name: 'donated_item_author', orderable: true, searchable: true },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: true,
                        render: function (data, type, row) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `<button class="btn btn-outline-danger btn-sm" onclick="printLetter(${row.id})">
                                                    <i class="ti ti-printer"></i> Print
                                                </button>`;
                        }
                    }
                ],
            });


            $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('sbkps.title') }} </button>`)

            $('#bs-datepicker-daterange').daterangepicker({
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                showDropdowns: true,
                opens: isRtl ? 'left' : 'right',
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                startDate = start;
                endDate = end;
                dTable.ajax.reload();
            });
        });
        function printLetter(id) {
            let printUrl = "{{ url('bebas-pustaka/sbkpprint/print') }}/" + id;
            window.open(printUrl, '_blank');
        }

        $(document).ready(function () {
            let ajaxRequest;
            $('#TypeaheadBasic').typeahead(
                {
                    hint: false,
                    highlight: true,
                    minLength: 3,

                },
                {
                    name: 'members',
                    source: function (query, syncResults, asyncResults) {
                        // Synchronous suggestions (if any)
                        syncResults([]);

                        // Clear the previous timeout if it exists
                        if (ajaxRequest) {
                            clearTimeout(ajaxRequest);
                        }

                        // Set a new timeout
                        ajaxRequest = setTimeout(function () {
                            // Asynchronous suggestions
                            $.ajax({
                                url: url + '/autodata',
                                type: 'POST',
                                data: { q: query },
                                dataType: 'json',
                                success: function (data) {
                                    asyncResults($.map(data, function (item) {
                                        return item;
                                    }));
                                },
                            });
                        }, 500);
                    },
                    display: function (item) {
                        return item.name;
                    },
                }
            );

            $('#TypeaheadBasic').bind('typeahead:select', function (ev, suggestion) {
                $('#member_id').val(suggestion.id);
                $('#username').val(suggestion.username);
            });

        });

        function add() {
            _reset();
            $('#frmbox').modal('show');
        }

        function save() {

            if ($("#frm").valid()) {
                let formData = new FormData();
                formData.append('member_id', $('#member_id').val());
                formData.append('username', $('#username').val());
                formData.append('judul', $('#judul').val());
                formData.append('pengarang', $('#pengarang').val());

                $.ajax({
                    url: url + '/save',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#frmbox').modal('hide'); // Tutup modal jika berhasil menyimpan
                            dTable.draw();
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                        } else if (data.status === 'error') {
                            // Menampilkan alert ketika ada error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });

                            // Menutup modal walaupun ada error
                            $('#frmbox').modal('hide');
                        }
                    },
                    error: function (xhr, status, error) {
                        // Menangani error pada saat request
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: "{{ __('common.message_error_title') }}"
                        });

                        // Menutup modal walaupun ada error
                        $('#frmbox').modal('hide');
                    }
                });
            } else {
                // Jika form tidak valid, tetap tutup modal
                $('#frmbox').modal('hide');
            }

        }



    </script>

@endsection