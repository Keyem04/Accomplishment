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
        ]);


        // dd("test");

        
        // ✅ no params → return empty array
        if (
            !$request->filled('department_id') ||
            !$request->filled('year') ||
            !$request->filled('month')
        ) {
            return response()->json([]);
        }


        $details = AccomplishmentDetail::with(['header.department', 'ppa'])
            ->whereHas('header', function ($query) use ($request) {
                $query->where('department_id', $request->department_id)
                    ->where('reporting_year', $request->year)
                    ->where('reporting_month', $request->month);
            })
            ->orderBy('date')
            ->get();

           return collect($details->transform(function($item) {
                // $item['mov'] = collect($item->mov)->map(fn($image) => ['image' => $image]);
                // $item['mov'] = collect($item->mov)->map(fn($image) => ['image' => asset('storage/' . $image)]);
                $movImages = collect($item->mov ?? []);
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
                    'mov_image_1' => $movImages->get(0) ? url('storage/' . $movImages->get(0)) : '',
                    'mov_image_2' => $movImages->get(1) ? url('storage/' . $movImages->get(1)) : '',
                    
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