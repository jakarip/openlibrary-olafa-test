@extends('layouts/layoutLandingpage')

@section('title', 'Login')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
$(function() {
    const swiper = new Swiper('#swiper-banner', {
        direction: 'horizontal',
        loop: true,
        slidesPerView:'auto',
        pagination:{
            clickable:!0,
            el:'.swiper-pagination'
        },
        navigation:{
            prevEl:".swiper-button-prev",
            nextEl:".swiper-button-next"
        }
    });
    const swiperbs = new Swiper('#swiper-best-seller', {
        direction: 'horizontal',
        loop: true,
        slidesPerView:'auto',
        pagination:{
            clickable:!0,
            el:'.sp-bs'
        },
        navigation:{
            prevEl:".sbp-bs",
            nextEl:".sbn-bs"
        }
    });

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

@section('content')
<style>
    .bbg {
        width: 150%;
        height: 150%;
        border-radius:20px;
        position: absolute;
        background: url('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg'); background-repeat: no-repeat; background-size: 100%;
        filter: blur(18px)
    }
    #swiper-best-seller .swiper-slide {
        height: auto !important;
        width: 200px !important;
    }
    #swiper-best-seller {
        width: 200px !important;
    }
    /*.bbg::before {
        background: url('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg'); background-repeat: no-repeat; background-size: 100%; width:100%; height:500px; border-radius:20px; text-align: center; background-position: center;
        content: '';
    }*/
</style>
<!-- Content: Section 1 Slider: Start -->

<section class="my-4" style="background:url('{{ asset('assets/img/landingpage/bg-catalog.jpg') }}'); background-repeat:no-repeat; background-size: 100%; padding-bottom:0px; ">
    <div class="container my-4" style="top:3rem; position: relative;">
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
<section class="my-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="swiper" id="swiper-banner" style="border-radius:20px; height:500px;">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" style="background-image:url({{ url('assets/img/banner/4.png') }}); background-size:cover"></div>
                        <div class="swiper-slide" style="background-image:url({{ url('assets/img/banner/1.png') }}); background-size:cover"></div>
                        <div class="swiper-slide" style="background-image:url({{ url('assets/img/banner/2.png') }}); background-size:cover"></div>
                        <div class="swiper-slide" style="background-image:url({{ url('assets/img/banner/3.png') }}); background-size:cover"></div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next swiper-button-white custom-icon"></div>
                    <div class="swiper-button-prev swiper-button-white custom-icon"></div>
                </div>
                <!--<div class="swiper-container swiper-container-horizontal swiper card" id="swiper-with-pagination-cards" style="border-radius:20px">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" style="background: url('{{ asset('assets/img/banner/1.jpg')}}'); background-repeat: no-repeat; background-size: 100%; height:500px; border-radius:20px;">1</div>
                    </div>
                </div>-->
            </div>
            <div class="col-lg-4">
                <div class="card" style="border-radius:20px;">
                    <div style="background-color: #2B5574; text-align: center; width:100%; height:500px; border-radius:20px">
                        <div class="card-body" style="position: relative;">
                            <h1 class="fw-bolder mb-0" style="color:#fff">Best Seller</h1>
                            <div class="mb-5" style="color:#fff">Based borrowed this month</div>
                            <!--<img src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:20px; border:solid 3px #fff" height="300">-->
                            <div class="swiper" id="swiper-best-seller">
                                <div class="swiper-wrapper">
                                    <img class="swiper-slide" src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:20px; border:solid 3px #fff">
                                    <img class="swiper-slide" src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:20px; border:solid 3px #fff">
                                    <img class="swiper-slide" src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:20px; border:solid 3px #fff">
                                </div>
                            </div>
                            <div class="swiper-pagination sp-bs"></div>
                            <div class="swiper-button-next sbn-bs swiper-button-white custom-icon"></div>
                            <div class="swiper-button-prev sbp-bs swiper-button-white custom-icon"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Content: Section 1 Slider: End -->

<!-- Content: Section 2 Services: Start -->
<section class="my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <a href="https://openlibrary.telkomuniversity.ac.id/home/information/id/53.html" class="">
                    <div class="d-flex align-items-start">
                        <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-books"></i></div>
                        <div class="">
                            <h5 class="mb-0 fw-bold">E-Catalogue</h5>
                            <small class="text-muted">Download koleksi katalog terbaru</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="https://openlibrary.telkomuniversity.ac.id/room/" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-calendar-pin"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Room Reservations</h5>
                        <small class="text-muted">Discussion room reservations</small>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-user-question"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Ask Librarian</h5>
                        <small class="text-muted">Feel free to contact us</small>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-help-hexagon"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Library FAQ</h5>
                        <small class="text-muted">Open library frequently ask questions</small>
                    </div>
                </div>
                </a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-3">
                <a href="#" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-file-text"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Karya Ilmiah Online</h5>
                        <small class="text-muted">Akses online karya ilmiah mahasiswa</small>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="https://bit.ly/PermohonanKelasLiterasiOpenlib" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-calendar-time"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Kelas Literasi</h5>
                        <small class="text-muted">Pengajuan jadwal kelas literasi</small>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="https://bit.ly/AbsenKelasLiteasi" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-user-check"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Presensi Kelas Literasi</h5>
                        <small class="text-muted">Daftar hadir kelas literasi</small>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="https://bit.ly/PermohonanBimbinganPemustaka" class="">
                <div class="d-flex align-items-start">
                    <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-message-2-star"></i></div>
                    <div class="">
                        <h5 class="mb-0 fw-bold">Bimbingan Pemustaka</h5>
                        <small class="text-muted">Pengajuan bimbingan pemustaka</small>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Content: Section 2 Services: End -->

