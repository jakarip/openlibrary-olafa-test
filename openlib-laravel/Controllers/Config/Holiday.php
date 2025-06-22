<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday\HolidayRuleModel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Holiday\HolidayModel;
class Holiday extends Controller
{
    public function index()
    {
        return view('config.holiday.holiday');
    }

    public function dt(Request $request)
    {
        try {
            $query = HolidayRuleModel::query()
                ->leftJoin('item_location', 'holiday_rule.location_id', '=', 'item_location.id')
                ->select('holiday_rule.*', 'item_location.name as location_name');

            if ($request->filled('holiday_rule_id')) {
                $query->where('holiday_rule.id', $request->input('holiday_rule_id'));
            }

            if ($request->filled('name')) {
                $query->where('holiday_rule.name', 'LIKE', '%' . $request->input('name') . '%');
            }

            if ($request->filled('weekday')) {
                $query->where('holiday_rule.weekday', $request->input('weekday'));
            }

            if ($request->filled('day')) {
                $query->where('holiday_rule.day', $request->input('day'));
            }

            if ($request->filled('month')) {
                $query->where('holiday_rule.month', $request->input('month'));
            }

            if ($request->filled('year_from')) {
                $query->where('holiday_rule.year_from', $request->input('year_from'));
            }

            if ($request->filled('year_to')) {
                $query->where('holiday_rule.year_to', $request->input('year_to'));
            }

            // Add location filter
            if ($request->filled('location_id')) {
                $query->where('holiday_rule.location_id', $request->input('location_id'));
            }

            $selectedColumns = (array) $request->input('selected_columns', []);

            $dataTable = DataTables::eloquent($query)
                ->addColumn('action', function ($rule) {
                    $btn = '<div class="btn-group my-btn-group">';
                    $btn .= '<button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle" type="button" data-id="' . $rule->id . '">';
                    $btn .= '<i class="ti ti-dots-vertical"></i>';
                    $btn .= '</button>';
                    $btn .= '<ul class="dropdown-menu" style="display:none;">';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center edit-btn" href="javascript:void(0);" data-id="' . $rule->id . '">';
                    $btn .= '<i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center text-danger delete-btn" href="javascript:void(0);" data-id="' . $rule->id . '">';
                    $btn .= '<i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->addColumn('location', function ($rule) {
                    return $rule->location_name ?? 'Global';
                })
                ->rawColumns(['action']);

            if (in_array('created_by', $selectedColumns)) {
                $dataTable->addColumn('created_by', function ($rule) {
                    return $rule->created_by ?? '-';
                });
            }

            if (in_array('created_at', $selectedColumns)) {
                $dataTable->editColumn('created_at', function ($rule) {
                    return (!empty($rule->created_at) && $rule->created_at != '0000-00-00 00:00:00')
                        ? \Carbon\Carbon::parse($rule->created_at)->format('Y-m-d H:i:s')
                        : '-';
                })
                    ->filterColumn('created_at', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') LIKE ?", ["%{$keyword}%"]);
                    });
            }

            if (in_array('updated_by', $selectedColumns)) {
                $dataTable->addColumn('updated_by', function ($rule) {
                    return $rule->updated_by ?? '-';
                });
            }

            if (in_array('updated_at', $selectedColumns)) {
                $dataTable->editColumn('updated_at', function ($rule) {
                    return (!empty($rule->updated_at) && $rule->updated_at != '0000-00-00 00:00:00')
                        ? \Carbon\Carbon::parse($rule->updated_at)->format('Y-m-d H:i:s')
                        : '-';
                })
                    ->filterColumn('updated_at', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') LIKE ?", ["%{$keyword}%"]);
                    });
            }

