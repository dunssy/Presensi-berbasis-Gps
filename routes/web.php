<?php

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AttendancePointController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\LogbookController;
use App\Http\Controllers\Manager\LogbookReviewController;
use App\Http\Controllers\Manager\ProjectMonitoringController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page (Public Homepage)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // ADMIN ROUTES (role: admin)
    // ==========================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');

        // Projects Management
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Attendance Points (GPS Geofence)
        Route::get('/attendance-points', [AttendancePointController::class, 'index'])->name('attendance-points.index');
        Route::post('/attendance-points', [AttendancePointController::class, 'store'])->name('attendance-points.store');
        Route::put('/attendance-points/{attendancePoint}', [AttendancePointController::class, 'update'])->name('attendance-points.update');
        Route::delete('/attendance-points/{attendancePoint}', [AttendancePointController::class, 'destroy'])->name('attendance-points.destroy');

        // Employees Management
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

        // Project Assignments
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Reports
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/logbook', [ReportController::class, 'logbook'])->name('reports.logbook');
    });

    // ==========================================
    // MANAGER ROUTES (role: manager, admin)
    // ==========================================
    Route::middleware('role:manager,admin')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/', [DashboardController::class, 'manager'])->name('dashboard');

        // Project Monitoring
        Route::get('/projects', [ProjectMonitoringController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectMonitoringController::class, 'show'])->name('projects.show');

        // Logbook Review
        Route::get('/logbooks', [LogbookReviewController::class, 'index'])->name('logbooks.index');
        Route::post('/logbooks/{logbook}/approve', [LogbookReviewController::class, 'approve'])->name('logbooks.approve');
        Route::post('/logbooks/{logbook}/reject', [LogbookReviewController::class, 'reject'])->name('logbooks.reject');
    });

    // ==========================================
    // KARYAWAN / EMPLOYEE ROUTES (role: karyawan, admin, manager)
    // ==========================================
    Route::middleware('role:karyawan,admin,manager')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [DashboardController::class, 'karyawan'])->name('dashboard');

        // Attendance Check-in & Check-out with GPS
        Route::post('/attendance/check-in', [AttendanceController::class, 'storeCheckIn'])->name('attendance.check-in');
        Route::post('/attendance/check-out', [AttendanceController::class, 'storeCheckOut'])->name('attendance.check-out');

        // Logbooks
        Route::get('/logbooks', [LogbookController::class, 'index'])->name('logbooks.index');
        Route::post('/logbooks', [LogbookController::class, 'store'])->name('logbooks.store');
    });
});
