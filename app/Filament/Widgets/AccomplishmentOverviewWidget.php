<?php

namespace App\Filament\Widgets;

use App\Models\AccomplishmentDetail;
use App\Models\AccomplishmentHeader;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AccomplishmentOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $user         = Auth::user();
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $prevMonth    = now()->subMonth()->month;
        $prevYear     = now()->subMonth()->year;
        $period       = now()->format('F Y');

        $hasRole = $user && $user->roles()->exists();

        // ---------------------------------------------------------------
        // STAT 1 — Total Accomplishments This Month (own department)
        // ---------------------------------------------------------------
        $totalThisMonth = $hasRole
            ? AccomplishmentDetail::query()
                ->whereHas('header', function ($q) use ($user, $currentMonth, $currentYear) {
                    $q->where('reporting_month', $currentMonth)
                      ->where('reporting_year',  $currentYear)
                      ->where('department_id',   $user->department_code);
                })->count()
            : 0;

        $totalLastMonth = $hasRole
            ? AccomplishmentDetail::query()
                ->whereHas('header', function ($q) use ($user, $prevMonth, $prevYear) {
                    $q->where('reporting_month', $prevMonth)
                      ->where('reporting_year',  $prevYear)
                      ->where('department_id',   $user->department_code);
                })->count()
            : 0;

        $diff      = $totalThisMonth - $totalLastMonth;
        $diffLabel = $diff >= 0 ? "+{$diff} vs last month" : "{$diff} vs last month";
        $diffIcon  = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $diffColor = $diff >= 0 ? 'success' : 'danger';

        $sparkline = [];
        for ($i = 5; $i >= 0; $i--) {
            if ($hasRole) {
                $date        = now()->subMonths($i);
                $sparkline[] = AccomplishmentDetail::query()
                    ->whereHas('header', function ($q) use ($user, $date) {
                        $q->where('reporting_month', $date->month)
                          ->where('reporting_year',  $date->year)
                          ->where('department_id',   $user->department_code);
                    })->count();
            } else {
                $sparkline[] = 0;
            }
        }

        // ---------------------------------------------------------------
        // STAT 2 — Submitted Reports This Month (own department)
        // ---------------------------------------------------------------
        $totalHeaders = 0;
        $submitted    = 0;
        $draft        = 0;

        if ($hasRole) {
            $headerQuery  = AccomplishmentHeader::query()
                ->where('reporting_month', $currentMonth)
                ->where('reporting_year',  $currentYear)
                ->where('department_id',   $user->department_code);

            $totalHeaders = (clone $headerQuery)->count();
            $submitted    = (clone $headerQuery)->where('status', 'submitted')->count();
            $draft        = $totalHeaders - $submitted;
        }

        return [

            Stat::make("Total Accomplishments — {$period}", number_format($totalThisMonth))
                ->description($diffLabel)
                ->descriptionIcon($diffIcon)
                ->color($diffColor)
                ->chart($sparkline),

        ];
    }
}