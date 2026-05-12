<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceHistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect welcome page to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('login');
})->name('register');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Attendance
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceHistoryController::class, 'index'])->name('attendance.history');
    Route::get('/attendance/export', [AttendanceHistoryController::class, 'export'])->name('attendance.export');
    Route::get('/attendance/scan', [QrCodeController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/scan', [QrCodeController::class, 'processScan'])->name('attendance.scan.process');
    
    // Teachers
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    
    // --- Route Import, Export & Bulk Actions ---

    Route::post('/teachers/bulk-toggle', [TeacherController::class, 'bulkToggleStatus'])->name('teachers.bulk-toggle');
    Route::delete('/teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])->name('teachers.bulk-delete'); 
    // -------------------------------------------

    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
    Route::get('/teachers/{teacher}/qr', [QrCodeController::class, 'show'])->name('teachers.qr');
    Route::get('/teachers/{teacher}/qr/download', [QrCodeController::class, 'download'])->name('teachers.qr.download');
    Route::post('/teachers/{teacher}/qr/regenerate', [QrCodeController::class, 'regenerate'])->name('teachers.qr.regenerate');
    Route::get('/teachers/{teacher}/data', [TeacherController::class, 'getData'])->name('teachers.data');
    
    // Subjects
    Route::resource('subjects', SubjectController::class);
    
    // Classes
    // Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    // Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    // Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    // Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    // Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    // Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    
    // Schedules
    // Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    // Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    // Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    // Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    // Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    // Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    // Route::get('/my-schedule', [ScheduleController::class, 'mySchedule'])->name('schedules.my');
    
    // Leaves
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('/my-leaves', [LeaveController::class, 'myLeaves'])->name('leaves.my');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/attendance', [SettingsController::class, 'updateAttendance'])->name('settings.attendance');
    Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance');
    Route::post('/settings/notification', [SettingsController::class, 'updateNotification'])->name('settings.notification');
    Route::post('/settings/reset', [SettingsController::class, 'resetSettings'])->name('settings.reset');
    
    // Messages / CS Chat
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'fetchMessages'])->name('messages.fetch');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');
    
    // Teacher Routes
    Route::middleware(['role:guru'])->prefix('teacher')->name('teacher.')->group(function () {
        // Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        // Route::get('/schedule', [TeacherDashboardController::class, 'schedule'])->name('schedule');
        // Route::get('/attendance', [TeacherDashboardController::class, 'attendance'])->name('attendance');
        // Route::get('/profile', [TeacherDashboardController::class, 'profile'])->name('profile');
        // Route::post('/profile', [TeacherDashboardController::class, 'updateProfile'])->name('profile.update');
        // Route::post('/leave-request', [TeacherDashboardController::class, 'storeLeaveRequest'])->name('leave-request.store');
        // Route::post('/today-notes', [TeacherDashboardController::class, 'updateTodayNotes'])->name('today-notes.update');
        // Route::get('/leaves', [TeacherDashboardController::class, 'leaves'])->name('leaves');
    });
});