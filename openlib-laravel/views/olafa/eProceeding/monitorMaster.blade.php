@extends('layouts/layoutMaster')

@section('title', 'Monitoring E-Proceeding')

@section('vendor-style')
@endsection

@section('page-style')
<style>
.select2-container {
        z-index: 1;
    }

.card {
    z-index: 0;
}

</style>
@endsection


@section('content')

<div class="card">
    <h5 class="card-header">Advanced Search</h5>
    <!--Search Form -->
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3 ">
                        
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="tahun" class="form-label">Pilih Edisi E-Proceeding:</label>
                            <select id="tahun" class="select2 form-select form-select-md">
                                @foreach($edition as $item)
                                    @php
                                        $mulai = explode('-', $item->datestart);
                                        $mulai = $mulai[2] . '-' . $mulai[1] . '-' . $mulai[0];
                                        $finish = explode('-', $item->datefinish);
                                        $finish = $finish[2] . '-' . $finish[1] . '-' . $finish[0];
                                    @endphp
                                    <option value="{{ $item->eproc_edition_id }}">
                                        {{ $item->nama }} ({{ $mulai }} s/d {{ $finish }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="list" class="form-label">Pilih List E-Proceeding:</label>
                            <select id="list" class="select2 form-select form-select-md">
                                @foreach($list as $item)
                                    <option value="{{ $item->list_id }}">{{ $item->list_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="fakultas" class="form-label">Pilih Fakultas:</label>
                            <select id="fakultas" class="select2 form-select form-select-md">
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button type="button" class="btn btn-primary w-100" id="filterButton">Filter</button>
                        </div>

                    </div>

                </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card-datatable text-wrap" >
            <table class="table table-bordered table-striped dataTable no-footer" id="table">
                <thead>
                    <tr>
                        <th  rowspan="2">Fakultas</th>
                        <th  rowspan="2">Program Studi</th>
                        <th  rowspan="2">TA/PA/Thesis Masuk</th>
                        <th  rowspan="2">Jurnal Masuk</th>
                        <th  rowspan="2">On Draft</th>
                        <th  rowspan="2">Need Revision</th>
                        <th  rowspan="2">Ready for Review</th>
                        <th  rowspan="2">Not Feasible All</th>
                        <th  rowspan="2">Not Feasible Jurnal</th>
                        <th  rowspan="2">Publish Eksternal</th>
                        <th  rowspan="2">Publish Eksternal (Loa Pending)</th>
                        <th  rowspan="2">Jurnal Approved Publish Tel-U Proceeding</th>
                        <th  rowspan="2">Metadata Approve for Catalog</th>
                        <th  colspan="7" class="text-center">Archived</th>
                    </tr>
                    <tr>
                        <th >Not Feasible All </th>
                        <th >Not Feasible </th>
                        <th >Publish Eksternal </th>
                        <th >Publish Eksternal (Loa Pending) </th>
                        <th >Jurnal Approved Publish Tel-U Proceeding </th>
                        <th >Metadata Approve for Catalog </th>
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
            </table>
    </div>

</div>

<!-- Modal for TA -->
<div class="modal fade" id="modal_ta" tabindex="-1" aria-labelledby="modal_taLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_taLabel">E-Proceeding Monitoring TA/PA/Thesis Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content goes here -->
                
                <div class="card mb-3">
                    {{-- <div class="card-header">
                        <h6>E-Proceeding Monitoring TA/PA/Thesis Masuk</h6>
                    </div> --}}
                    <div class="card-body">
                        
                        {{-- <p id="modalContent"></p>
                        <p id="modalEdisi"></p> --}}
                            <div class="card-datatable text-nowrap">
                                    <table class="table" id="table_ta">
                                        <thead>
                                            <tr>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Judul</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            
                                        </tbody>
                                    </table>
                            </div>
                    </div>
                </div>
                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Jurnal -->
<div class="modal fade" id="modal_jurnal" tabindex="-1" aria-labelledby="modal_jurnalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_jurnalLabel">E-Proceeding Monitoring Jurnal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        {{-- <!-- Modal content goes here -->
                        <p id="modalContent"></p>
                        <p id="modalEdisi"></p> --}}
                        <div class="card-datatable ">
                            <table class="table" id="table_jurnal">
                                <thead>
                                    <tr>
                                        <th width='10%'>NIM</th>
                                        <th>Nama</th>
                                        <th>Judul</th>
                                    </tr>
                                </thead>
                                <tbody >
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Doc -->
<div class="modal fade" id="modal_doc" tabindex="-1" aria-labelledby="modal_docLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_docLabel">E-Proceeding Monitoring Doc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Modal content goes here -->
                        <p id="modalContent"></p>
                        <p id="modalEdisi"></p>
                        <p id="modalID"></p>
                        <div class="card-datatable ">
                            <table class="table" id="table_doc">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody >
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Publish -->
<div class="modal fade" id="modal_publish" tabindex="-1" aria-labelledby="modal_publishLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_publishLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content goes here -->
                <p id="modalContent"></p>
                <p id="modalEdisi"></p>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6>E-Proceeding Monitoring Publish</h6>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable">
                            <table class="table" id="table_publish">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Judul</th>
                                        <th >Surat Bebas Pinjam</th>
                                    </tr>
                                </thead>
                                <tbody >
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Archieved -->
<div class="modal fade" id="modal_archieved" tabindex="-1" aria-labelledby="modal_archievedLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_archievedLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content goes here -->
                <p id="modalContent"></p>
                <p id="modalEdisi"></p>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6>E-Proceeding Monitoring Archieved</h6>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable ">
                            <table class="table" id="table_archieved">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Judul</th>
                                    </tr>
                                </thead>
                                <tbody >
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Archieved Journal Publish -->
<div class="modal fade" id="modal_archievedjournalpublish" tabindex="-1" aria-labelledby="modal_archievedjournalpublishLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_archievedjournalpublishLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content goes here -->
                <p id="modalContent"></p>
                <p id="modalEdisi"></p>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6>E-Proceeding Monitoring Archieved Journal Publish</h6>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable ">
                            <table class="table" id="table_archievedjournalpublish">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Katalog</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
let url = '{{ url('olafa/e-proceeding-monitor/') }}';

$(function() {
    dTable = $('#table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.edisi = $('#tahun').val(); //harus refactor nama 
                d.list = $('#list').val(); //harus refactor nama
                d.faculty = $('#fakultas').val();
            },
        },
        columns: [
            { data: 'nama_fakultas', name: 'nama_fakultas', class: 'text-center', },
            { data: 'nama_prodi', name: 'nama_prodi', class: 'text-center', },
            { data: 'tamasuk', name: 'tamasuk', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(\'tamasuk\', \'' + row.jurusan + '\', \'' + row.edisi + '\' )">' + data + '</button>';
            }},
            { data: 'jurnal', name: 'jurnal', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'jurnal\', \'' + row.jurusan + '\', \'' + row.edisi + '\' )">' + data + '</button>';
            }},
            { data: 'draft', name: 'draft', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'draft\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'revision', name: 'revision', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'revision\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'review', name: 'review', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'review\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'feasibleall', name: 'feasibleall', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'feasibleall\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'feasiblejurnal', name: 'feasiblejurnal', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'feasiblejurnal\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'eksternal', name: 'eksternal', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'eksternal\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'loapending', name: 'loapending', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'loapending\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'jurnalpublish', name: 'jurnalpublish', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'jurnalpublish\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'metadata', name: 'metadata', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'metadata\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedfeasibleall', name: 'archievedfeasibleall', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedfeasibleall\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedfeasible', name: 'archievedfeasible', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedfeasible\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedeksternal', name: 'archievedeksternal', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedeksternal\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedloapending', name: 'archievedloapending', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedloapending\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedjurnalpublish', name: 'archievedjurnalpublish', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedjurnalpublish\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }},
            { data: 'archievedmetadata', name: 'archievedmetadata', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0" onclick="showModal(\'archievedmetadata\', \'' + row.jurusan + '\', \'' + row.edisi + '\')">' + data + '</button>';
            }}
        ],
        responsive: false,
        scrollX: true,
    });
     // Reload data table when filter button is clicked
    $('#filterButton').on('click', function() {
        dTable.ajax.reload();
    });

    function updateFakultasOptions(listValue) {
        let newOptions = '';
        if (listValue == '1') {
            newOptions = `
                <option value="all">PILIH FAKULTAS</option>
                <option value="7">FAKULTAS INFORMATIKA</option>
                <option value="6">FAKULTAS REKAYASA INDUSTRI</option>
                <option value="5">FAKULTAS TEKNIK ELEKTRO</option>
            `;
        } else if (listValue == '2') {
            newOptions = `
                <option value="3">FAKULTAS ILMU TERAPAN</option>
            `;
        } else if (listValue == '3') {
            newOptions = `
                <option value="all">PILIH FAKULTAS</option>
                <option value="8">FAKULTAS EKONOMI DAN BISNIS</option>
                <option value="9">FAKULTAS KOMUNIKASI DAN ILMU SOSIAL</option>
            `;
        } else if (listValue == '4') {
            newOptions = `
                <option value="4">FAKULTAS INDUSTRI KREATIF</option>
            `;
        } else {
            newOptions = `
                <option value="">PILIH FAKULTAS</option>
            `;
        }
        $('#fakultas').html(newOptions);
    }

    // Initial update based on the current value of #list
    updateFakultasOptions($('#list').val());

    // Update options when the value of #list changes
    $('#list').on('change', function() {
        updateFakultasOptions($(this).val());
    });

});

