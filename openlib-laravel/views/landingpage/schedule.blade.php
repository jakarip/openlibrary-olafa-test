@extends('layouts/layoutMaster')

@section('title', __('Jam Operasional'))

@section('vendor-style')
@endsection

@section('page-style')
    <style>
        .schedule-table {
            border: 1px solid #CACACA;
            border-radius: 10px;
            overflow: hidden;
        }

        .schedule-table th {
            background-color: #FFFFFF;
            font-weight: 600;
            border: none;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            text-align: center;
            color: #000000;
            border-bottom: 2px solid #CACACA;
        }

        .schedule-table td {
            border: none;
            border-bottom: 1px solid #CACACA;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            text-align: center;
            background-color: #FFFFFF;
        }

        .schedule-table tbody tr:last-child td {
            border-bottom: none;
        }

        .library-name {
            text-align: left !important;
            font-weight: 600;
            color: #000000;
        }

        .schedule-cell {
            font-size: 0.875rem;
            font-weight: 500;
            color: #000000;
        }

        .schedule-open {
            color: #000000;
            font-weight: 600;
        }

        .schedule-closed {
            color: #B61614;
            font-weight: 600;
        }

        .schedule-special {
            color: #FFA500;
            font-weight: 600;
        }

        .schedule-holiday {
            color: #B61614;
            font-weight: 600;
        }

        .today-highlight {
            background-color: #FFFFFF;
            border-left: 3px solid #B61614;
            border-right: 3px solid #B61614;
        }

        .today-header {
            background-color: #B61614 !important;
            color: #FFFFFF !important;
        }

        .week-navigation {
            background-color: #9F1521;
            border-radius: 10px;
            color: #FFFFFF;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .week-navigation h4 {
            color: #FFFFFF !important;
        }

        .btn-week-nav {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-week-nav:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        .schedule-legend {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #000000;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .legend-dot.open {
            background-color: #000000;
        }

        .legend-dot.special {
            background-color: #FFA500;
        }

        .legend-dot.closed {
            background-color: #B61614;
        }

        .legend-dot.holiday {
            background-color: #B61614;
        }

        @media (max-width: 768px) {

            .schedule-table th,
            .schedule-table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
            }

            .library-name {
                font-size: 0.875rem;
            }

            .schedule-legend {
                justify-content: flex-start;
                gap: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="week-navigation">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="ti ti-calendar-time me-2"></i>Jam Operasional Perpustakaan
                    </h4>
                    <p class="mb-0 opacity-75">{{ $weekRange }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('landingpage.schedule', ['date' => $prevWeek]) }}" class="btn btn-week-nav">
                        <i class="ti ti-chevron-left me-1"></i>Previous Week
                    </a>
                    <a href="{{ route('landingpage.schedule', ['date' => $nextWeek]) }}" class="btn btn-week-nav">
                        Next Week<i class="ti ti-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Schedule Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table schedule-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Lokasi Perpustakaan</th>
                                @foreach($dates as $date)
                                    <th class="{{ $date['is_today'] ? 'today-header' : '' }}">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $date['day_short'] }}</span>
                                            <small class="opacity-75">{{ $date['date'] }}</small>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($libraries as $library)
                                <tr>
                                    <td class="library-name">{{ $library['name'] }}</td>
                                    @foreach($library['schedules'] as $schedule)
                                        <td class="schedule-cell {{ $schedule['is_today'] ? 'today-highlight' : '' }}">
                                            <span class="schedule-{{ $schedule['status'] }}">
                                                {{ $schedule['hours'] }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="schedule-legend">
            <div class="legend-item">
                <div class="legend-dot open"></div>
                <span>Buka Normal</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot special"></div>
                <span>Jam Khusus</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot closed"></div>
                <span>Tutup</span>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // Smooth scroll animation untuk navigasi
            $('.btn-week-nav').on('click', function (e) {
                $('body').addClass('loading');
            });

            // Auto scroll ke hari ini jika dalam viewport
            const todayCell = $('.today-highlight');
            if (todayCell.length && $(window).width() < 768) {
                todayCell[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'center'
                });
            }
        });
    </script>
@endsection