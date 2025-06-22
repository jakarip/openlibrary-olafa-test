@extends('layouts/layoutLandingpage')

@section('title', 'Login')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
<section class="my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <a href="#">
                    <img class="img-fluid" src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" alt="tutor image 1" style="border-radius:15px">
                </a>
            </div>
            <div class="col-md-10">
                <h3 class="fw-bold">Lancar Kuasai 16 Tenses Cara Mudah Paham 16 Tenses Tanpa Banyak Mikir</h3>
                <div class="row mb-5">
                    <div class="col-md-3 d-flex flex-wrap">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/3.png" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="ms-1">
                            <span class="text-muted">Pengarang</span>
                            <h6 class="mb-0 fw-bold">Lester McCarthy (Client)</h6>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex flex-wrap">
                        <div class="ms-1">
                            <span class="text-muted">Kode</span>
                            <h6 class="mb-0 fw-bold">23.01.1060</h6>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex flex-wrap">
                        <div class="ms-1">
                            <span class="text-muted">Klasifikasi</span>
                            <h6 class="mb-0 fw-bold">000 - General Works</h6>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex flex-wrap">
                        <div class="ms-1">
                            <span class="text-muted">Jenis</span>
                            <h6 class="mb-0 fw-bold">Buku - Circulation (Dapat Dipinjam)</h6>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex flex-wrap">
                        <div class="ms-1">
                            <span class="text-muted">Ketersediaan</span>
                            <h6 class="mb-0 fw-bold"><span class="fw-bold text-primary">1</span> Tersedia dari <span class="fw-bold text-primary">16</span> Eksemplar</h6>
                        </div>
                    </div>
                </div>
                <div class="text-justify">
                    <p class="text-justify">Dalam sebuah bahasa, terdapat aturan-aturan yang perlu diketahui agar dapat berkomunikasi menggunakan bahasa tersebut. Bahasa Inggris memiliki aturan tenses yang perlu diketahui oleh para pembelajar bahasa asing ini. Tenses sendiri merupakan perubahan bentuk kata kerja berdasarkan keterangan waktu dan sifat kegiatan.</p>
                    <p class="text-justify">Buku Lancar Kuasai 16 Tenses: Cara Mudah Paham 16 Tenses Tanpa Banyak Mikir ini memuat materi pembahasan 16 tenses yang dapat dijadikan acuan belajar. Penulis menyajikan materi dengan jelas dan sederhana sehingga akan mudah dipahami oleh pembelajar. Pembelajar dapat memahami tenses dalam 16 hari dan dapat melakukan latihan evaluasi dengan soal yang telah disediakan di buku ini.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5 list-group list-group-horizontal-md">
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-user-star ti-md ms-n1 me-2"></i> Pengarang
                </h5>

                <div class="row">
                    <div class="col-md-3">Pengarang</div>
                    <div class="col-md-9 fw-semibold">Setiawan Agung Pamungkas</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Jenis</div>
                    <div class="col-md-9 fw-semibold">Perorangan</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Penyunting</div>
                    <div class="col-md-9 fw-semibold">-</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Penerjemah</div>
                    <div class="col-md-9 fw-semibold">-</div>
                </div>
            </div>
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-books ti-md ms-n1 me-2"></i> Informasi Buku
                </h5>

                <div class="row">
                    <div class="col-md-3">ISBN</div>
                    <div class="col-md-9 fw-semibold">978-623-7898-73-3</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Kolasi</div>
                    <div class="col-md-9 fw-semibold">220p.: pdf file.; 7 MB</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Bahasa</div>
                    <div class="col-md-9 fw-semibold">Indonesia</div>
                </div>
            </div>
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-category ti-md ms-n1 me-2"></i> Kategori
                </h5>

                <div class="row">
                    <div class="col-md-3">Klasifikasi</div>
                    <div class="col-md-9 fw-semibold">621.367 - Technological photography and photo-optics, Spectrography, Stroboscopic photography, Image processing, Optical data processing</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Subject</div>
                    <div class="col-md-9 fw-semibold">GEOGRAPHICAL INFORMATION SYSTEMS</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Alt Subject</div>
                    <div class="col-md-9 fw-semibold">-</div>
                </div>
            </div>
        </div>
        <div class="row mt-3 list-group list-group-horizontal-md">
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-speakerphone ti-md ms-n1 me-2"></i> Penerbit
                </h5>

                <div class="row">
                    <div class="col-md-3">Penerbit</div>
                    <div class="col-md-9 fw-semibold">C-Klik Media</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Kota</div>
                    <div class="col-md-9 fw-semibold">Yogyakarta</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Tahun</div>
                    <div class="col-md-9 fw-semibold">2023</div>
                </div>
            </div>
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-bookmarks ti-md ms-n1 me-2"></i> Sirkulasi
                </h5>

                <div class="row">
                    <div class="col-md-3">Harga sewa</div>
                    <div class="col-md-9 fw-semibold">IDR 0,00</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Denda Harian</div>
                    <div class="col-md-9 fw-semibold">IDR 1.000,00</div>
                </div>
                <div class="row">
                    <div class="col-md-3">Jenis</div>
                    <div class="col-md-9 fw-semibold">Sirkulasi</div>
                </div>
            </div>
            <div class="col-md-4 list-group-item p-4 text-heading">
                <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                    <i class="ti ti-map-pin ti-md ms-n1 me-2"></i> Lokasi Buku
                </h5>

                <div>
                    <i class="ti ti-building-bank ti-sm me-3"></i> TelU - Gedung FKB Lantai 4
                </div>
                <div>
                    <div><i class="ti ti-sm me-3 ti-phone-call"></i> 081280000110</div>
                </div>
                <div>
                    <div><i class="ti ti-sm me-3 ti-mail"></i> library@telkomuniversity.ac.id</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 pricing-free-trial mt-5" style="background: url('{{ asset('assets/img/landingpage/bg-banner-01.png')}}'); background-size:cover; border-radius:20px">
                <div class="position-relative">
                    <div class="d-flex justify-content-between flex-column-reverse flex-lg-row align-items-center py-4 px-3">
                        <div class="text-center text-lg-start me-5 ms-3">
                            <h4 class="text-danger mb-1 fw-bold">Download File E-Book / Flippingbook</h4>
                            <p class="mb-1">Gunakan e-book ini hanya untuk tujuan pribadi atau profesional sesuai dengan lisensi atau perjanjian yang berlaku. Hindari penggunaan yang tidak sah. Jangan pernah membagikan e-book ini dengan orang lain yang tidak memiliki izin resmi untuk mengaksesnya. Ini adalah pelanggaran hak cipta dan dapat menyebabkan masalah hukum.</p>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="card card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-file-text ti-md"></i></span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0">E-Book (e-book.pdf)</h6>
                                                <small class="text-truncate text-muted">diunduh 4 kali</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- image -->
                        <div class="text-center ms-5">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/illustrations/girl-sitting-with-laptop.png" class="img-fluid" alt="Api Key Image" width="500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="text-danger mb-1 fw-bold">Download File E-Book / Flippingbook</h4>
                <p class="mb-1">Gunakan e-book ini hanya untuk tujuan pribadi atau profesional sesuai dengan lisensi atau perjanjian yang berlaku. Hindari penggunaan yang tidak sah. Jangan pernah membagikan e-book ini dengan orang lain yang tidak memiliki izin resmi untuk mengaksesnya. Ini adalah pelanggaran hak cipta dan dapat menyebabkan masalah hukum.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4 h-75">
                    <div class="card-body d-flex justify-content-between align-items-top">
                        <div class="mb-0 me-3">
                            <div class="text-body fw-semibold mb-0">C. Disclaimer (Pernyataan Orisinalitas) yang sudah bertandatangan. (File discan agar document jelas dan rapi) (disclaimer.pdf)</div>
                            <small class="text-muted">belum pernah diunduh</small>
                        </div>
                        <div class="card-icon">
                            <i class="ti ti-file-text ti-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4 h-75">
                    <div class="card-body d-flex justify-content-between align-items-top">
                        <div class="mb-0 me-3">
                            <div class="text-body fw-semibold mb-0">I. Daftar Isi (daftarisi.pdf)</div>
                            <small class="text-muted">belum pernah diunduh</small>
                        </div>
                        <div class="card-icon">
                            <i class="ti ti-file-text ti-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