<!-- Content: Section 3 New Books: Start -->
<section class="my-5 py-5" style="background:url('{{ asset('assets/img/landingpage/bg-featured-books.png') }}'); background-repeat:no-repeat; background-size: 100%">
    <div class="container">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-3">
                <h5 class="mb-0">Populer Books</h5>
                <small class="text-muted">Discover the popular books (most frequently borrowed) to read that are trending right now.</small>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                </div>
            </div>
            <!-- </div> -->
        </div>
        <div class="row">
            @foreach($popular as $p)
            <div class="col-md-2">
                <div class="badge bg-danger" style="position: absolute;margin-top: 13%;border-radius: 0px 5px 5px 0px;font-size: 13px;">
                    <i class="ti ti-calendar me-1"></i>
                    <span class="mt-n1">{{ date('m/Y', strtotime($p->tgl)) }}</span>
                </div>
                <img src="{{ @getimagesize('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/'.$p->cover) ? 'https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/'.$p->cover : url('assets/img/default-book-cover.jpg') }}" style="border-radius:15px; width:200px">
                <h6 class="mt-3 fw-bold" style="min-height: 61px">{{ $p->title }}</h6>
                <div class="d-flex align-items-center fw-bold" style="color:#7D70DA"><i class="ti ti-clipboard-check me-1"></i>Tersedia {{ $p->available }} koleksi</div>
                <div class="d-flex align-items-center fw-bold" style="color:#F67546"><i class="ti ti-bookmarks me-1"></i><small>{{ $p->cat }}</small></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Content: Section 3 New Books: End -->

<!-- Content: Section 3 New Books: Start -->
<section class="my-5 py-5">
    <div class="container">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="mb-0">Earning Reports</h5>
                <small class="text-muted">Weekly Earnings Overview</small>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                </div>
            </div>
            <!-- </div> -->
        </div>
        <div class="row">
            @for($i=1; $i<=6; $i++)
            <div class="col-md-2">
                <div class="badge bg-danger" style="position: absolute;margin-top: 30px;border-radius: 0px 5px 5px 0px;font-size: 13px;">
                    <i class="ti ti-calendar me-1"></i>
                    <span class="mt-n1">08/23</span>
                </div>
                <img src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:15px; width:200px">
                <h6 class="mt-3 fw-bold">Calculus: Early Transcendental Functions, International Metric Edition, 8th Edition</h6>
                <div class="d-flex align-items-center fw-bold" style="color:#7D70DA"><i class="ti ti-clipboard-check me-1"></i>Tersedia 1 koleksi</div>
                <div class="d-flex align-items-center fw-bold" style="color:#F67546"><i class="ti ti-bookmarks me-1"></i><small>Buku - Circulation (Dapat Dipinjam)</small></div>
            </div>
            @endfor
        </div>
    </div>
</section>
<!-- Content: Section 3 New Books: End -->

<!-- Content: Section 3 Books: Start -->
<section class="my-5">
    <div class="container">
        <div class="card-header pb-0 d-flex justify-content-between mb-lg-n4">
            <div class="card-title mb-0">
                <h5 class="mb-0">Latest News</h5>
                <small class="text-muted">Weekly Earnings Overview</small>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                </div>
            </div>
            <!-- </div> -->
        </div>
        <div class="row">
            @for($i=1; $i<=4; $i++)
            <div class="col-md-3">
                <div class="rounded-2 text-center mb-3">
                    <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/academy/course-details">
                        <img class="img-fluid" src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/pages/app-academy-tutor-1.png" alt="tutor image 1">
                    </a>
                </div>
                <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/academy/course-details" class="h6 fw-bold">Why reading is important for our children?</a>
                <p class="mt-2" style="font-size:13px; line-height: 1.25; text-align: justify">lorem ipsum dolor sit amet, consectetur adipis lorem ipsum dolor sit amet, consectetur adipis lorem ipsum dolor sit amet, consectetur adipis lorem ipsum dolor sit amet, consectetur adipis</p>
            </div>
            @endfor
        </div>
    </div>
</section>
<!-- Content: Section 3 Books: End -->

<!-- Content: Section 3 Books: Start -->
<section class="mt-5 py-3" style="background-color: #f6f8fb">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center gap-3" style="margin:100px">
            <div class="text-center" style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                <div><img src="{{ asset('assets/img/icons/book.png') }}" style="width:90px"></div>
                <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">9,789</h3>
                <span class="text-muted">Jumlah Judul Buku</span>
            </div>
            <div class="text-center" style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                <div><img src="{{ asset('assets/img/icons/books.png') }}" style="width:90px"></div>
                <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">29,789</h3>
                <span class="text-muted">Jumlah Eksemplar Buku</span>
            </div>
            <div class="text-center" style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                <div><img src="{{ asset('assets/img/icons/borrow-book.png') }}" style="width:90px"></div>
                <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">139,789</h3>
                <span class="text-muted">Jumlah Peminjaman Buku</span>
            </div>
            <div class="text-center" style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                <div><img src="{{ asset('assets/img/icons/member.png') }}" style="width:90px"></div>
                <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">39,789</h3>
                <span class="text-muted">Jumlah Anggota</span>
            </div>
            <div class="text-center" style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                <div><img src="{{ asset('assets/img/icons/visit.png') }}" style="width:90px"></div>
                <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">139,789</h3>
                <span class="text-muted">Jumlah Kunjungan</span>
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
<!-- Content: Section 3 Books: End -->
@endsection

