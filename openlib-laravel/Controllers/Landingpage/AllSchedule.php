<?php
namespace App\Http\Controllers\Landingpage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemLocation;
use App\Models\ItemLocationHour;
use App\Models\Holiday;
use Carbon\Carbon;

class AllSchedule extends Controller
{
    public function allSchedules(Request $request)
    {
        $currentDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $startDate = $currentDate->copy()->startOfWeek();

        $prevWeek = $startDate->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $startDate->copy()->addWeek()->format('Y-m-d');

        // PERBAIKAN: Hanya ambil yang show_as_footer = 1 dan order by orderby
        $locations = ItemLocation::active()->ordered()->get();

        $dates = [];
        $libraries = [];

        // Generate 7 hari dalam seminggu
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dates[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('l'),
                'day_short' => $date->format('D'),
                'full_date' => $date->format('Y-m-d'),
                'is_today' => $date->isToday()
            ];
        }

        $dayMap = [
            'sun' => 'open_sun',
            'mon' => 'open_mon',
            'tue' => 'open_tue',
            'wed' => 'open_wed',
            'thu' => 'open_thu',
            'fri' => 'open_fri',
            'sat' => 'open_sat',
        ];

        foreach ($locations as $loc) {
            $schedules = [];

            foreach ($dates as $date) {
                $currentDate = $date['full_date'];
                $dayName = strtolower(Carbon::parse($currentDate)->format('D'));
                $dayColumn = $dayMap[$dayName];

                // Cek apakah hari libur
                $isHoliday = $loc->holidays()->where('holiday_date', $currentDate)->exists();

                if ($isHoliday) {
                    $hours = 'Tutup';
                    $status = 'holiday';
                } else {
                    // Cek jam khusus
                    $special = $loc->specialHours()->where('ilh_date', $currentDate)->first();

                    if ($special) {
                        $hours = $special->ilh_hour;
                        $status = 'special';
                    } else {
                        $value = $loc->$dayColumn;

                        if ($value === null || $value === "" || $value === "-" || $value === "00:00 - 00:00") {
                            $hours = 'Tutup';
                            $status = 'closed';
                        } else {
                            $hours = $value;
                            $status = 'open';
                        }
                    }
                }

                $schedules[] = [
                    'hours' => $hours,
                    'status' => $status,
                    'is_today' => $date['is_today']
                ];
            }

            $libraries[] = [
                'name' => $loc->name,
                'schedules' => $schedules
            ];
        }

        // Week range untuk display
        $weekRange = $startDate->format('M d') . ' - ' . $startDate->copy()->endOfWeek()->format('M d, Y');

        return view('landingpage.schedule', compact('dates', 'libraries', 'prevWeek', 'nextWeek', 'weekRange'));
    }
}