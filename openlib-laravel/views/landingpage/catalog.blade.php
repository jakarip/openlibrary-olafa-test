@extends('layouts/layoutLandingpage')

@section('title', 'Login')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('vendor-script')
@endsection

@section('content')
<section style="background:url('{{ asset('assets/img/landingpage/bg-catalog.jpg') }}'); background-repeat:no-repeat; background-size: 100%; padding-bottom:0px; ">
    <div class="container" style="top:3rem; position: relative;">
        <div class="card card-body">
        <h1 class="text-center fw-bold">Pencarian Katalog</h1>
            <form name="filter" id="filter" method="get" action="">
                <input type="hidden" name="page" id="page" value="{{ $request->page }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="input-group input-group-merge input-group-lg">
                        <span class="input-group-text" id="basic-addon-search31" style="border:0px"><i class="ti ti-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="judul, kode, pengarang, penerbit, subyek dan jenis" style="border:0px">
                    </div>
                    <div class="input-group input-group-merge input-group-lg" style="width:350px">
                        <span class="input-group-text px-0" style="border:0px"><i class="ti ti-sort-descending-2"></i></span>
                        <select class="form-select" name="order" id="order" style="border:0px">
                            <option value="entrance_date-desc">Sort: Terbaru</option>
                            <option value="entrance_date-asc">Sort: Terlama</option>
                            <option value="published_year-desc">Sort: Terbitan Baru</option>
                            <option value="published_year-asc">Sort: Terbitan Lama</option>
                            <option value="title-asc">Sort: Judul A-Z</option>
                            <option value="title-desc">Sort: Judul Z-A</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 mt-3" style="border-bottom:solid 1px #ccc"></div>
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <small class="text-secondary">Filter Data Saat Ini</small><br>
                        @if(!empty($request->author))
                        <div class="badge bg-label-primary">{{ $request->author }}</div>
                        <input type="hidden" name="author" id="author" value="{{ $request->author }}">
                        @endif

                        @if(!empty($request->publisher))
                        <div class="badge bg-label-primary">{{ $request->publisher }}</div>
                        <input type="hidden" name="publisher" id="publisher" value="{{ $request->publisher }}">
                        @endif

                        @if(!empty($request->subject))
                        <div class="badge bg-label-primary">{{ $request->subject }}</div>
                        <input type="hidden" name="subject" id="subject" value="{{ $request->subject }}">
                        @endif

                        @if(!empty($request->classification_code))
                        <div class="badge bg-label-primary">{{ $request->classification_code }}</div>
                        <input type="hidden" name="classification_code" id="classification_code" value="{{ $request->classification_code }}">
                        @endif

                        @if(!empty($request->knowledge_location))
                        <div class="badge bg-label-primary">{{ $request->knowledge_location }}</div>
                        <input type="hidden" name="author" id="author" value="{{ $request->knowledge_location }}">
                        @endif

                        @if(!empty($request->knowledge_type))
                        <div class="badge bg-label-primary">{{ $request->knowledge_type }}</div>
                        <input type="hidden" name="knowledge_type" id="knowledge_type" value="{{ $request->knowledge_type }}">
                        @endif

                        @if(!empty($request->published_year))
                        <div class="badge bg-label-primary">{{ $request->published_year }}</div>
                        <input type="hidden" name="published_year" id="published_year" value="{{ $request->published_year }}">
                        @endif

                        @if(!empty($request->catalog_date))
                        <div class="badge bg-label-primary">{{ $request->catalog_date }}</div>
                        <input type="hidden" name="catalog_date" id="catalog_date" value="{{ $request->catalog_date }}">
                        @endif
                    </div>
                    <div class="col-md-2">
                        <small class="text-secondary">&nbsp;</small><br>
                        <a href="javascript:filtermodal()" class="text-danger fw-semibold float-end">Advanced Search</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section style="margin-top:5rem">
    <div class="container">
        <div class="row">
            @foreach ($data as $d)
            <div class="col-md-4">
                <div class="card mb-lg-4 mb-2" style="background-color:#f5f5f5; box-shadow: none">
                    <div class="card-body d-flex justify-content-start align-items-start" style="padding:13px">
                        <img src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" class="me-4" style="width: 88px; height: auto;">
                        <div class="mb-0">
                            <div class="mb-1 fw-semibold" style="font-size: 14px; line-height: 17px; color:#286ABD">{{ strtoupper(Ycode::ellipsis($d->title,'80')) }}</div>
                            <small style="color: #333"><i class="ti ti-tags ti-sm me-2" style="font-size:18px !important"></i>{{ $d->classification->code }} - {{ $d->classification->title ?? 'Untitled' }}</small><br>
                            <small style="color: #333"><i class="ti ti-bookmark ti-sm me-2" style="font-size:18px !important"></i>{{ $d->type->name }}</small><br>
                            <small class="text-success"><i class="ti ti-discount-check-filled ti-sm me-2" style="font-size:18px !important"></i>Tersedia 0 dari 0 Koleksi</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-3 row">
            <div class="col-md-6 fw-semibold" style=" color:#286ABD">
                Menampilkan {{ $data->count() }} dari {{ number_format($data->total()) }} Total
            </div>
            <div class="col-md-6">
                {{ $data->links('layouts/sections/pagination/pagination') }}
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="get" id="form-modal" class="form-validate">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-filter me-2"></i>Pencarian Detil Katalog</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="search" id="search" value="{{ $request->search }}">
                <input type="hidden" name="order" id="order" value="{{ $request->order }}">
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Pengarang</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="author" id="author">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Penerbit</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="publisher" id="publisher">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Subject</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="subject" id="subject">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Kode Klasifikasi</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="classification_code" id="classification_code">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Lokasi</label>
                    <div class="col-md-9">
                        {{ Form::select('knowledge_location', $knowledge_location, '', ['id' => 'knowledge_location', 'class' => 'form-select']) }}
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Jenis Katalog</label>
                    <div class="col-md-9">
                        {{ Form::select('knowledge_type', $knowledge_type, '', ['id' => 'knowledge_type', 'class' => 'form-select']) }}
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Tahun Terbit</label>
                    <div class="col-md-9">
                        {{ Form::select('published_year', $published_year, '', ['id' => 'published_year', 'class' => 'form-select']) }}
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label">Tanggal Masuk</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control drp" name="catalog_date" id="catalog_date" value="" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Bersihkan</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-filter ti-sm me-2"></i>Cari</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script language="JavaScript">
$(function() {
    $('#search').keypress(function(e) {
        if (e.which == 13) {
            submitform();
        }
    });
});
function submitform() {
    $('form').submit();
}
function filtermodal() {
    $("#frmbox").modal('show');
}
</script>
@endsection
