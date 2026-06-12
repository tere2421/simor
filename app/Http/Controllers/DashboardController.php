<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockTransaction;
use App\Models\StockTransactionHeader;
use App\Models\TemperatureRecord;
use App\Models\TemperatureZone;
use App\Models\ChecklistItem;
use App\Models\ChecklistRecord;
use App\Models\Schedule;
use App\Models\StaffProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ── DATA BERSAMA (semua role) ──────────────────────────────
        $todaySchedules = Schedule::with(['staffProfile', 'shift'])
            ->whereDate('schedule_date', today())
            ->where('status', 'approved')
            ->get();

        $checklistPct = 0;
        $doneChecklist = 0;
        $todayChecklist = ChecklistItem::where('is_active', true)->count();
        if ($todayChecklist > 0) {
            $doneChecklist = ChecklistRecord::whereDate('date', today())->where('is_done', true)->count();
            $checklistPct  = round(($doneChecklist / $todayChecklist) * 100);
        }

        $zones = TemperatureZone::with('latestRecord')->get();

        // ── DATA KHUSUS SM & PIC ───────────────────────────────────
        $totalIngredients  = 0;
        $criticalStocks    = collect();
        $emptyStocks       = 0;
        $expiryAlerts      = collect();
        $recentTransactions = collect();
        $stockChartData    = collect();
        $abnormalTemp      = 0;
        $totalStaff        = 0;
        $pendingSchedules  = 0;
        $weekSchedules     = 0;
        $mySchedule        = null;

        if ($user->isManager()) {
            $totalIngredients = Ingredient::where('is_active', true)->count();
            $criticalStocks   = Ingredient::where('is_active', true)
                ->whereColumn('current_stock', '<=', 'min_stock_threshold')
                ->with('category')->get();
            $emptyStocks      = Ingredient::where('is_active', true)->where('current_stock', 0)->count();
            $expiryAlerts     = Ingredient::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', now()->addDays(7))
                ->whereDate('expiry_date', '>=', now())
                ->orderBy('expiry_date')->get();
            $recentTransactions = StockTransactionHeader::with(['user', 'lines.ingredient'])
                ->latest()->take(6)->get();
            $stockChartData   = Ingredient::where('is_active', true)
                ->select('name', 'current_stock', 'min_stock_threshold')
                ->orderBy('current_stock')->take(8)->get();
            $abnormalTemp     = TemperatureRecord::where('is_abnormal', true)
                ->whereDate('recorded_at', today())->count();
            $totalStaff       = StaffProfile::where('is_active', true)->count();
            $pendingSchedules = Schedule::where('status', 'pending')->count();
            $weekSchedules    = Schedule::whereBetween('schedule_date', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->where('status', 'approved')->count();
        }

        // ── DATA KHUSUS STAFF ──────────────────────────────────────
        if ($user->isStaff()) {
            // Jadwal staff ini minggu ini
            $staffProfile = $user->staffProfile;
            if ($staffProfile) {
                $mySchedule = Schedule::with('shift')
                    ->where('staff_profile_id', $staffProfile->id)
                    ->whereBetween('schedule_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->orderBy('schedule_date')
                    ->get();
            }
            // Staff juga boleh lihat stok kritis (read only)
            $criticalStocks = Ingredient::where('is_active', true)
                ->whereColumn('current_stock', '<=', 'min_stock_threshold')
                ->with('category')->take(5)->get();
            $expiryAlerts = Ingredient::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', now()->addDays(3))
                ->whereDate('expiry_date', '>=', now())
                ->orderBy('expiry_date')->get();
        }

        return view('dashboard.index', compact(
            'user',
            'totalIngredients', 'criticalStocks', 'emptyStocks', 'expiryAlerts',
            'recentTransactions', 'stockChartData',
            'abnormalTemp', 'zones', 'todayChecklist', 'doneChecklist', 'checklistPct',
            'todaySchedules', 'totalStaff', 'pendingSchedules', 'weekSchedules',
            'mySchedule'
        ));
    }
}
