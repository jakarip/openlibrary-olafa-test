@extends('layouts/layoutMaster')

@section('title', __('catalogs.return.web_title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('page-style')
    <style>
        .search-result-item {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">{{ __('catalogs.circulation') }} /</span> {{ __('catalogs.return.title') }}</h4>

        <!-- Member Search Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('catalogs.member') }}</h5>
                <button id="btnChangeMember" class="btn btn-secondary btn-sm d-none">
                    <i class="ti ti-refresh me-1"></i> {{ __('catalogs.change_member') }}
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">{{ __('catalogs.member_search') }}</label>
                            <div class="input-group">
                                <input id="memberSearch" type="text" class="form-control"
                                    placeholder="{{ __('sbkps.name') }} / {{ __('sbkps.nim') }} / Username" autofocus>
                                <button id="btnSearchMember" class="btn btn-primary" type="button">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                            <div id="memberResults" class="list-group mt-1"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="memberInfo" class="d-none">
                            <h6 class="mb-2">{{ __('catalogs.member_information') }}</h6>
                            <p class="mb-1"><strong>{{ __('sbkps.name') }}:</strong> <span id="memberName"></span></p>
                            <p class="mb-1"><strong>{{ __('sbkps.nim') }}:</strong> <span id="memberNumber"></span></p>
                            <p class="mb-1"><strong>{{ __('catalogs.type') }}:</strong> <span id="memberType"></span></p>
                            <p class="mb-0"><strong>{{ __('catalogs.borrowing.active') }}:</strong> <span id="activeLoans"></span> {{ __('catalogs.book') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Input Section -->
        <div id="returnSection" class="card mb-4 d-none">
            <div class="card-header">
                <h5 class="mb-0">{{ __('catalogs.book_return') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('catalogs.barcode') }}</label>
                        <div class="input-group">
                            <input id="barcodeInput" type="text" class="form-control"
                                placeholder="{{ __('catalogs.return.input_barcode') }}">
                            <button id="btnCheckItem" class="btn btn-primary" type="button">
                                <i class="ti ti-search me-1"></i> {{ __('catalogs.return_cek') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Return Form (initially hidden) -->
                <div id="returnFormSection" class="d-none">
                    <hr>
                    <h6 class="mb-3">{{ __('catalogs.return.details') }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.book.title') }}</label>
                                <input type="text" class="form-control" id="returnTitle" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Barcode</label>
                                <input type="text" class="form-control" id="returnBarcode" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.borrowing.date') }}</label>
                                <input type="text" class="form-control" id="returnRentDate" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.due_date') }}</label>
                                <input type="text" class="form-control" id="returnDueDate" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.return.status') }}</label>
                                <select id="returnStatus" class="form-select">
                                    <option value="2">{{ __('catalogs.returned') }}</option>
                                    <option value="3">{{ __('catalogs.damaged') }}</option>
                                    <option value="4">{{ __('catalogs.lost') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.return.overdue') }}</label>
                                <input type="text" class="form-control" id="returnLateDays" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.return.fine_day') }}</label>
                                <input type="text" class="form-control" id="returnPenaltyPerDay" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('catalogs.total_fine') }}</label>
                                <input type="text" class="form-control" id="returnPenaltyTotal" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info d-none" id="damageInfo">
                        <i class="ti ti-info-circle me-1"></i>
                        {{ __('catalogs.return.with_status') }} <strong>{{ __('catalogs.damaged') }}</strong> {{ __('catalogs.return.fine_50') }}
                    </div>
                    <div class="alert alert-warning d-none" id="lostInfo">
                        <i class="ti ti-alert-triangle me-1"></i>
                        {{ __('catalogs.return.with_status') }} <strong>{{ __('catalogs.lost') }}</strong> {{ __('catalogs.return.fine_100') }}
                    </div>
                    <div class="text-end mt-3">
                        <button id="btnCancelReturn" class="btn btn-secondary me-2">
                            <i class="ti ti-x me-1"></i> {{ __('common.cancel') }}
                        </button>
                        <button id="btnProcessReturn" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> {{ __('catalogs.return.process') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Loans Section -->
        <div id="currentLoansSection" class="card mb-4 d-none">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('catalogs.borrowing.active') }}</h5>
                <select id="statusFilter" class="form-select w-auto">
                    <option value="active" selected>{{ __('catalogs.not_return') }}</option>
                    <option value="returned">{{ __('catalogs.return') }}</option>
                    <option value="all">{{ __('common.all') }}</option>
                </select>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="loansTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('catalogs.title') }}</th>
                                <th>Barcode</th>
                                <th>{{ __('catalogs.borrowing.date') }}</th>
                                <th>{{ __('catalogs.due_date') }}</th>
                                <th>{{ __('catalogs.extend_from') }}</th>
                                <th>{{ __('catalogs.extend_to') }}</th>
                                <th>{{ __('config.holiday.page.title') }}</th>
                                <th>{{ __('catalogs.overdue') }}</th>
                                <th>{{ __('catalogs.total_fine') }}</th>
                                <th>{{ __('common.active') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Current loans will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9" class="text-end">{{ __('catalogs.total_fine') }}</th>
                                <th colspan="2" id="totalPenalty">Rp 0</th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-end">{{ __('catalogs.total_paid') }}</th>
                                <th colspan="2" id="totalPaid">Rp 0</th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-end">{{ __('catalogs.remaining_fine') }}</th>
                                <th colspan="2" id="outstandingPenalty">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div id="paymentSection" class="card mb-4 d-none">
            <div class="card-header">
                <h5 class="mb-0">{{ __('catalogs.fine_payment') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('catalogs.return.payment_amount') }} (Rp)</label>
                            <input type="number" id="paymentAmount" class="form-control" min="1">
                        </div>
                        <!-- <div class="mb-3">
                                                                        <label class="form-label">Catatan</label>
                                                                        <textarea id="paymentNote" class="form-control" rows="3"></textarea>
                                                                    </div> -->
                        <button id="btnProcessPayment" class="btn btn-primary">
                            <i class="ti ti-coin me-1"></i> {{ __('catalogs.payment_process') }}
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">{{ __('catalogs.fine_information') }}</h6>
                            <p class="mb-1">{{ __('catalogs.total_fine') }}: <strong id="paymentTotalPenalty">Rp 0</strong></p>
                            <p class="mb-1">{{ __('catalogs.total_paid') }}: <strong id="paymentTotalPaid">Rp 0</strong></p>
                            <p class="mb-0">{{ __('catalogs.remaining_fine') }}: <strong id="paymentOutstandingPenalty">Rp 0</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            console.log("Document ready loaded for return page");

            // Auto-focus on member search field
            $('#memberSearch').focus();

            // Global variables
            let selectedMember = null;
            let selectedRent = null;
            let loansTable = null;

            // Member search handling
            $('#memberSearch').on('keyup', function (e) {
                console.log("Keyup event triggered on memberSearch");
                if (e.key === 'Enter') {
                    searchMember();
                }
            });

            $('#btnSearchMember').on('click', function () {
                console.log("Search button clicked");
                searchMember();
            });

            function searchMember() {
                console.log("searchMember function called");
                const query = $('#memberSearch').val().trim();
                if (query.length < 3) {
                    Swal.fire({
                        title: '{{ __("catalogs.input_short") }}',
                        text: '{{ __("catalogs.minimum_member") }}',
                        icon: 'warning'
                    });
                    return;
                }

                console.log("Making AJAX request for:", query);

                $.ajax({
                    url: '/catalog/circulation/return/auto-data',
                    method: 'GET',
                    data: { q: query },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log("Search results received:", data);

                        // Store last results globally for access
                        window.lastSearchResults = data.items;

                        // Jika hanya ada satu hasil yang cocok, langsung pilih
                        if (data.single_match) {
                            selectMember(data.member);
                            return;
                        }

                        // Jika tidak ada hasil atau banyak hasil, tampilkan daftar
                        let html = '';
                        if (data.items.length === 0) {
                            html = '<div class="list-group-item">{{ __("catalogs.not_found") }}</div>';
                        } else {
                            data.items.forEach(item => {
                                html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                        <div>
                                                                                                                                            <div><strong>${item.fullname}</strong></div>
                                                                                                                                            <div class="text-muted">${item.nim} - ${item.username}</div>
                                                                                                                                        </div>
                                                                                                                                        <div class="d-flex gap-2 align-items-center">
                                                                                                                                            <span class="badge bg-primary">${item.active_loans} Pinjaman</span>
                                                                                                                                            <button class="btn btn-sm btn-primary btn-select-member" data-id="${item.id}">
                                                                                                                                                <i class="ti ti-user-check"></i> Pilih
                                                                                                                                            </button>
                                                                                                                                        </div>
                                                                                                                                    </div>`;
                            });
                        }
                        $('#memberResults').html(html).show();
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: '{{ __("catalogs.search_failed") }}',
                            icon: 'error'
                        });
                    }
                });
            }

            // Select member function
            function selectMember(member) {
                console.log("selectMember called with member:", member);
                $('#btnChangeMember').removeClass('d-none');
                $.ajax({
                    url: '/catalog/circulation/return/member/info',
                    method: 'GET',
                    data: { member_id: member.id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log("Member info response:", response);

                        if (response.success) {
                            selectedMember = response.member;

                            // Update UI
                            $('#memberName').text(selectedMember.name);
                            $('#memberNumber').text(selectedMember.number);
                            $('#memberType').text(selectedMember.type);
                            $('#activeLoans').text(selectedMember.active_loans);

                            // Update search field with the selected member
                            $('#memberSearch').val(`${member.username} - ${member.nim} - ${member.fullname}`);

                            // Show member info and sections
                            $('#memberInfo').removeClass('d-none');
                            $('#returnSection').removeClass('d-none');
                            $('#currentLoansSection').removeClass('d-none');

                            // Show payment section if there are penalties
                            if (selectedMember.total_penalty > 0) {
                                $('#paymentSection').removeClass('d-none');
                                updatePaymentInfo(selectedMember.total_penalty);
                            } else {
                                $('#paymentSection').addClass('d-none');
                            }

                            $('#memberResults').hide();

                            // Focus on barcode input
                            $('#barcodeInput').focus();

                            // Initialize or refresh loans table
                            initLoansTable(member.id);

                            // Display penalty alert if any
                            if (selectedMember.outstanding_penalty > 0) {
                                Swal.fire({
                                    title: '{{ __("catalogs.fine_information") }}',
                                    text: `{{ __('catalogs.denda_sebesar') }} Rp. ${selectedMember.outstanding_penalty.toLocaleString('id-ID')}`,
                                    icon: 'info'
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: '{{ __("catalogs.member_not_found") }}',
                            icon: 'error'
                        });
                    }
                });
            }

            // Change member button handler
            $('#btnChangeMember').on('click', function () {
                console.log("Change member button clicked");
                Swal.fire({
                    title: '{{ __("catalogs.change_member") }}?',
                    text: '{{ __("catalogs.return_canceled") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.yes_change_member") }}',
                    cancelButtonText: '{{ __("common.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetToMemberSearch();
                    }
                });
            });

            // Event handler for manually selecting a member from the list
            $(document).on('click', '.btn-select-member', function () {
                const memberId = $(this).data('id');
                console.log("Select member button clicked for ID:", memberId);

                // Find the member data from the current results
                if (window.lastSearchResults) {
                    const memberItem = window.lastSearchResults.find(item => item.id === memberId);
                    if (memberItem) {
                        selectMember(memberItem);
                        return;
                    }
                }

                // Fallback if data not found in window.lastSearchResults
                $.ajax({
                    url: '/catalog/circulation/return/member/info',
                    method: 'GET',
                    data: { member_id: memberId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            selectMember({
                                id: memberId,
                                username: response.member.username || '',
                                nim: response.member.number || '',
                                fullname: response.member.name || ''
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        Swal.fire({
                            title: 'Error',
                            text: '{{ __("catalogs.member_not_found") }}',
                            icon: 'error'
                        });
                    }
                });
            });

            // Initialize loans table function
            // Find the initLoansTable function and update it like this:
            function initLoansTable(memberId) {
                console.log('Initializing loans table for member ID:', memberId);

                if (loansTable) {
                    loansTable.destroy();
                }

                // Show loading message immediately
                $('#loansTable tbody').html('<tr><td colspan="11" class="text-center">Loading data, please wait...</td></tr>');

                loansTable = $('#loansTable').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 10,      // Start with fewer rows for faster initial load
                    lengthMenu: [5, 10, 25, 50],
                    ajax: {
                        url: '/catalog/circulation/return/history', // Use the optimized endpoint
                        method: 'GET',
                        timeout: 60000,  // Increase timeout to 60 seconds
                        data: function (d) {
                            d.member_id = memberId;
                            d.status = $('#statusFilter').val();
                        },
                        error: function (xhr, error, thrown) {
                            console.error("DataTables AJAX error:", error);
                            $('#loansTable tbody').html('<tr><td colspan="11" class="text-center text-danger">' +
                                '{{ __("common.message_error_title") }} <button class="btn btn-sm btn-primary retry-load">{{ __("catalogs.try_again") }}</button>' +
                                '</td></tr>');
                        }
                    },
                    columns: [
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        { data: 'title' },
                        { data: 'barcode' },
                        { data: 'rent_date' },
                        { data: 'due_date' },
                        {
                            data: 'extended_from',
                            render: function (data) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'extended_to',
                            render: function (data) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'holiday_day',
                            className: 'text-end',
                            render: function (data) {
                                return data || 0;
                            }
                        },
                        {
                            data: 'penalty_day',
                            className: 'text-end',
                            render: function (data) {
                                return data || 0;
                            }
                        },
                        {
                            data: 'penalty_total',
                            className: 'text-end',
                            render: function (data) {
                                return 'Rp. ' + (parseInt(data) || 0).toLocaleString('id-ID');
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                if (!row.return_date) {
                                    return `<button class="btn btn-sm btn-primary btn-return-item" data-barcode="${row.barcode}">
                                                                                                                                    <i class="ti ti-arrow-back-up"></i> {{ __('catalogs.return.kembalikan') }}
                                                                                                                                </button>`;
                                }
                                return '-';
                            }
                        }
                    ],
                    footerCallback: function (row, data, start, end, display) {
                        try {
                            const api = this.api();
                            const json = api.ajax.json();

                            if (json && json.footer) {
                                $('#totalPenalty').text('Rp. ' + json.footer.grand_penalty.toLocaleString('id-ID'));
                                $('#totalPaid').text('Rp. ' + json.footer.total_paid.toLocaleString('id-ID'));
                                $('#outstandingPenalty').text('Rp. ' + json.footer.outstanding.toLocaleString('id-ID'));

                                // Update payment section if visible
                                if (!$('#paymentSection').hasClass('d-none')) {
                                    $('#paymentTotalPenalty').text('Rp. ' + json.footer.grand_penalty.toLocaleString('id-ID'));
                                    $('#paymentTotalPaid').text('Rp. ' + json.footer.total_paid.toLocaleString('id-ID'));
                                    $('#paymentOutstandingPenalty').text('Rp. ' + json.footer.outstanding.toLocaleString('id-ID'));
                                }
                            } else {
                                console.warn('Footer data not available in response');
                            }
                        } catch (e) {
                            console.error('Error in footerCallback:', e);
                        }
                    },
                    language: {
                        processing: "Memuat data...",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data yang ditampilkan",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        zeroRecords: "Tidak ada data yang cocok",
                        emptyTable: "Tidak ada data tersedia",
                        paginate: {
                            first: "Pertama",
                            previous: "Sebelumnya",
                            next: "Selanjutnya",
                            last: "Terakhir"
                        }
                    }
                });

                // Add retry button functionality
                $(document).on('click', '.retry-load', function () {
                    loansTable.ajax.reload();
                });
            }

            // Barcode input handling
            $('#barcodeInput').on('keyup', function (e) {
                console.log("Keyup event on barcodeInput");
                if (e.key === 'Enter') {
                    checkBarcode();
                }
            });

            $('#btnCheckItem').on('click', function () {
                console.log("Check item button clicked");
                checkBarcode();
            });

            // Replace the checkBarcode function with this version:
            function checkBarcode() {
                console.log("checkBarcode function called");
                if (!selectedMember) {
                    Swal.fire({
                        title: '{{ __("catalogs.choose_member") }}',
                        text: '{{ __("common.choose_member_desc") }}',
                        icon: 'warning'
                    });
                    return;
                }

                const barcode = $('#barcodeInput').val().trim();
                if (!barcode) {
                    Swal.fire({
                        title: '{{ __("catalogs.barcode_empty") }}',
                        text: '{{ __("catalogs.return.input_barcode") }}',
                        icon: 'warning'
                    });
                    return;
                }

                // Remove the loading indicator - we'll let AJAX handle its own loading state

                // Check barcode with server
                $.ajax({
                    url: '/catalog/circulation/return/check-barcode',
                    method: 'GET',
                    data: {
                        barcode: barcode,
                        member_id: selectedMember.id
                    },
                    timeout: 120000, // 2 minutes
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log("Check barcode response:", response);
                        // No need for Swal.close() as we didn't open a Swal loading indicator

                        if (response.success) {
                            selectedRent = response.rent;

                            // Populate return form
                            $('#returnTitle').val(selectedRent.title);
                            $('#returnBarcode').val(selectedRent.barcode);
                            $('#returnRentDate').val(selectedRent.rent_date);
                            $('#returnDueDate').val(selectedRent.due_date);
                            $('#returnLateDays').val(selectedRent.penalty_day || 0);
                            $('#returnPenaltyPerDay').val(`Rp ${(selectedRent.penalty_per_day || 0).toLocaleString('id-ID')}`);
                            $('#returnPenaltyTotal').val(`Rp ${(selectedRent.penalty_total || 0).toLocaleString('id-ID')}`);

                            // Show return form
                            $('#returnFormSection').removeClass('d-none');

                            // Handle status changes
                            $('#returnStatus').val(2).trigger('change');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                            $('#barcodeInput').val('').focus();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);

                        Swal.fire({
                            title: 'Error',
                            text: '{{ __("catalogs.search_failed") }}',
                            icon: 'error'
                        });

                        $('#barcodeInput').val('').focus();
                    }
                });
            }

            // Return item button in table
            // Return item button in table
            $(document).on('click', '.btn-return-item', function () {
                const barcode = $(this).data('barcode');
                console.log("Return item button clicked for barcode:", barcode);

                $('#barcodeInput').val(barcode);

                // Show loading indicator
                Swal.fire({
                    title: 'Memproses...',
                    text: '{{ __("catalogs.find_barcode") }}',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Check barcode with server
                $.ajax({
                    url: '/catalog/circulation/return/check-barcode',
                    method: 'GET',
                    data: {
                        barcode: barcode,
                        member_id: selectedMember.id
                    },
                    timeout: 120000, // 2 minutes
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log("Check barcode response:", response);
                        Swal.close();

                        if (response.success) {
                            selectedRent = response.rent;

                            // Populate return form
                            $('#returnTitle').val(selectedRent.title);
                            $('#returnBarcode').val(selectedRent.barcode);
                            $('#returnRentDate').val(selectedRent.rent_date);
                            $('#returnDueDate').val(selectedRent.due_date);
                            $('#returnLateDays').val(selectedRent.penalty_day || 0);
                            $('#returnPenaltyPerDay').val(`Rp ${(selectedRent.penalty_per_day || 0).toLocaleString('id-ID')}`);
                            $('#returnPenaltyTotal').val(`Rp ${(selectedRent.penalty_total || 0).toLocaleString('id-ID')}`);

                            // Show return form
                            $('#returnFormSection').removeClass('d-none');

                            // Handle status changes
                            $('#returnStatus').val(2).trigger('change');

                            // Scroll to return form
                            $('html, body').animate({
                                scrollTop: $('#returnSection').offset().top - 20
                            }, 300);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        console.error("AJAX error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);

                        Swal.fire({
                            title: 'Error',
                            text: '{{ __("catalogs.search_failed") }}',
                            icon: 'error'
                        });
                    }
                });
            });
            // Cancel return button
            $('#btnCancelReturn').on('click', function () {
                console.log("Cancel return button clicked");
                // Clear selected rent
                selectedRent = null;

                // Hide return form
                $('#returnFormSection').addClass('d-none');
                $('#barcodeInput').val('').focus();
            });

            // Return status changed
            $('#returnStatus').on('change', function () {
                const status = $(this).val();
                console.log("Return status changed to:", status);

                // Hide all info boxes
                $('#damageInfo, #lostInfo').addClass('d-none');

                if (status === '3') {
                    // Show damage info
                    $('#damageInfo').removeClass('d-none');
                } else if (status === '4') {
                    // Show lost info
                    $('#lostInfo').removeClass('d-none');
                }
            });

            // Process return button
            $('#btnProcessReturn').on('click', function () {
                console.log("Process return button clicked");
                if (!selectedRent) {
                    return;
                }

                const status = $('#returnStatus').val();
                let statusText = '';

                switch (status) {
                    case '2': statusText = '{{ __("catalogs.returned") }}'; break;
                    case '3': statusText = '{{ __("catalogs.damaged") }}'; break;
                    case '4': statusText = '{{ __("catalogs.lost") }}'; break;
                }

                Swal.fire({
                    title: '{{ __("catalogs.return.confirmation") }}',
                    text: `{{ __('catalogs.return.mark_status') }} ${statusText}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.proceed") }}',
                    cancelButtonText: '{{ __("common.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processReturn(selectedRent.id, status);
                    }
                });
            });

            function processReturn(rentId, status) {
                console.log("Processing return for rent ID:", rentId, "with status:", status);
                $.ajax({
                    url: '/catalog/circulation/return/process',
                    method: 'POST',
                    data: {
                        rent_id: rentId,
                        status: status,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log("Process return response:", response);

                        if (response.success) {
                            Swal.fire({
                                title: '{{ __("common.success_notification") }}',
                                text: `{{ __("catalogs.success_collection") }} ${response.status_text}${response.penalty_total > 0 ? '. {{ __("catalogs.return.fine") }}: Rp ' + response.penalty_total.toLocaleString('id-ID') : ''}`,
                                icon: 'success'
                            });

                            // Reset form
                            selectedRent = null;
                            $('#returnFormSection').addClass('d-none');
                            $('#barcodeInput').val('').focus();

                            // Refresh loans table
                            loansTable.ajax.reload();

                            // Update active loans count
                            selectedMember.active_loans--;
                            $('#activeLoans').text(selectedMember.active_loans);

                            // Update payment section if there's a penalty
                            if (response.penalty_total > 0) {
                                $('#paymentSection').removeClass('d-none');
                                // Refresh penalty information
                                updatePaymentInfo(response.penalty_total);
                            }
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                        Swal.fire({
                            title: 'Error Server',
                            text: '{{ __("catalogs.return.failed") }}',
                            icon: 'error'
                        });
                    }
                });
            }

            // Process payment button
            $('#btnProcessPayment').on('click', function () {
                console.log("Process payment button clicked");
                const amount = parseInt($('#paymentAmount').val());

                if (!amount || amount <= 0) {
                    Swal.fire({
                        title: '{{ __("catalogs.amount_not_valid") }}',
                        text: '{{ __("catalogs.input_valid_amount") }}',
                        icon: 'warning'
                    });
                    return;
                }

                Swal.fire({
                    title: '{{ __("catalogs.payment_confirmation") }}',
                    text: `{{ __('catalogs.fine_process') }} Rp ${amount.toLocaleString('id-ID')}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.proceed") }}',
                    cancelButtonText: '{{ __("common.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/catalog/circulation/return/payment',
                            method: 'POST',
                            data: {
                                member_id: selectedMember.id,
                                amount: amount,
                                rent_id: selectedRent ? selectedRent.id : null,
                                note: $('#paymentNote').val(),
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                console.log("Payment processing response:", response);
                                if (response.success) {
                                    Swal.fire({
                                        title: '{{ __("common.success_notification") }}',
                                        text: response.message,
                                        icon: 'success'
                                    });

                                    // Clear payment form
                                    $('#paymentAmount').val('');
                                    $('#paymentNote').val('');

                                    // Refresh loans table
                                    loansTable.ajax.reload();

                                    // Update payment info
                                    updatePaymentInfo(response.outstanding);
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("AJAX error:", error);
                                Swal.fire({
                                    title: 'Error Server',
                                    text: '{{ __("catalogs.payment_failed") }}',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Update payment info function
            function updatePaymentInfo(totalPenalty) {
                console.log("Updating payment info for penalty:", totalPenalty);
                $.ajax({
                    url: '/catalog/circulation/return/history',
                    method: 'GET',
                    data: {
                        member_id: selectedMember.id,
                        draw: 1,
                        start: 0,
                        length: 1,
                        status: 'all'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log("Payment info response:", response);

                        if (response.footer) {
                            const grandPenalty = response.footer.grand_penalty || 0;
                            const totalPaid = response.footer.total_paid || 0;
                            const outstanding = response.footer.outstanding || 0;

                            // Update payment info
                            $('#paymentTotalPenalty').text(`Rp ${grandPenalty.toLocaleString('id-ID')}`);
                            $('#paymentTotalPaid').text(`Rp ${totalPaid.toLocaleString('id-ID')}`);
                            $('#paymentOutstandingPenalty').text(`Rp ${outstanding.toLocaleString('id-ID')}`);

                            // Set max amount for payment
                            // $('#paymentAmount').attr('max', outstanding);
                            // $('#paymentAmount').val(outstanding);

                            // Hide payment section if no outstanding
                            if (outstanding <= 0) {
                                $('#paymentSection').addClass('d-none');
                            } else {
                                $('#paymentSection').removeClass('d-none');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error when updating payment info:", error);
                    }
                });
            }

            function resetToMemberSearch() {
                // Reset variables
                selectedMember = null;
                selectedRent = null;

                // Hide sections - make sure ALL sections are included
                $('#memberInfo').addClass('d-none');
                $('#returnSection').addClass('d-none');
                $('#returnFormSection').addClass('d-none');
                $('#currentLoansSection').addClass('d-none');
                $('#paymentSection').addClass('d-none'); // Make sure this line exists

                // Hide the change member button
                $('#btnChangeMember').addClass('d-none');

                // Clear the member search input and results
                $('#memberSearch').val('');
                $('#memberResults').empty();
                $('#barcodeInput').val('');

                // Reset form fields
                $('#returnTitle, #returnBarcode, #returnRentDate, #returnDueDate, #returnLateDays, #returnPenaltyPerDay, #returnPenaltyTotal').val('');
                $('#paymentAmount, #paymentNote').val('');

                // Destroy loans table if exists
                if (loansTable) {
                    loansTable.clear().destroy();
                    loansTable = null;
                }

                // Focus on member search
                $('#memberSearch').focus();
            }

            // Handle status filter change
            $('#statusFilter').on('change', function () {
                if (loansTable) {
                    console.log("Status filter changed to:", $(this).val());
                    loansTable.ajax.reload();
                }
            });
        });
    </script>
@endsection