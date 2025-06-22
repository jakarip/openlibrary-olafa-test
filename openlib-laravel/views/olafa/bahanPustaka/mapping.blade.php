@extends('layouts/layoutMaster')

@section('title', 'Mapping Katalog')

@section('vendor-style')
@endsection

@section('page-style')
<style>
.highcharts-credits,
.highcharts-button {
    display: none;
}
</style>
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
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="searchtype" class="form-label">Pencarian Berdasarkan:</label>
                        <select name="searchtype" id="searchtype" class="select2 form-select form-select-md">
                            <option value="all">Semua</option>
                            <option value="title">Judul</option>
                            <option value="subject">Subjek</option>
                            <option value="author">Pengarang</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="tipe" class="form-label">Pilih Jenis:</label>
                        <select id="tipe" class="select2 form-select form-select-md">
                            @if(isset($tipe))
                                    @foreach($tipe as $row)
                                        <option value="{{ $row->id }}" {{ $row->id == '1' ? 'selected' : '' }}>{{ $row->name }}</option>
                                    @endforeach
                            @endif
                        </select>
                    </div>

                </div>
            </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card-datatable" >
    <table class="dt-scrollableTable table" id="table">
            <thead>
                <tr>
                    <th width="5%">{{ __('common.action') }}</th>
                    <th width="15%">Jenis</th> 
                    <th width="10%">Kode</th> 
                    <th width="20%">Judul</th> 
                    <th width="15%">Klasifikasi</th> 
                    <th width="15%">Subjek</th> 
                    <th width="15%">Pengarang</th> 
                    <th width="5%">Tahun</th> 
                    <th width="10%">Total Mapping</th> 
                </tr>
            </thead>
            <tbody>

            </tbody>
            {{-- <tfoot>
                <tr>
                    <th colspan="2" align="center"><b>TOTAL</b></th>
                    <th id="total_judul_fisik" align="right"></th>
                    <th id="total_eks_fisik" align="right"></th>
                    <th id="total_judul" align="right"></th>
                    <th id="total_eks" align="right"></th>
                    <th id="total_semua_judul" align="right"></th>
                    <th id="total_semua_eks" align="right"></th>
                    <th id="total_mk" align="right"></th>
                    <th id="total_mkadabuku" align="right"></th>
                    <th></th>
                </tr>
            </tfoot> --}}
    </table>
    </div>
</div>


@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('olafa/bahan-pustaka-mapping/') }}';


$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.tipe = $('#tipe').val(); 
                d.searchtype = $('#searchtype').val(); 
            },
            // dataSrc: function(json) {
            //     $('#total_judul_fisik').html('<b>' + json.total[0].judul_fisik + ' Judul</b>');
            //     $('#total_eks_fisik').html('<b>' + json.total[0].eks_fisik + ' Eksemplar</b>');
            //     $('#total_judul').html('<b>' + json.total[0].judul + ' Judul</b>');
            //     $('#total_eks').html('<b>' + json.total[0].eks + ' Eksemplar</b>');
            //     $('#total_semua_judul').html('<b>' + (parseInt(json.total[0].judul_fisik) + parseInt(json.total[0].judul)) + ' Judul</b>');
            //     $('#total_semua_eks').html('<b>' + (parseInt(json.total[0].eks_fisik) + parseInt(json.total[0].eks)) + ' Eksemplar</b>');
            //     $('#total_mk').html('<b>' + json.total[0].mk + ' Matakuliah</b>');
            //     $('#total_mkadabuku').html('<b>' + json.total[0].mkadabuku + ' Matakuliah</b>');
            //     return json.data;
            // }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            { data: 'tipe', name: 'tipe',orderable: true, searchable: true, },
            { data: 'codes', name: 'codes',orderable: true, searchable: true, },
            { data: 'title', name: 'title' ,orderable: true, searchable: true,},
            { data: 'klasifikasi', name: 'klasifikasi' ,orderable: true, searchable: true,},
            { data: 'subjek', name: 'subjek' ,orderable: true, searchable: true,},
            { data: 'author', name: 'author' ,orderable: true, searchable: true,},
            { data: 'published_year', name: 'published_year' },
            { data: 'total', name: 'total' },
        ],
    });
    // Reload DataTables when form inputs change
    $('#tipe,#searchtype').on('change', function() {
        dTable.ajax.reload();
    });
});

</script>
@endsection