<?php

namespace App\Http\Controllers\Api;

use App\Models\Office;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccomplishmentDetail;
use App\Models\AccomplishmentHeader;

class AccomplishmentPrintController extends Controller
{
    public function print(Request $request)
    {
        $request->validate([
            'department_id' => 'required|integer',
            'year' => 'required|integer',
            'month' => 'required|integer',
        ]);

        $details = AccomplishmentDetail::with(['header.department', 'ppa'])
            ->whereHas('header', function ($query) use ($request) {
                $query->where('department_id', $request->department_id)
                    ->where('reporting_year', $request->year)
                    ->where('reporting_month', $request->month);
            })
            ->orderBy('date')
            ->get();

        if ($details->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => null,
            ]);
        }

        $header = $details->first()->header;
        $department = $header->department;
        $monthName = date('F', mktime(0, 0, 0, $header->reporting_month, 1));


        return response()->json([
            'department_id' => $department->id,
            'department' => $department->office,
            'reporting_month' => $header->reporting_month,
            'reporting_month_name' => $monthName,
            'reporting_year' => $header->reporting_year,

            'accomplishment_details' => $details->map(fn ($d) => [
                'date' => $d->date,
                'title_of_accomplishment' => $d->title_of_accomplishment,
                'brief_description' => $d->brief_description,
                'scope' => $d->scope,
                'results' => $d->results,
                'mov' => $d->mov,
                'ppa_id' => $d->ppa_id,
                'paps_desc' => $d->ppa?->paps_desc,
            ]),
        ]);

    }


}