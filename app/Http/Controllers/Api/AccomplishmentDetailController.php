<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AccomplishmentDetailController extends Controller
{
    public function getByHeaderId(Request $request)
    {
        $request->validate([
            'header_id' => 'nullable|integer',
        ]);

        // ✅ If no header_id → return empty array
        if (!$request->filled('header_id')) {
            return response()->json([]);
        }

        $details = AccomplishmentDetail::with(['ppa', 'header.department'])
            ->where('header_id', $request->header_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(
            $details->transform(function ($item) {

                $images = collect($item->mov ?? [])
                    ->map(fn ($imagePath) => url('storage/' . $imagePath))
                    ->values();

                return [
                    'id' => $item->id,
                    'header_id' => $item->header_id,
                    'department_id' => $item->header?->department_id,
                    'office' => $item->header?->department?->office,

                    'date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'title_of_accomplishment' => $item->title_of_accomplishment,
                    'brief_description' => $item->brief_description,
                    'scope' => $item->scope,
                    'results' => $item->results,

                    'ppa_id' => $item->ppa_id,
                    'paps_desc' => $item->ppa?->paps_desc,

                    // MOV images (max 2 like your form)
                    'image1' => $images->get(0),
                    'image2' => $images->get(1),
                ];
            })
        );
    }
}