function getColumnMap() {
        return {
            'tamasuk': { modalId: '#modal_ta' },
            'jurnal': { modalId: '#modal_jurnal' },
            'draft': { id: 22, modalId: '#modal_doc' },
            'revision': { id: 2, modalId: '#modal_doc' },
            'review': { id: 1, modalId: '#modal_doc' },
            'feasibleall': { id: 4, modalId: '#modal_doc' },
            'feasiblejurnal': { id: 3, modalId: '#modal_doc' },
            'eksternal': { id: 52, modalId: '#modal_doc' },
            'loapending': { id: 64, modalId: '#modal_doc' },
            'jurnalpublish': { modalId: '#modal_publish' },
            'metadata': { id: 91, modalId: '#modal_doc' },
            'archievedfeasibleall': { id:4,modalId: '#modal_archieved' },
            'archievedfeasible': {id:3, modalId: '#modal_archieved' },
            'archievedeksternal': {id:52, modalId: '#modal_archieved' },
            'archievedloapending': {id:64, modalId: '#modal_archieved' },
            'archievedjurnalpublish': {modalId: '#modal_archievedjournalpublish' },
            'archievedmetadata': { modalId: '#modal_archieved' }
            // 'total????': { id: 5,modalId: '#modal_doc' }
        };
}

