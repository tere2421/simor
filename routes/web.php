<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\TemperatureController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ManagerTaskController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── STOK ──────────────────────────────────────────────────────
    Route::resource('categories', CategoryController::class)
         ->except('show')->middleware('role:SM,PIC');

    Route::get('/ingredients',              [IngredientController::class, 'index'])->name('ingredients.index');
    Route::middleware('role:SM,PIC')->group(function () {
        Route::get('/ingredients/create',            [IngredientController::class, 'create'])->name('ingredients.create');
        Route::post('/ingredients',                  [IngredientController::class, 'store'])->name('ingredients.store');
        Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');
        Route::get('/ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
        Route::put('/ingredients/{ingredient}',      [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}',   [IngredientController::class, 'destroy'])->name('ingredients.destroy');
    });

    Route::get('/stocks',         [StockTransactionController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create',  [StockTransactionController::class, 'create'])->name('stocks.create');
    Route::post('/stocks',        [StockTransactionController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{stock}', [StockTransactionController::class, 'show'])->name('stocks.show');

    Route::middleware('role:SM,PIC')->group(function () {
        Route::get('/opnames',                    [StockOpnameController::class, 'index'])->name('opnames.index');
        Route::get('/opnames/create',             [StockOpnameController::class, 'create'])->name('opnames.create');
        Route::get('/opnames/download-template',  [StockOpnameController::class, 'downloadTemplate'])->name('opnames.download-template');
        Route::post('/opnames/upload',            [StockOpnameController::class, 'upload'])->name('opnames.upload');
        Route::post('/opnames',                   [StockOpnameController::class, 'store'])->name('opnames.store');
        Route::get('/opnames/{opname}',           [StockOpnameController::class, 'show'])->name('opnames.show');
        Route::patch('/opnames/{opname}/approve', [StockOpnameController::class, 'approve'])->name('opnames.approve');
    });

    // ── MONITORING ────────────────────────────────────────────────
    Route::prefix('temperatures')->name('temperatures.')->group(function () {
        // Records — semua role
        Route::get('/',       [TemperatureController::class, 'index'])->name('index');
        Route::get('/create', [TemperatureController::class, 'create'])->name('create');
        Route::post('/',      [TemperatureController::class, 'store'])->name('store');

        // Zones CRUD — SM & PIC
        Route::get('/zones',                  [TemperatureController::class, 'zones'])->name('zones')->middleware('role:SM,PIC');
        Route::get('/zones/create',           [TemperatureController::class, 'createZone'])->name('zones.create')->middleware('role:SM,PIC');
        Route::post('/zones',                 [TemperatureController::class, 'storeZone'])->name('zones.store')->middleware('role:SM,PIC');
        Route::get('/zones/{zone}/edit',      [TemperatureController::class, 'editZone'])->name('zones.edit')->middleware('role:SM,PIC');
        Route::put('/zones/{zone}',           [TemperatureController::class, 'updateZone'])->name('zones.update')->middleware('role:SM,PIC');
        Route::delete('/zones/{zone}',        [TemperatureController::class, 'destroyZone'])->name('zones.destroy')->middleware('role:SM,PIC');
    });

    // Checklist kebersihan
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/',        [ChecklistController::class, 'index'])->name('index');
        Route::post('/',       [ChecklistController::class, 'store'])->name('store');
        Route::get('/history', [ChecklistController::class, 'history'])->name('history');
    });

    // Manager Tasks (SM & PIC)
    Route::middleware('role:SM,PIC')->group(function () {
        Route::get('/manager-tasks',  [ManagerTaskController::class, 'index'])->name('manager-tasks.index');
        Route::post('/manager-tasks', [ManagerTaskController::class, 'store'])->name('manager-tasks.store');
    });
    Route::middleware('role:SM')->group(function () {
        Route::get('/manager-tasks/manage',    [ManagerTaskController::class, 'taskIndex'])->name('manager-tasks.manage');
        Route::get('/manager-tasks/create',    [ManagerTaskController::class, 'taskCreate'])->name('manager-tasks.create');
        Route::post('/manager-tasks/tasks',    [ManagerTaskController::class, 'taskStore'])->name('manager-tasks.store-task');
        Route::get('/manager-tasks/{task}/edit',   [ManagerTaskController::class, 'taskEdit'])->name('manager-tasks.edit');
        Route::put('/manager-tasks/{task}',        [ManagerTaskController::class, 'taskUpdate'])->name('manager-tasks.update');
        Route::delete('/manager-tasks/{task}',     [ManagerTaskController::class, 'taskDestroy'])->name('manager-tasks.destroy');
    });

    // ── JADWAL ────────────────────────────────────────────────────
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::middleware('role:SM')->group(function () {
        Route::get('/shifts/create',     [ShiftController::class, 'create'])->name('shifts.create');
        Route::post('/shifts',           [ShiftController::class, 'store'])->name('shifts.store');
        Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    });

    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::middleware('role:SM,PIC')->group(function () {
        Route::post('/schedules/bulk-save',           [ScheduleController::class, 'bulkSave'])->name('schedules.bulkSave');
        Route::get('/schedules/export',               [ScheduleController::class, 'export'])->name('schedules.export');
        Route::get('/schedules/create',               [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules',                     [ScheduleController::class, 'store'])->name('schedules.store');
        Route::patch('/schedules/{schedule}/approve', [ScheduleController::class, 'approve'])->name('schedules.approve');
        Route::patch('/schedules/{schedule}/cancel',  [ScheduleController::class, 'cancel'])->name('schedules.cancel');
        Route::delete('/schedules/{schedule}',        [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // Kehadiran — problem tracking
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::middleware('role:SM,PIC')->group(function () {
        Route::get('/attendances/create',          [AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('/attendances',                [AttendanceController::class, 'store'])->name('attendances.store');
        Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    });

    // ── STAFF — SM only ──────────────────────────────────────────
    Route::middleware('role:SM')->group(function () {
        Route::get('/staff',                          [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create',                   [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff',                         [StaffController::class, 'store'])->name('staff.store');
        Route::delete('/staff/{staff}',               [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::patch('/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.resetPassword');
    });
});
