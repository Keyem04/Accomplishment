<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AccomplishmentHeaderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'department_id' => 'nullable|integer',
        ]);

        // ✅ if no department_id → return empty array
        if (!$request->filled('department_id')) {
            return response()->json([]);
        }

        $headers = AccomplishmentHeader::with('department')
            ->where('department_id', $request->department_id)
            ->orderByDesc('reporting_year')
            ->orderByDesc('reporting_month')
            ->get();

        return response()->json(
            $headers->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'department_id' => $item->department_id,
                    'office' => $item->department?->office,
                    'reporting_month' => $item->reporting_month,
                    'reporting_month_name' => Carbon::create()
                        ->month($item->reporting_month)
                        ->monthName,
                    'reporting_year' => $item->reporting_year,
                ];
            })
        );
    }
}