            return $dataTable->toJson();

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Updated addHoliday function with enhanced validation
    public function addHoliday(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'weekday' => 'nullable|string',
            'day' => 'nullable|string',
            'month' => 'nullable|string',
            'year_from' => 'required|integer|min:0',
            'year_to' => 'required|integer|gte:year_from|min:0',
            'location_id' => 'nullable|integer|exists:item_location,id',
        ]);

        // Normalize day value
        if ($request->day != '*' && $request->day != '') {
            // Remove leading zeros
            $normalizedDay = ltrim($request->day, '0');

            // Error if original had more than one leading zero (e.g., "006")
            if (strlen($request->day) - strlen($normalizedDay) > 1) {
                return response()->json([
                    'message' => 'Invalid day format',
                    'error' => 'The day cannot have multiple leading zeros'
                ], 422);
            }

            // Validate the numeric value
            $dayNum = (int) $normalizedDay;
            if ($dayNum < 1 || $dayNum > 31) {
                return response()->json([
                    'message' => 'Invalid day specified',
                    'error' => 'Day must be between 1 and 31, or * for all days'
                ], 422);
            }
        } else {
            $normalizedDay = $request->day ?: '*';
        }

        // Normalize month value
        if ($request->month != '*' && $request->month != '') {
            // Remove leading zeros
            $normalizedMonth = ltrim($request->month, '0');

            // Error if original had more than one leading zero (e.g., "006")
            if (strlen($request->month) - strlen($normalizedMonth) > 1) {
                return response()->json([
                    'message' => 'Invalid month format',
                    'error' => 'The month cannot have multiple leading zeros'
                ], 422);
            }

            // Validate the numeric value
            $monthNum = (int) $normalizedMonth;
            if ($monthNum < 1 || $monthNum > 12) {
                return response()->json([
                    'message' => 'Invalid month specified',
                    'error' => 'Month must be between 1 and 12, or * for all months'
                ], 422);
            }

            // Validate day is valid for the specific month (if day is provided)
            if ($normalizedDay != '*' && $normalizedDay != '') {
                $isValid = false;

                // Check all years in range - day must be valid in at least one year
                for ($year = $request->year_from; $year <= $request->year_to; $year++) {
                    if (checkdate($monthNum, (int) $normalizedDay, $year)) {
                        $isValid = true;
                        break;
                    }
                }

                if (!$isValid) {
                    return response()->json([
                        'message' => 'Invalid date',
                        'error' => "Day $normalizedDay is not valid for month $normalizedMonth in the specified year range"
                    ], 422);
                }
            }
        } else {
            $normalizedMonth = $request->month ?: '*';
        }

        DB::beginTransaction();

        try {
            $user = auth()->user()?->master_data_user ?? 'admin';

            // Always create a new rule, no duplicate checking
            $holidayRule = HolidayRuleModel::create([
                'name' => $request->name,
                'weekday' => $request->weekday ?? '*',
                'day' => $normalizedDay,
                'month' => $normalizedMonth,
                'year_from' => $request->year_from,
                'year_to' => $request->year_to,
                'location_id' => $request->filled('location_id') ? $request->location_id : null,
                'created_by' => $user,
                'created_at' => now(),
                'updated_by' => $user,
                'updated_at' => now(),
            ]);

            // Generate holidays based on the rule
            $years = range($holidayRule->year_from, $holidayRule->year_to);
            $holidayDatesToCreate = []; // Track dates to create

            foreach ($years as $year) {
                // Determine date range based on month specification
                if ($holidayRule->month !== '*') {
                    // Handle month with or without leading zero for DateTime parsing
                    $monthFormatted = strlen($holidayRule->month) == 1 ?
                        '0' . $holidayRule->month : $holidayRule->month;

                    try {
                        // If month is specified, only look at that month
                        $start = new \DateTime("$year-$monthFormatted-01");
                        $end = clone $start;
                        $end->modify('last day of this month');
                    } catch (\Exception $e) {
                        // Handle invalid date exception
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Invalid date format',
                            'error' => $e->getMessage()
                        ], 422);
                    }
                } else {
                    // Otherwise look at entire year
                    $start = new \DateTime("$year-01-01");
                    $end = new \DateTime("$year-12-31");
                }

                // Loop through each day in the range
                $current = clone $start;
                while ($current <= $end) {
                    $shouldAdd = true;

                    // Check weekday condition
                    if ($holidayRule->weekday !== '*' && $current->format('w') != $holidayRule->weekday) {
                        $shouldAdd = false;
                    }

                    // Check day condition
                    if ($holidayRule->day !== '*' && $current->format('d') != $holidayRule->day) {
                        $shouldAdd = false;
                    }

                    // If all conditions pass, add to our list
                    if ($shouldAdd) {
                        $holidayDatesToCreate[] = $current->format('Y-m-d');
                    }

                    $current->modify('+1 day');
                }
            }

            // Delete existing holidays with the same dates (regardless of which rule they belong to)
            // But also consider location now
            if (!empty($holidayDatesToCreate)) {
                if ($request->filled('location_id')) {
                    // If location specified, only delete conflicting holidays for same location
                    HolidayModel::whereIn('holiday_date', $holidayDatesToCreate)
                        ->where(function ($query) use ($request) {
                            $query->where('location_id', $request->location_id)
                                ->orWhereNull('location_id');
                        })
                        ->delete();
                } else {
                    // If global (no location), delete all conflicting dates
                    HolidayModel::whereIn('holiday_date', $holidayDatesToCreate)->delete();
                }

                // Create new holiday entries with the new rule_id
                foreach ($holidayDatesToCreate as $holidayDate) {
                    HolidayModel::create([
                        'holiday_rule_id' => $holidayRule->id,
                        'holiday_date' => $holidayDate,
                        'location_id' => $request->filled('location_id') ? $request->location_id : null,
                        'created_by' => $user,
                        'created_at' => now(),
                        'updated_by' => $user,
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Holiday rule dan holiday berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan holiday rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Updated save function with enhanced validation
    public function save(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:holiday_rule,id',
            'name' => 'required|string',
            'weekday' => 'nullable|string',
            'day' => 'nullable|string',
            'month' => 'nullable|string',
            'year_from' => 'required|integer|min:0',
            'year_to' => 'required|integer|gte:year_from|min:0',
            'location_id' => 'nullable|integer|exists:item_location,id',
        ]);

        // Normalize day value
        if ($request->day != '*' && $request->day != '') {
            // Remove leading zeros
            $normalizedDay = ltrim($request->day, '0');

            // Error if original had more than one leading zero (e.g., "006")
            if (strlen($request->day) - strlen($normalizedDay) > 1) {
                return response()->json([
                    'message' => 'Invalid day format',
                    'error' => 'The day cannot have multiple leading zeros'
                ], 422);
            }

            // Validate the numeric value
            $dayNum = (int) $normalizedDay;
            if ($dayNum < 1 || $dayNum > 31) {
                return response()->json([
                    'message' => 'Invalid day specified',
                    'error' => 'Day must be between 1 and 31, or * for all days'
                ], 422);
            }
        } else {
            $normalizedDay = $request->day ?: '*';
        }

        // Normalize month value
        if ($request->month != '*' && $request->month != '') {
            // Remove leading zeros
            $normalizedMonth = ltrim($request->month, '0');

            // Error if original had more than one leading zero (e.g., "006")
            if (strlen($request->month) - strlen($normalizedMonth) > 1) {
                return response()->json([
                    'message' => 'Invalid month format',
                    'error' => 'The month cannot have multiple leading zeros'
                ], 422);
            }

            // Validate the numeric value
            $monthNum = (int) $normalizedMonth;
            if ($monthNum < 1 || $monthNum > 12) {
                return response()->json([
                    'message' => 'Invalid month specified',
                    'error' => 'Month must be between 1 and 12, or * for all months'
                ], 422);
            }

            // Validate day is valid for the specific month (if day is provided)
            if ($normalizedDay != '*' && $normalizedDay != '') {
                $isValid = false;

                // Check all years in range - day must be valid in at least one year
                for ($year = $request->year_from; $year <= $request->year_to; $year++) {
                    if (checkdate($monthNum, (int) $normalizedDay, $year)) {
                        $isValid = true;
                        break;
                    }
                }

                if (!$isValid) {
                    return response()->json([
                        'message' => 'Invalid date',
                        'error' => "Day $normalizedDay is not valid for month $normalizedMonth in the specified year range"
                    ], 422);
                }
            }
        } else {
            $normalizedMonth = $request->month ?: '*';
        }

        DB::beginTransaction();

        try {
            $user = auth()->user()?->master_data_user ?? 'admin';

            $holidayRule = HolidayRuleModel::findOrFail($request->id);

            // Update the rule
            $holidayRule->update([
                'name' => $request->name,
                'weekday' => $request->weekday ?? '*',
                'day' => $normalizedDay,
                'month' => $normalizedMonth,
                'year_from' => $request->year_from,
                'year_to' => $request->year_to,
                'location_id' => $request->filled('location_id') ? $request->location_id : null,
                'updated_by' => $user,
                'updated_at' => now(),
            ]);

            // Delete existing holidays for this specific rule
            HolidayModel::where('holiday_rule_id', $holidayRule->id)->delete();

            // Generate holidays based on updated rule
            $years = range($holidayRule->year_from, $holidayRule->year_to);
            $holidayDatesToCreate = []; // Track dates to create

            foreach ($years as $year) {
                // Determine date range based on month specification
                if ($holidayRule->month !== '*') {
                    // Handle month with or without leading zero for DateTime parsing
                    $monthFormatted = strlen($holidayRule->month) == 1 ?
                        '0' . $holidayRule->month : $holidayRule->month;

                    try {
                        // If month is specified, only look at that month
                        $start = new \DateTime("$year-$monthFormatted-01");
                        $end = clone $start;
                        $end->modify('last day of this month');
                    } catch (\Exception $e) {
                        // Handle invalid date exception
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Invalid date format',
                            'error' => $e->getMessage()
                        ], 422);
                    }
                } else {
                    // Otherwise look at entire year
                    $start = new \DateTime("$year-01-01");
                    $end = new \DateTime("$year-12-31");
                }

                // Loop through each day in the range
                $current = clone $start;
                while ($current <= $end) {
                    $shouldAdd = true;

                    // Check weekday condition
                    if ($holidayRule->weekday !== '*' && $current->format('w') != $holidayRule->weekday) {
                        $shouldAdd = false;
                    }

                    // Check day condition
                    if ($holidayRule->day !== '*' && $current->format('d') != $holidayRule->day) {
                        $shouldAdd = false;
                    }

                    // If all conditions pass, add to our list
                    if ($shouldAdd) {
                        $holidayDatesToCreate[] = $current->format('Y-m-d');
                    }

                    $current->modify('+1 day');
                }
            }

            // For editing, we delete only dates from other rules that would conflict
            if (!empty($holidayDatesToCreate)) {
                // Delete any holidays with the same dates that belong to different rules
                // Also consider location
                if ($request->filled('location_id')) {
                    // If location specified, only delete conflicting holidays for same location
                    HolidayModel::whereIn('holiday_date', $holidayDatesToCreate)
                        ->where('holiday_rule_id', '!=', $holidayRule->id)
                        ->where(function ($query) use ($request) {
                            $query->where('location_id', $request->location_id)
                                ->orWhereNull('location_id');
                        })
                        ->delete();
                } else {
                    // If global (no location), delete all conflicting dates
                    HolidayModel::whereIn('holiday_date', $holidayDatesToCreate)
                        ->where('holiday_rule_id', '!=', $holidayRule->id)
                        ->delete();
                }

                // Create new holiday entries with this rule_id
                foreach ($holidayDatesToCreate as $holidayDate) {
                    HolidayModel::create([
                        'holiday_rule_id' => $holidayRule->id,
                        'holiday_date' => $holidayDate,
                        'location_id' => $request->filled('location_id') ? $request->location_id : null,
                        'created_by' => $user,
                        'created_at' => now(),
                        'updated_by' => $user,
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Holiday rule updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update holiday rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getLocations()
    {
        try {
            $locations = DB::table('item_location')
                ->select('id', 'name')
                ->where('id', '>', 0)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json(['data' => $locations]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // Add this function to the Holiday controller class:
    public function getbyid(Request $request)
    {
        try {
            $id = $request->input('id');
            $rule = HolidayRuleModel::leftJoin('item_location', 'holiday_rule.location_id', '=', 'item_location.id')
                ->select('holiday_rule.*', 'item_location.name as location_name')
                ->where('holiday_rule.id', $id)
                ->first();

            if (!$rule) {
                return response()->json(['error' => 'Holiday rule not found'], 404);
            }

            // Format month with leading zero if needed
            if ($rule->month !== '*' && strlen($rule->month) == 1) {
                $rule->month_formatted = '0' . $rule->month;
            } else {
                $rule->month_formatted = $rule->month;
            }

            return response()->json(['data' => $rule]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get holiday rule data'], 500);
        }
    }



    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:holiday_rule,id',
        ]);

        DB::beginTransaction();

        try {
            // Delete holidays associated with this rule
            HolidayModel::where('holiday_rule_id', $request->id)->delete();

            // Delete the rule
            HolidayRuleModel::destroy($request->id);

            DB::commit();

            return response()->json(['message' => 'Holiday rule deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete holiday rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Route Detail

    public function detail()
    {
        return view('config.holiday.holiday-detail');
    }

    public function dtDetail(Request $request)
    {
        try {
            $query = HolidayModel::query()
                ->leftJoin('holiday_rule', 'holiday.holiday_rule_id', '=', 'holiday_rule.id')
                ->leftJoin('item_location', 'holiday.location_id', '=', 'item_location.id')
                ->select('holiday.*', 'holiday_rule.name as rule_name', 'item_location.name as location_name');

            if ($request->filled('holiday_rule_id')) {
                $query->where('holiday_rule_id', $request->holiday_rule_id);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('holiday_date', [$request->date_from, $request->date_to]);
            }

            // Add location filter
            if ($request->filled('location_id')) {
                $query->where('holiday.location_id', $request->location_id);
            }

            return DataTables::eloquent($query)
                ->addColumn('action', function ($holiday) {
                    $btn = '<div class="btn-group my-btn-group">';
                    $btn .= '<button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle" type="button" data-id="' . $holiday->id . '">';
                    $btn .= '<i class="ti ti-dots-vertical"></i>';
                    $btn .= '</button>';
                    $btn .= '<ul class="dropdown-menu" style="display:none;">';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center text-danger delete-btn" href="javascript:void(0);" data-id="' . $holiday->id . '">';
                    $btn .= '<i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->editColumn('holiday_date', function ($holiday) {
                    return Carbon::parse($holiday->holiday_date)->format('Y-m-d');
                })
                ->addColumn('location', function ($holiday) {
                    return $holiday->location_name ?? 'Global';
                })
                ->addColumn('rule_name', function ($holiday) {
                    return $holiday->rule_name;
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