function updateTableHeader(id) {
    let detail = '';
    if (id == 52 || id == 64 || id == 3 || id == 4 || id == 91) {
        detail = 'publish';
    }

    let theadContent = '';
    if (detail === 'publish') {
        theadContent = `
            <tr class="headings">
                <th width="10%">NIM</th>
                <th width="20%">Nama</th>
                <th width="65%">Judul</th>
                ${id != 64 ? '<th width="5%">Surat Bebas Pinjam</th>' : '<th width="5%">File bisa di transfer</th>'}
            </tr>
        `;
    } else {
        theadContent = `
            <tr class="headings">
                <th width="10%">NIM</th>
                <th width="20%">Nama</th>
                <th width="70%">Judul</th>
            </tr>
        `;
    }

    $('#table_doc thead').html(theadContent);
}

function showModal(column, jurusan, edisi) {
        const columnMap = getColumnMap();
        let modalId = '#defaultModal';
        let id;

        if (columnMap[column]) {
            modalId = columnMap[column].modalId || modalId;
            id = columnMap[column].id;
        }

        $(modalId).find('#modalContent').text('Jurusan: ' + jurusan);
        $(modalId).find('#modalEdisi').text('Edisi: ' + edisi);
        $(modalId).find('#modalID').text('ID: ' + id);
        $(modalId).modal('show');

        if (modalId === '#modal_ta') {
            initializeTaDataTable(jurusan, edisi);
        }else if (modalId === '#modal_publish') {
            initializePublishDataTable(jurusan, edisi);
        }else if (modalId === '#modal_jurnal') {
            initializeJurnalDataTable(jurusan, edisi);
        }else if (modalId === '#modal_doc') {
            updateTableHeader(id);
            initializeDocDataTable(jurusan, edisi, id);
        }else if (modalId === '#modal_archieved') {
            initializeArchivedDataTable(jurusan, edisi,id);
        }else if (modalId === '#modal_archievedjournalpublish') {
            initializeArchivedPublishDataTable(jurusan, edisi);
        }
}

function initializeTaDataTable(jurusan, edisi) {
    if ($.fn.DataTable.isDataTable('#table_ta')) {
        $('#table_ta').DataTable().clear().destroy();
    }
    $('#table_ta').DataTable({
    processing: true,
    serverSide: true,
    // searching: false,
    buttons: [],
    // lengthChange: false,
    // paging: false,
    // info: false,
    pageLength: 10,
    responsive: false,
    scrollX: true,
    ajax: {
        url: url+'/dt_detail_ta',
        type: 'POST',
        data: {
            jurusan: jurusan,
            edisi: edisi,
            _token: '{{ csrf_token() }}' // Include CSRF token if needed
        }
    },
    columns: [
            { data: 'master_data_user', name: 'master_data_user' },
            { data: 'master_data_fullname', name: 'master_data_fullname' },
            { data: 'title', name: 'title' },
            { data: 'state_name', name: 'state_name' },
        ]   
    });
}

