@extends('layouts/layoutMaster')

@section('title', __('catalogs.borrowing.title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">{{ __('catalogs.circulation') }} /</span> {{ __('catalogs.borrowing') }}</h4>

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
                            <p class="mb-1"><strong>{{ __('catalogs.borrowing.limit') }}:</strong> <span id="memberLimit"></span> {{ __('catalogs.book') }}</p>
                            <p class="mb-0"><strong>{{ __('catalogs.borrowing.active') }}:</strong> <span id="activeLoans"></span> {{ __('catalogs.book') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Input Section -->
        <div id="borrowingSection" class="card mb-4 d-none">
            <div class="card-header">
                <h5 class="mb-0">{{ __('catalogs.borrowing.add') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('catalogs.barcode') }}</label>
                        <div class="input-group">
                            <input id="barcodeInput" type="text" class="form-control"
                                placeholder="{{ __('catalogs.return.input_barcode') }}">
                            <button id="btnAddItem" class="btn btn-primary" type="button">
                                <i class="ti ti-plus me-1"></i> {{ __('catalogs.add') }}
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('catalogs.choose_catalog') }}</label>
                        <button id="btnShowCatalog" class="btn btn-danger w-100">
                            <i class="ti ti-book me-1"></i> {{ __('catalogs.find_catalog') }}
                        </button>
                    </div>
                </div>

                <!-- Borrowing Items Table -->
                <div class="table-responsive">
                    <table id="borrowingTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Barcode</th>
                                <th width="40%">{{ __('catalogs.title') }}</th>
                                <th width="10%">{{ __('catalogs.rental_cost') }}</th>
                                <th width="10%">{{ __('catalogs.borrowing.duration') }}</th>
                                <th width="10%">{{ __('catalogs.due_date') }}</th>
                                <th width="10%">{{ __('common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th colspan="4" id="totalCost">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button id="btnProcess" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> {{ __('catalogs.borrowing.process') }}
                    </button>
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
                    <!-- Update the loans table structure to include actions column -->
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
                                <th>{{ __('catalogs.return.date') }}</th>
                                <th>{{ __('config.holiday.page.title') }}</th>
                                <th>{{ __('catalogs.fine_day') }}</th>
                                <th>{{ __('catalogs.total_fine') }}</th>
                                <th>{{ __('common.active') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Current loans will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="10" class="text-end">{{ __('catalogs.total_fine') }}</th>
                                <th colspan="2" id="totalPenalty">Rp 0</th>
                            </tr>
                            <tr>
                                <th colspan="10" class="text-end">{{ __('catalogs.total_paid') }}</th>
                                <th colspan="2" id="totalPaid">Rp 0</th>
                            </tr>
                            <tr>
                                <th colspan="10" class="text-end">{{ __('catalogs.remaining_fine') }}</th>
                                <th colspan="2" id="outstandingPenalty">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Modal -->
    <div class="modal fade" id="catalogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('catalogs.catalog_collection') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input id="catalogSearch" type="text" class="form-control"
                                        placeholder="{{ __('catalogs.find_title') }}">
                                    <button id="btnCatalogSearch" class="btn btn-primary">
                                        <i class="ti ti-search"></i> {{ __('catalogs.find') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select id="catalogStatusFilter" class="form-select">
                                    <option value="1" selected>{{ __('catalogs.avail') }}</option>
                                    <option value="0">{{ __('catalogs.all_status') }}</option>
                                    <option value="2">{{ __('catalogs.borrowed') }}</option>
                                    <option value="3">{{ __('catalogs.damaged') }}</option>
                                    <option value="4">{{ __('catalogs.lost') }}</option>
                                    <option value="5">Expired</option>
                                    <option value="6">{{ __('catalogs.lost_replaced') }}</option>
                                    <option value="7">{{ __('catalogs.processed') }}</option>
                                    <option value="8">{{ __('catalogs.spare') }}</option>
                                    <option value="9">Weeding</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="catalogTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>{{ __('catalogs.title') }}</th>
                                    <th>{{ __('sbkps.author') }}</th>
                                    <th>{{ __('catalogs.publisher') }}</th>
                                    <th>{{ __('catalogs.year') }}</th>
                                    <th>Status</th>
                                    <th>{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                        </table>
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
            // Auto-focus on member search field
            $('#memberSearch').focus();

            // Global variables
            let selectedMember = null;
            let borrowingItems = [];
            let loansTable = null;
            let catalogTable = null;

            // Initialize catalog DataTable
            catalogTable = $('#catalogTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/catalog/circulation/borrowing/catalog/search',
                    data: function (d) {
                        d.q = $('#catalogSearch').val();
                        d.status = $('#catalogStatusFilter').val(); // Add status filter
                    }
                },
                columns: [{
                    data: 'barcode'
                },
                {
                    data: 'title'
                },
                {
                    data: 'author'
                },
                {
                    data: 'publisher'
                },
                {
                    data: 'year'
                },
                {
                    data: 'status',
                    render: function (data, type, row) {
                        return `<span class="badge bg-${row.status_class}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        if (row.is_available) {
                            return `<button class="btn btn-sm btn-primary btn-add-catalog" data-stock-id="${row.stock_id}">
                                                                        <i class="ti ti-plus"></i> {{ __('catalogs.add') }}
                                                                    </button>`;
                        } else {
                            return `<button class="btn btn-sm btn-secondary" disabled>
                                                                        <i class="ti ti-lock"></i> {{ __('catalogs.not_avail') }}
                                                                    </button>`;
                        }
                    }
                }
                ]
            });

            // Status filter change event
            $('#catalogStatusFilter').on('change', function () {
                catalogTable.ajax.reload();
            });

            // Update catalog search button click to include status
            $('#btnCatalogSearch').on('click', function () {
                catalogTable.ajax.reload();
            });

            // Reset filter when modal opens
            $('#btnShowCatalog').on('click', function () {
                $('#catalogModal').modal('show');
                $('#catalogSearch').val('');
                $('#catalogStatusFilter').val('1'); // Reset to "Tersedia" by default
                catalogTable.ajax.reload();
            });


            // Member search handling
            $('#memberSearch').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    searchMember();
                }
            });

            $('#btnSearchMember').on('click', function () {
                searchMember();
            });

            function searchMember() {
                const query = $('#memberSearch').val().trim();
                if (query.length < 3) {
                    Swal.fire({
                        title: '{{ __("catalogs.input_short") }}',
                        text: '{{ __("catalogs.minimum_member") }}',
                        icon: 'warning'
                    });
                    return;
                }

                $.ajax({
                    url: '/catalog/circulation/borrowing/auto-data',
                    data: {
                        q: query
                    },
                    success: function (data) {
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
                                const isLimitReached = item.active_loans >= item.loan_limit;
                                const buttonClass = isLimitReached ? 'bg-danger' : 'bg-primary';
                                const limitText = isLimitReached ? 'Batas tercapai' : `${item.active_loans}/${item.loan_limit}`;

                                html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                                                                                                                <div>
                                                                                                                                                                                                                                    <div><strong>${item.fullname}</strong></div>
                                                                                                                                                                                                                                    <div class="text-muted">${item.nim} - ${item.username}</div>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <div class="d-flex gap-2 align-items-center">
                                                                                                                                                                                                                                    <span class="badge ${buttonClass}">${limitText}</span>
                                                                                                                                                                                                                                    <button class="btn btn-sm btn-primary btn-select-member" data-id="${item.id}">
                                                                                                                                                                                                                                        <i class="ti ti-user-check"></i> Pilih
                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>`;
                            });
                        }
                        $('#memberResults').html(html).show();
                    }
                });
            }

            // Select member
            function selectMember(member) {
                $.ajax({
                    url: '/catalog/circulation/borrowing/member/info',
                    data: {
                        member_id: member.id
                    },
                    success: function (response) {
                        if (response.success) {
                            selectedMember = response.member;

                            // Update UI
                            $('#memberName').text(selectedMember.name);
                            $('#memberNumber').text(selectedMember.number);
                            $('#memberType').text(selectedMember.type);
                            $('#memberLimit').text(selectedMember.loan_limit);
                            $('#activeLoans').text(selectedMember.active_loans);

                            // Update search field with the selected member
                            $('#memberSearch').val(`${member.username} - ${member.nim} - ${member.fullname}`);

                            // Show member info and borrowing section
                            $('#memberInfo').removeClass('d-none');
                            $('#borrowingSection').removeClass('d-none');
                            $('#currentLoansSection').removeClass('d-none');
                            $('#memberResults').hide();
                            // Show the change member button
                            $('#btnChangeMember').removeClass('d-none');


                            // Focus on barcode input
                            $('#barcodeInput').focus();

                            // Initialize or refresh loans table
                            initLoansTable(member.id);
                            loansTable.on('draw', function () {
                                // Make sure the container is responsive
                                $('#currentLoansSection .card-body').css('overflow-x', 'auto');

                                // Only add action column if it doesn't already exist
                                if ($('#loansTable thead tr th').length < 12) {
                                    // Add the header column first if missing
                                    $('#loansTable thead tr').append('<th>{{__("common.action")}}</th>');
                                }

                                // Add action buttons to each row
                                $('#loansTable tbody tr').each(function (index) {
                                    const rowData = loansTable.row(index).data();
                                    if (!rowData) return;

                                    // Remove any existing action cell to prevent duplicates
                                    $(this).find('td:last-child').remove();

                                    if (!rowData.return_date) {
                                        const rentId = rowData.id;
                                        let actionHtml = '';

                                        // Check if we can extend - only add the Perpanjang button
                                        if (rowData.extended_count < selectedMember.extension_limit) {
                                            actionHtml += `<button class="btn btn-sm btn-primary btn-extend" data-id="${rentId}">
                                                                                                                <i class="ti ti-calendar-plus"></i> {{ __('catalogs.extend') }}
                                                                                                            </button>`;
                                        } else {
                                            actionHtml = '-'; // Show dash if can't extend
                                        }

                                        $(this).append(`<td>${actionHtml}</td>`);
                                    } else {
                                        $(this).append('<td>-</td>');
                                    }
                                });
                            });

                            // Display penalty alert if any
                            if (selectedMember.total_penalty > 0) {
                                Swal.fire({
                                    title: '{{ __("catalogs.fine_information") }}',
                                    text: `{{ __('catalogs.denda_sebesar') }} Rp. ${selectedMember.total_penalty.toLocaleString('id-ID')}`,
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
                    }
                });
            }
            // Event handler for manually selecting a member from the list
            $(document).on('click', '.btn-select-member', function () {
                const memberId = $(this).data('id');

                // Find the member data from window.lastSearchResults directly
                if (window.lastSearchResults) {
                    const memberItem = window.lastSearchResults.find(item => item.id === memberId);
                    if (memberItem) {
                        selectMember(memberItem);
                        return;
                    }
                }

                // Fallback if data not found
                $.ajax({
                    url: '/catalog/circulation/borrowing/member/info',
                    data: {
                        member_id: memberId
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
                    }
                });
            });

            // Store member data to use when selecting
            $('#memberResults').on('DOMSubtreeModified', function () {
                // Store items data on the results element
                if (window.lastSearchResults) {
                    $(this).data('items', window.lastSearchResults);
                }
            });

            // Change member
            // Change member button
            $('#btnChangeMember').on('click', function () {
                Swal.fire({
                    title: '{{ __("catalogs.change_member") }}?',
                    text: '{{ __("catalogs.item_lost") }}',
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

            // Barcode input handling
            $('#barcodeInput').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    addItemByBarcode();
                }
            });

            $('#btnAddItem').on('click', function () {
                addItemByBarcode();
            });

            function addItemByBarcode() {
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

                // Check if we've reached the borrowing limit
                if (borrowingItems.length + selectedMember.active_loans >= selectedMember.loan_limit) {
                    Swal.fire({
                        title: '{{ __("catalogs.limit_reached") }}',
                        text: `{{ __('catalogs.member_limit') }} (${selectedMember.loan_limit} {{ __('catalogs.book') }})`,
                        icon: 'error'
                    });
                    return;
                }

                // Check if barcode already in the list
                const exists = borrowingItems.some(item => item.barcode === barcode);
                if (exists) {
                    Swal.fire({
                        title: '{{ __("catalogs.duplicate") }}',
                        text: '{{ __("catalogs.barcode_already") }}',
                        icon: 'warning'
                    });
                    $('#barcodeInput').val('').focus();
                    return;
                }

                // Check barcode with server
                $.ajax({
                    url: '/catalog/circulation/borrowing/check-barcode',
                    data: {
                        barcode: barcode,
                        member_id: selectedMember.id
                    },
                    success: function (response) {
                        if (response.success) {
                            // Add to list
                            borrowingItems.push({
                                stock_id: response.item.stock_id,
                                item_id: response.item.item_id,
                                barcode: response.item.barcode,
                                title: response.item.title,
                                rent_cost: response.item.rent_cost || 0,
                                penalty_cost: response.item.penalty_cost || 0,
                                rent_period: selectedMember.loan_period || 7,
                                due_date: response.item.due_date
                            });

                            // Update table
                            updateBorrowingTable();

                            // Clear input and focus
                            $('#barcodeInput').val('').focus();
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                            $('#barcodeInput').val('').focus();
                        }
                    }
                });
            }



            // Catalog search handling
            $('#catalogSearch').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    catalogTable.ajax.reload();
                }
            });

            // Add item from catalog
            // Add item from catalog
            $(document).on('click', '.btn-add-catalog', function () {
                const stockId = $(this).data('stock-id');

                // Check if we've reached the borrowing limit
                if (borrowingItems.length + selectedMember.active_loans >= selectedMember.loan_limit) {
                    Swal.fire({
                        title: '{{ __("catalogs.limit_reached") }}',
                        text: `{{ __('catalogs.member_limit') }} (${selectedMember.loan_limit} {{ __('catalogs.book') }})`,
                        icon: 'error'
                    });
                    return;
                }

                // Check with server
                $.ajax({
                    url: '/catalog/circulation/borrowing/check-item',
                    method: 'GET',
                    data: {
                        stock_id: stockId,
                        member_id: selectedMember.id
                    },
                    success: function (response) {
                        if (response.success) {
                            // Check if already in list
                            const exists = borrowingItems.some(item => item.stock_id === response.item.stock_id);
                            if (exists) {
                                Swal.fire({
                                    title: '{{ __("catalogs.duplicate") }}',
                                    text: '{{ __("catalogs.item_already") }}',
                                    icon: 'warning'
                                });
                                return;
                            }

                            // Add to list
                            borrowingItems.push({
                                stock_id: response.item.stock_id,
                                item_id: response.item.item_id,
                                barcode: response.item.barcode,
                                title: response.item.title,
                                rent_cost: response.item.rent_cost || 0,
                                penalty_cost: response.item.penalty_cost || 0,
                                rent_period: selectedMember.loan_period || 7,
                                due_date: response.item.due_date
                            });

                            // Update table
                            updateBorrowingTable();

                            // Close modal
                            $('#catalogModal').modal('hide');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Ajax error:", error);
                        Swal.fire({
                            title: 'Error Server',
                            text: '{{ __("catalogs.failed_to_add") }}',
                            icon: 'error'
                        });
                    }
                });
            });

            // Catalog search handling - trigger search on button click too
            $('#catalogSearch').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    catalogTable.ajax.reload();
                }
            });

            // Remove item from borrowing list
            $(document).on('click', '.btn-remove-item', function () {
                const index = $(this).data('index');
                borrowingItems.splice(index, 1);
                updateBorrowingTable();
            });

            // Update borrowing table
            function updateBorrowingTable() {
                let html = '';
                let totalCost = 0;

                if (borrowingItems.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">{{ __("catalogs.borrowing.not_selected") }}</td></tr>';
                } else {
                    borrowingItems.forEach((item, index) => {
                        const itemCost = (item.rent_cost * item.rent_period) || 0;
                        totalCost += itemCost;

                        html += `<tr>
                                                                                                                                                                                                <td>${index + 1}</td>
                                                                                                                                                                                                <td>${item.barcode}</td>
                                                                                                                                                                                                <td>${item.title}</td>
                                                                                                                                                                                                <td>Rp. ${item.rent_cost.toLocaleString('id-ID')}</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <input type="number" class="form-control form-control-sm" 
                                                                                                                                                                                                           value="${item.rent_period}" readonly>
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>${item.due_date}</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-sm btn-danger btn-remove-item" data-index="${index}">
                                                                                                                                                                                                        <i class="ti ti-trash"></i>
                                                                                                                                                                                                    </button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>`;
                    });
                }

                $('#borrowingTable tbody').html(html);
                $('#totalCost').text(`Rp. ${totalCost.toLocaleString('id-ID')}`);
            }

            // Handle rent period change
            $(document).on('change', '.rent-period', function () {
                const index = $(this).data('index');
                const newPeriod = parseInt($(this).val());

                if (newPeriod < 1) {
                    $(this).val(1);
                    return;
                }

                if (newPeriod > 30) {
                    $(this).val(30);
                    return;
                }

                // Update period and recalculate due date
                borrowingItems[index].rent_period = newPeriod;

                $.ajax({
                    url: '/catalog/circulation/borrowing/calculate-due-date',
                    data: {
                        start_date: moment().format('YYYY-MM-DD'),
                        days: newPeriod
                    },
                    success: function (response) {
                        if (response.success) {
                            borrowingItems[index].due_date = response.due_date;
                            updateBorrowingTable();
                        }
                    }
                });
            });

            // Process borrowing
            $('#btnProcess').on('click', function () {
                if (!selectedMember) {
                    Swal.fire({
                        title: '{{ __("catalogs.choose_member") }}',
                        text: '{{ __("catalogs.please_choose_member") }}',
                        icon: 'warning'
                    });
                    return;
                }

                if (borrowingItems.length === 0) {
                    Swal.fire({
                        title: '{{ __("catalogs.no_item") }}',
                        text: '{{ __("catalogs.borrowing.add_item") }}',
                        icon: 'warning'
                    });
                    return;
                }

                // Confirm process
                Swal.fire({
                    title: '{{ __("catalogs.borrowing.process") }}?',
                    text: `{{ __('catalogs.proses') }} ${borrowingItems.length} {{ __('catalogs.borrowed_by') }} ${selectedMember.name}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.proceed") }}',
                    cancelButtonText: '{{ __("common.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Prepare data
                        const items = borrowingItems.map(item => ({
                            stock_id: item.stock_id,
                            rent_cost: item.rent_cost,
                            penalty_cost: item.penalty_cost,
                            rent_period: item.rent_period
                        }));

                        // Send to server
                        $.ajax({
                            url: '/catalog/circulation/borrowing/store',
                            method: 'POST',
                            data: {
                                member_id: selectedMember.id,
                                items: items
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '{{ __("common.success_notification") }}',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        // Reset everything and return to member search
                                        resetToMemberSearch();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            }
                        });
                    }
                });
            });

            function resetToMemberSearch() {
                // Reset borrowing items
                borrowingItems = [];

                // Reset selected member
                selectedMember = null;

                // Hide sections
                $('#memberInfo').addClass('d-none');
                $('#borrowingSection').addClass('d-none');
                $('#currentLoansSection').addClass('d-none');
                $('#currentLoansSection .card-body').css('overflow-x', 'auto');
                // Clear the member search input and results
                $('#memberSearch').val('');
                $('#memberResults').empty();
                $('#btnChangeMember').addClass('d-none');
                // Reset tables
                $('#borrowingTable tbody').empty();
                $('#totalCost').text('Rp 0');

                if (loansTable) {
                    loansTable.clear().destroy();
                    loansTable = null;
                }

                // Focus on member search
                $('#memberSearch').focus();
            }
            // Initialize loans table
            function initLoansTable(memberId) {
                if (loansTable) {
                    loansTable.destroy();
                }

                loansTable = $('#loansTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '/catalog/circulation/borrowing/history',
                        data: function (d) {
                            d.member_id = memberId;
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'barcode'
                    },
                    {
                        data: 'rent_date'
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return row.due_date;
                        }
                    },
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
                        data: 'return_date',
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
                            if (!row.return_date && row.extended_count < selectedMember.extension_limit) {
                                return `<button class="btn btn-sm btn-primary btn-extend" data-id="${row.id}">
                                                                                                                    <i class="ti ti-calendar-plus"></i> {{ __('catalogs.extend') }}
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
                            } else {
                                console.warn('Footer data not available in response');
                            }
                        } catch (e) {
                            console.error('Error in footerCallback:', e);
                        }
                    }
                });
            }

            // Handle status filter change
            $('#statusFilter').on('change', function () {
                if (loansTable) {
                    loansTable.ajax.reload();
                }
            });

            // Extend book
            // Extend book
            $(document).on('click', '.btn-extend', function () {
                const rentId = $(this).data('id');

                Swal.fire({
                    title: '{{ __("catalogs.extend_borrowing") }}',
                    html: `<div class="mb-3">
                                                                                    <label class="form-label">{{ __('catalogs.total_days') }} (max ${selectedMember.extension_days})</label>
                                                                                    <input id="extendDays" type="number" class="form-control" 
                                                                                    value="${selectedMember.extension_days}" min="1" max="${selectedMember.extension_days}" readonly>
                                                                                    </div>`,
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.extend") }}',
                    cancelButtonText: '{{ __("common.cancel") }}',
                    preConfirm: () => {
                        const days = document.getElementById('extendDays').value;
                        if (!days || days < 1) {
                            Swal.showValidationMessage('{{ __("catalogs.number_days_required") }}');
                            return false;
                        }
                        return days;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/catalog/circulation/borrowing/extend',
                            method: 'POST',
                            data: {
                                rent_id: rentId,
                                days: result.value
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '{{ __("common.success") }}',
                                        text: response.message,
                                        icon: 'success'
                                    });
                                    loansTable.ajax.reload();
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Ajax error:", error);
                                Swal.fire({
                                    title: 'Error Server',
                                    text: '{{ __("catalogs.failed_to_extend") }}',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Return book (already exists, but adding here for completeness)
            $(document).on('click', '.btn-return', function () {
                const rentId = $(this).data('id');

                Swal.fire({
                    title: '{{ __("catalogs.return.kembalikan") }} {{ __("catalogs.book") }}?',
                    text: '{{ __("catalogs.return.date_set") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("catalogs.return.kembalikan") }}',
                    cancelButtonText: '{{ __("common.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/catalog/circulation/borrowing/return',
                            method: 'POST',
                            data: {
                                rent_id: rentId
                            },
                            success: function (response) {
                                if (response.success) {
                                    let message = response.message;
                                    if (response.penalty_total > 0) {
                                        message += ` {{ __('catalogs.return.fine') }}: Rp. ${response.penalty_total.toLocaleString('id-ID')}`;
                                    }

                                    Swal.fire({
                                        title: '{{ __("common.success_notification") }}',
                                        text: message,
                                        icon: 'success'
                                    });

                                    // Refresh table and update counts
                                    loansTable.ajax.reload();
                                    selectedMember.active_loans--;
                                    $('#activeLoans').text(selectedMember.active_loans);
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Ajax error:", error);
                                Swal.fire({
                                    title: 'Error Server',
                                    text: '{{ __("catalogs.return.failed") }}',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection