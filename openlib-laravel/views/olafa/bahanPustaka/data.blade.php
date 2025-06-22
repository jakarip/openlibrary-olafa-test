@extends('layouts/layoutMaster')

@section('title',  __('olafas.library_materials.page.title'))

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
                        <label for="fakultas" class="form-label">{{ __('olafas.library_materials.select_faculty') }}</label>
                        <select id="fakultas" class="form-select form-select-md select2">
                            <option value="8" selected >FAKULTAS EKONOMI DAN BISNIS</option>
                            <option value="9">FAKULTAS KOMUNIKASI DAN BISNIS</option>
                            <option value="3">FAKULTAS ILMU TERAPAN</option>
                            <option value="4">FAKULTAS INDUSTRI KREATIF</option>
                            <option value="5">FAKULTAS TEKNIK ELEKTRO</option>
                            <option value="6">FAKULTAS REKAYASA INDUSTRI</option>
                            <option value="7">FAKULTAS INFORMATIKA</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="tahun" class="form-label">{{ __('olafas.library_materials.select_year') }}</label>
                        <select id="tahun" class="form-select form-select-md select2">
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024" selected >2024</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="kurikulum" class="form-label">{{ __('olafas.library_materials.select_curriculum_year') }}</label>
                        <select id="kurikulum" class="form-select form-select-md select2">
                            <option value="2000">2000</option>
                            <option value="2004">2004</option>
                            <option value="2008">2008</option>
                            <option value="2012">2012</option>
                            <option value="2016">2016</option>
                            <option value="2020" selected>2020</option>
                        </select>
                    </div>
                    </div>
                </div>
                </div>
            </form>
        </div>
        <hr class="mt-0">
        <div class="card-datatable table-responsive" >
            <table class="table  dataTable dt-select-no-highlight nowrap" id="table">
                <thead>
                    <tr>
                        <th width="">{{ __('common.action') }}</th>
                        <th>{{ __('olafas.library_materials.faculty') }}</th>
                        <th>{{ __('olafas.library_materials.study_program') }}</th>
                        <th>{{ __('olafas.library_materials.printed_book_title') }}</th>
                        <th>{{ __('olafas.library_materials.printed_book_copy') }}</th>
                        <th>{{ __('olafas.library_materials.ebook_title') }}</th>
                        <th>{{ __('olafas.library_materials.ebook_copy') }}</th>
                        <th>{{ __('olafas.library_materials.all_titles') }}</th>
                        <th>{{ __('olafas.library_materials.all_copies') }}</th>
                        <th>{{ __('olafas.library_materials.subject') }}</th>
                        <th>{{ __('olafas.library_materials.subject_with_book') }}</th>
                        <th width="">{{ __('common.percentage') }}</th> 
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th colspan="2" align="center"><b>{{ __('common.total') }}</b></th>
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
                </tfoot>
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
let url = '{{ url('olafa/bahan-pustaka/') }}';


$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.year = $('#kurikulum').val(); //harus refactor nama 
                d.grow_year = $('#tahun').val(); //harus refactor nama
                d.faculty = $('#fakultas').val();
            },
            dataSrc: function(json) {
                $('#total_judul_fisik').html('<b>' + json.total[0].judul_fisik + ' {{ __("olafas.library_materials.printed_book_title") }}</b>');
                $('#total_eks_fisik').html('<b>' + json.total[0].eks_fisik + ' {{ __("olafas.library_materials.printed_book_copy") }}</b>');
                $('#total_judul').html('<b>' + json.total[0].judul + ' {{ __("olafas.library_materials.ebook_title") }}</b>');
                $('#total_eks').html('<b>' + json.total[0].eks + ' {{ __("olafas.library_materials.ebook_copy") }}</b>');
                $('#total_semua_judul').html('<b>' + (parseInt(json.total[0].judul_fisik) + parseInt(json.total[0].judul)) + ' {{ __("olafas.library_materials.all_titles") }}</b>');
                $('#total_semua_eks').html('<b>' + (parseInt(json.total[0].eks_fisik) + parseInt(json.total[0].eks)) + ' {{ __("olafas.library_materials.all_copies") }}</b>');
                $('#total_mk').html('<b>' + json.total[0].mk + ' {{ __("olafas.library_materials.subject") }}</b>');
                $('#total_mkadabuku').html('<b>' + json.total[0].mkadabuku + ' {{ __("olafas.library_materials.subject_with_book") }}</b>');
                return json.data;
            }
        },
        columns: [
            { data: 'action', name: 'action', class: 'text-center' },
            { data: 'nama_fakultas', name: 'nama_fakultas', },
            { data: 'nama_prodi', name: 'nama_prodi', },
            { data: 'judul_fisik', name: 'judul_fisik', },
            { data: 'eks_fisik', name: 'eks_fisik', },
            { data: 'judul', name: 'judul', },
            { data: 'eks', name: 'eks', },
            { data: 'total_judul', name: 'total_judul', },
            { data: 'total_eks', name: 'total_eks', },
            { data: 'mk', name: 'mk', },
            { data: 'mkadabuku', name: 'mkadabuku', },
            { data: 'percentage', name: 'percentage', }
        ],
        responsive: false,
        scrollX: true,
        
    });
    // Reload DataTables when form inputs change
    $('#fakultas, #tahun, #kurikulum').on('change', function() {
        dTable.ajax.reload();
    });

});

</script>
@endsection