@extends('layouts/layoutMaster')

@section('title', __('sbkps.kelengkapan_title'))

@section('vendor-style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-style')
    <style>
        @page {
            size: A4;
            /* Bisa 'A4 landscape' jika perlu lebih lebar */
            margin: 0;
            /* Margin 0 agar konten lebih lega */
        }

        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
            }

            /* Sembunyikan semua elemen, kecuali yang di-allow */
            body * {
                visibility: hidden;
            }

            /* Elemen yang ingin dicetak */
            #print-header,
            #print-header *,
            #status-list,
            #status-list * {
                visibility: visible;
            }

            /* Hindari terpotong di tengah */
            #print-header,
            #status-list,
            .card {
                page-break-inside: avoid !important;
                break-inside: avoid;
            }

            .card {
                border: none !important;
            }

            #print-header {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>

@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('sbkps.kelengkapan_title') }}</h4>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                @if(in_array($arId, [2, 13, 15, 99]))
                    <p class="text-muted mb-0">{{ __('sbkps.status_kelengkapan') }}</p>
                @else
                    <p class="text-muted mb-0">{{ __('sbkps.status_displayed') }}</p>
                @endif

                <button id="btnPrint" class="btn btn-danger" style="display: none;">
                    <i class="ti ti-printer me-1"></i> Print
                </button>
            </div>

            @if(!in_array($arId, [2, 13, 15, 99]))
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="TypeaheadBasic" class="form-label">
                        {{ __('sbkps.find') }}
                        </label>
                        <input id="TypeaheadBasic" class="form-control typeahead" type="text" autocomplete="off"
                            placeholder="{{ __('sbkps.type_here') }}..." />
                    </div>
                </div>

                <div class="row mb-4" id="member-info" style="display: none;">
                    <div class="col-md-6">
                        <label for="selected-member" class="form-label">{{ __('sbkps.chosen_member') }}</label>
                        <input type="text" id="selected-member" class="form-control" readonly>
                    </div>
                </div>
            @endif


            <div class="table-responsive text-nowrap">
                <div id="print-header" class="d-none d-print-block mb-4">
                    <div class="text-center">
                        <h4 class="mb-1 fw-bold">Telkom University - Open Library</h4>
                        <p class="mb-0">{{ __('sbkps.status_requirement') }}</p>
                        <hr>
                        <p class="mb-0">
                        {{ __('sbkps.name') }}: <span id="print-fullname"></span> |
                        {{ __('sbkps.nim') }}: <span id="print-nim"></span>
                        </p>
                    </div>
                </div>

                <table class="table table-hover">
                    <thead class="border-bottom">
                        <tr>
                            <th class="text-start">{{ __('sbkps.list_requirement') }}</th>
                            <th class="text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="status-list">
                        <tr id="status-placeholder">
                            <td colspan="2" class="text-center">
                                @if(in_array($arId, [2, 13, 15, 99]))
                                {{ __('sbkps.view_here') }}.
                                @else
                                {{ __('sbkps.choose_view_status') }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script>
        console.log("arId dari server:", "{{ $arId }}");
        console.log("session('user'):", @json(session('user')));
        $('#btnPrint').on('click', function () {
            window.print();
        });
        $(document).ready(function () {
            let arId = "{{ $arId }}";
            let url = "{{ url('bebas-pustaka/kelengkapan-sbkp') }}";
            let userName = "{{ auth()->user()->master_data_user ?? '' }}";

            function renderStatus(statusData) {
                console.log('Response getStatus:', statusData);
                var statusList = $('#status-list');
                statusList.empty();

                if (!statusData || statusData.length === 0) {
                    const message = '{{ __("sbkps.not_found") }}'; 
                    statusList.append(`<li class="text-center">${message}</li>`);
                     return;
                }

                var status = statusData[0];
                var items = [{
                    label: '{{ __("sbkps.no_borrowing") }}',
                    value: status.peminjaman,
                    icon: 'ti-book'
                },
                {
                    label: '{{ __("sbkps.no_fine") }}',
                    value: status.lunas,
                    icon: 'ti-wallet'
                },
                {
                    label: '{{ __("sbkps.document_uploaded") }}',
                    value: status.dokumen,
                    icon: 'ti-file'
                },
                {
                    label: '{{ __("sbkps.pembimbing_approve") }}',
                    value: status.approval,
                    icon: 'ti-check'
                },
                {
                    label: '{{ __("sbkps.book_donated") }}',
                    value: status.buku,
                    icon: 'ti-gift'
                },
                ];

                items.forEach(function (item) {
                    var badgeClass = item.value ? 'bg-label-success' : 'bg-label-danger';
                    var badgeText = item.value ? '{{ __("sbkps.completed") }}' : '{{ __("sbkps.incomplete") }}';

                    statusList.append(`
                                                                                                                      <tr>
                                                                                                                        <td>
                                                                                                                          <div class="d-flex align-items-center">
                                                                                                                            <div class="avatar flex-shrink-0 me-3">
                                                                                                                              <span class="avatar-initial rounded ${badgeClass}">
                                                                                                                                <i class="ti ${item.icon} ti-md"></i>
                                                                                                                              </span>
                                                                                                                            </div>
                                                                                                                            <div>
                                                                                                                              <p class="mb-0 fw-medium">${item.label}</p>
                                                                                                                            </div>
                                                                                                                          </div>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                          <span class="badge ${badgeClass}">${badgeText}</span>
                                                                                                                        </td>
                                                                                                                      </tr>
                                                                                                                    `);
                });
            }

            if ([2, 13, 15, 99].includes(parseInt(arId))) {
                let userFullname = "{{ auth()->user()->master_data_fullname ?? '' }}";
                let userNim = "{{ auth()->user()->master_data_number ?? '' }}";

                $('#print-fullname').text(userFullname);
                $('#print-nim').text(userNim);
                console.log("User ar_id =", arId, " => Tampilkan status otomatis.");
                if (userName) {
                    $.ajax({
                        url: url + '/get-status',
                        type: 'POST',
                        data: {
                            username: userName
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (statusData) {
                            renderStatus(statusData);
                            if (statusData && statusData.length > 0) {
                                $('#btnPrint').show();
                            }
                        }
                    });
                } else {
                    console.warn("Tidak ada username di auth()->user()");
                }
                return;
            }

            let ajaxRequest;
            $('#TypeaheadBasic').typeahead({
                hint: false,
                highlight: true,
                minLength: 3,
            }, {
                name: 'members',
                source: function (query, syncResults, asyncResults) {
                    syncResults([]);
                    if (ajaxRequest) clearTimeout(ajaxRequest);
                    ajaxRequest = setTimeout(function () {
                        $.ajax({
                            url: url + '/autodata',
                            type: 'POST',
                            data: {
                                q: query
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                asyncResults(data);
                            },
                        });
                    }, 500);
                },
                display: function (item) {
                    return item.name;
                },
            }).on('typeahead:select', function (event, item) {
                $('#selected-member').val(item.username);
                $('#member-info').show();
                $('#print-fullname').text(item.fullname);
                $('#print-nim').text(item.nim);
                $.ajax({
                    url: url + '/get-status',
                    type: 'POST',
                    data: {
                        username: item.username
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (statusData) {
                        renderStatus(statusData);
                        if (statusData && statusData.length > 0) {
                            $('#btnPrint').show();
                        }
                    }
                });
            });
        });
    </script>
@endsection