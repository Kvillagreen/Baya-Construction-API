<?php

use App\Http\Controllers\Api\HR\EmployeeController;
use App\Http\Controllers\Api\HR\SalaryController;
use App\Http\Controllers\Api\HR\ContributionController;
use App\Http\Controllers\Api\HR\DeductionController;
use App\Http\Controllers\Api\HR\DailyTimeRecordController;
use App\Http\Controllers\Api\HR\LeaveEntryController;
use App\Http\Controllers\Api\HR\OvertimeLogController;
use App\Http\Controllers\Api\HR\PayrollAdjustmentController;
use App\Http\Controllers\Api\HR\PayrollCutoffController;
use App\Http\Controllers\Api\HR\PayrollController;
use App\Http\Controllers\Api\HR\PayrollPeriodController;
use App\Http\Controllers\Api\HR\PayrollRunController;
use App\Http\Controllers\Api\HR\PayslipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::patch('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::post('/salaries', [SalaryController::class, 'store'])->name('salaries.store');
    Route::patch('/salaries/{salary}', [SalaryController::class, 'update'])->name('salaries.update');
    Route::get('/contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::post('/contributions', [ContributionController::class, 'store'])->name('contributions.store');
    Route::patch('/contributions/{contribution}', [ContributionController::class, 'update'])->name('contributions.update');
    Route::get('/deductions', [DeductionController::class, 'index'])->name('deductions.index');
    Route::post('/deductions', [DeductionController::class, 'store'])->name('deductions.store');
    Route::patch('/deductions/{deduction}', [DeductionController::class, 'update'])->name('deductions.update');
    Route::get('/payroll/runs', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll-periods', [PayrollPeriodController::class, 'index'])->name('payroll-periods.index');
    Route::post('/payroll-periods', [PayrollPeriodController::class, 'store'])->name('payroll-periods.store');
    Route::get('/payroll-runs', [PayrollRunController::class, 'index'])->name('payroll-runs.index');
    Route::get('/payroll-runs/{payrollRun}', [PayrollRunController::class, 'show'])->name('payroll-runs.show');
    Route::post('/payroll-runs/preview', [PayrollRunController::class, 'preview'])->name('payroll-runs.preview');
    Route::post('/payroll-runs/finalize', [PayrollRunController::class, 'finalize'])->name('payroll-runs.finalize');
    Route::post('/payroll-runs/{payrollRun}/payslip', [PayslipController::class, 'generate'])->name('payslips.generate');
    Route::get('/payslips/{payslip}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::patch('/overtime-logs/{overtimeLog}', [OvertimeLogController::class, 'update'])->name('overtime.update');
    Route::delete('/overtime-logs/{overtimeLog}', [OvertimeLogController::class, 'destroy'])->name('overtime.destroy');
    Route::patch('/leave-entries/{leaveEntry}', [LeaveEntryController::class, 'update'])->name('leave.update');
    Route::delete('/leave-entries/{leaveEntry}', [LeaveEntryController::class, 'destroy'])->name('leave.destroy');
});

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/overtime-logs', [OvertimeLogController::class, 'index'])->name('overtime.index');
    Route::post('/overtime-logs', [OvertimeLogController::class, 'store'])->name('overtime.store');
    Route::get('/payroll-adjustments', [PayrollAdjustmentController::class, 'index'])->name('payroll-adjustments.index');
    Route::post('/payroll-adjustments', [PayrollAdjustmentController::class, 'store'])->name('payroll-adjustments.store');
    Route::patch('/payroll-adjustments/{payrollAdjustment}', [PayrollAdjustmentController::class, 'update'])->name('payroll-adjustments.update');
    Route::delete('/payroll-adjustments/{payrollAdjustment}', [PayrollAdjustmentController::class, 'destroy'])->name('payroll-adjustments.destroy');
    Route::get('/daily-time-records', [DailyTimeRecordController::class, 'index'])->name('dtr.index');
    Route::post('/daily-time-records', [DailyTimeRecordController::class, 'store'])->name('dtr.store');
    Route::get('/leave-entries', [LeaveEntryController::class, 'index'])->name('leave.index');
    Route::post('/leave-entries', [LeaveEntryController::class, 'store'])->name('leave.store');
    Route::get('/payroll-cutoffs/latest', [PayrollCutoffController::class, 'latest'])->name('payroll-cutoffs.latest');
    Route::post('/payroll-cutoffs', [PayrollCutoffController::class, 'store'])->name('payroll-cutoffs.store');
});
