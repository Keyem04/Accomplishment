<?php

namespace App\Http\Controllers\Api;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccomplishmentDetail;
use App\Models\AccomplishmentHeader;
use Illuminate\Support\Carbon;

class AccomplishmentPrintController extends Controller
{
    public function print(Request $request)
    {
    
        $request->validate([
            'department_id' => 'nullable|integer',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer',
            // 'include_in_print' => 'nullable|boolean',
        ]);

        // dd("test");
        
        // ✅ no params → return empty array
        if (
            !$request->filled('department_id') ||
            !$request->filled('year') ||
            !$request->filled('month') 
            // !$request->filled('include_in_print')
        ) {
            return response()->json([]);
        }


        $details = AccomplishmentDetail::with(['header.department', 'ppa', 'user'])
            ->whereHas('header', function ($query) use ($request) {
                $query->where('department_id', $request->department_id)
                    ->where('reporting_year', $request->year)
                    ->where('reporting_month', $request->month)
                    // ->where('include_in_print', $request->include_in_print ?? true)
                    ;
            })
            ->where('include_in_print', true)
            ->orderBy('date')
            ->get();

            return collect($details->transform(function($item) {
                // $item['mov'] = collect($item->mov)->map(fn($image) => ['image' => $image]);
                // $item['mov'] = collect($item->mov)->map(fn($image) => ['image' => asset('storage/' . $image)]);
                $images = collect($item->mov ?? [])
                    ->map(fn ($imagePath) => url('storage/' . $imagePath))
                    ->values();

                $preparedBy = $item->user?->FullName ?: $item->user?->UserName ?: 'System/Guest';

                $data = [
                    'department_id' => $item->header?->department_id,
                    'office' => $item->header?->department?->office,
                    'reporting_month' => Carbon::create()->month($item->header->reporting_month)->monthName,
                    'reporting_year' => $item->header?->reporting_year,
                    'date' => $item->date,
                    'title_of_accomplishment' => $item->title_of_accomplishment,
                    'brief_description' => $item->brief_description,
                    'scope' => $item->scope,
                    'results' => $item->results,
                    'paps_desc' => $item->ppa?->paps_desc,
                    // 'mov' => collect($item->mov ?? [])
                    //     ->map(fn($imagePath) => [
                    //         'image' => url('storage/' . $imagePath)
                    //     ])
                    //     ->values()
                    //     ->toArray()
                    // 👇 MOV images flattened
                    'image1' => $images->get(0), // null if not exists
                    'image2' => $images->get(1), // null if not exists
                    'include_in_print' => $item->include_in_print,
                    'user_name' => trim($preparedBy), 
                ];

                return $data;
            }));

       
        // $details = DB::table('accomplishment_headers as ah')
        //     ->select(
        //         'ah.department_id',
        //         'o.office',
        //         'ah.reporting_month',
        //         'ah.reporting_year',
        //         'ad.date',
        //         'ad.title_of_accomplishment',
        //         'ad.brief_description',
        //         'ad.scope',
        //         'ad.results',
        //         'ppa.paps_desc'
        //     )
        //     ->leftJoin('fms.offices as o', 'o.department_code', '=', 'ah.department_id')
        //     ->leftJoin('accomplishment_details as ad', 'ad.header_id', '=', 'ah.id')
        //     ->leftJoin('opcr.program_and_projects as ppa', 'ppa.id', '=', 'ad.ppa_id')
        //     ->where('ah.department_id', 26)
        //     ->where('ah.reporting_year', 2023)
        //     ->where('ah.reporting_month', 2)
        //     ->get();

        // dd($details);

        // return $details;
        //  dd($details);
          // ✅ params exist but no records → empty array
        // if ($details->isEmpty()) {
        //     return response()->json([]);
        // }


        // $header = $details->first()->header;
        // $department = $header->department;
        // $monthName = date('F', mktime(0, 0, 0, $header->reporting_month, 1));
        

        // return response()->json([
        //     'department_id' => $department->id,
        //     'department' => $department->office,
        //     'reporting_month' => $header->reporting_month,
        //     'reporting_month_name' => $monthName,
        //     'reporting_year' => $header->reporting_year,

        //     'accomplishment_details' => $details->map(fn ($d) => [
        //         'date' => $d->date,
        //         'title_of_accomplishment' => $d->title_of_accomplishment,
        //         'brief_description' => $d->brief_description,
        //         'scope' => $d->scope,
        //         'results' => $d->results,
        //         'ppa_id' => $d->ppa_id,
        //         'paps_desc' => $d->ppa?->paps_desc,
        //           'mov' => collect($d->mov ?? [])
        // ->map(fn ($path) => [
        //     'image' => $path,
        // ])
        // ->values(),


        //     ]),
        // ]);

    }


}