function initializeJurnalDataTable(jurusan, edisi) {
    if ($.fn.DataTable.isDataTable('#table_jurnal')) {
        $('#table_jurnal').DataTable().clear().destroy();
    }

    $('#table_jurnal').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        // responsive: false,
        // scrollX: true,
        ajax: {
            url: url + '/dt_detail_jurnal',
            type: 'POST',
            data: {
                jurusan: jurusan,
                edisi: edisi,
                _token: '{{ csrf_token() }}' // Include CSRF token if needed
            }
        },
        columns: [
            { data: 'master_data_user', name: 'master_data_user' },
            { data: 'master_data_fullname', name: 'master_data_fullname' },
            { data: 'title', name: 'title' },
        ]
    });
}

function initializeDocDataTable(jurusan, edisi,id) {
    if ($.fn.DataTable.isDataTable('#table_doc')) {
        $('#table_doc').DataTable().clear().destroy();
    }

    const columns = [
        { data: 'master_data_user', name: 'master_data_user' },
        { data: 'master_data_fullname', name: 'master_data_fullname' },
        { data: 'title', name: 'title' }
    ];

    if (id == 52 || id == 64 || id == 3 || id == 4 || id == 91) {
        if (id == 64) {
            columns.push({
                data: 'file',
                name: 'file',
                class: 'text-center',
                render: function(data, type, row) {
                    if (data >= 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                    }
                }
            });
        } else {
            columns.push({
                data: 'free_letter',
                name: 'free_letter',
                class: 'text-center',
                render: function(data, type, row) {
                    if (data >= 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                    }
                }
            });
        }
    }

    $('#table_doc').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        // responsive: false,
        // scrollX: true,
        ajax: {
            url: url + '/dt_detail_doc',
            type: 'POST',
            data: {
                jurusan: jurusan,
                edisi: edisi,
                id: id,
                _token: '{{ csrf_token() }}' // Include CSRF token if needed
            }
        },
        columns: columns
    });
}

function initializePublishDataTable(jurusan, edisi) {
    if ($.fn.DataTable.isDataTable('#table_publish')) {
        $('#table_publish').DataTable().clear().destroy();
    }

    $('#table_publish').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        // responsive: false,
        // scrollX: true,
        ajax: {
            url: url + '/dt_detail_publish',
            type: 'POST',
            data: {
                jurusan: jurusan,
                edisi: edisi,
                _token: '{{ csrf_token() }}' // Include CSRF token if needed
            }
        },
        columns: [
            { data: 'master_data_user', name: 'master_data_user' },
            { data: 'master_data_fullname', name: 'master_data_fullname' },
            { data: 'title', name: 'title' },
            {
                data: 'free_letter',
                name: 'free_letter',
                class: 'text-center',
                render: function(data, type, row) {
                    if (data >= 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                    }
                }
            }
        ]
    });
}

function initializeArchivedDataTable(jurusan, edisi,id){
    if ($.fn.DataTable.isDataTable('#table_archieved')) {
        $('#table_archieved').DataTable().clear().destroy();
    }

    const columns = [
        { data: 'master_data_user', name: 'master_data_user' },
        { data: 'master_data_fullname', name: 'master_data_fullname' },
        { data: 'title', name: 'title' }
    ];

    $('#table_archieved').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        // responsive: false,
        // scrollX: true,
        ajax: {
            url: url + '/dt_detail_archived',
            type: 'POST',
            data: {
                jurusan: jurusan,
                edisi: edisi,
                id: id,
                _token: '{{ csrf_token() }}' // Include CSRF token if needed
            }
        },
        columns: columns
    });
}

function initializeArchivedPublishDataTable(jurusan, edisi){
    if ($.fn.DataTable.isDataTable('#table_archievedjournalpublish')) {
        $('#table_archievedjournalpublish').DataTable().clear().destroy();
    }

    const columns = [
        { data: 'master_data_user', name: 'master_data_user' },
        { data: 'master_data_fullname', name: 'master_data_fullname' },
        { data: 'code', name: 'code' },
        { data: 'title', name: 'title' },
    ];

    $('#table_archievedjournalpublish').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        // responsive: false,
        // scrollX: true,
        ajax: {
            url: url + '/dt_detail_archievedjournalpublish',
            type: 'POST',
            data: {
                jurusan: jurusan,
                edisi: edisi,
                _token: '{{ csrf_token() }}' // Include CSRF token if needed
            }
        },
        columns: columns
    });
}

</script>
@endsection