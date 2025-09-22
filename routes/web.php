<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AcademicSettingController;
use App\Http\Controllers\GradeSettingController;
use App\Http\Controllers\StreamSettingController;
use App\Http\Controllers\ShiftSettingController;
use App\Http\Controllers\SectionSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceMonthSettingController;
use App\Http\Controllers\StudentAcademicAttendanceController;
use App\Http\Controllers\GradeStreamSubjectController;
use App\Http\Controllers\ExamSettingController;
use App\Http\Controllers\ExamMarkController;
use App\Http\Controllers\StudentPromoteController;
use App\Http\Controllers\DisciplineNoteController;
use App\Http\Controllers\CorrectiveMeasureController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\StudentReportController;
use Illuminate\Support\Facades\Artisan;









/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,  // Disable registration
    'reset' => true,      // Enable password reset
    'verify' => true      // Keep email verification
]);



// Custom authentication routes with /dashboard prefix
// Login routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password reset routes (if needed)
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');



// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {

    Route::get('/', [HomeController::class, 'root'])->name('root');
    // Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard.index');

    // routes/web.php
    // routes/web.php
    Route::prefix('/academic-settings')->name('academic-settings.')->group(function () {
        Route::get('/', [AcademicSettingController::class, 'index'])->name('index');
        Route::get('/form-data', [AcademicSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [AcademicSettingController::class, 'store'])->name('store');
        Route::get('/list', [AcademicSettingController::class, 'getAcademicSettingsList'])->name('list');
        Route::get('/details/{id}', [AcademicSettingController::class, 'getDetails'])->name('details');
        Route::put('/update/{id}', [AcademicSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AcademicSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [AcademicSettingController::class, 'toggleStatus'])->name('toggle-status');
    });


    // Grade Setting Routes
    Route::prefix('/grade-settings')->name('grade-settings.')->group(function () {
        Route::get('/', [GradeSettingController::class, 'index'])->name('index');
        Route::get('/form-data', [GradeSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [GradeSettingController::class, 'store'])->name('store');
        Route::get('/list', [GradeSettingController::class, 'getGradeSettingsList'])->name('list');
        Route::get('/details/{id}', [GradeSettingController::class, 'getDetails'])->name('details');
        Route::put('/update/{id}', [GradeSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [GradeSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [GradeSettingController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/restore/{id}', [GradeSettingController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [GradeSettingController::class, 'forceDelete'])->name('force-delete');
        Route::get('/trashed', [GradeSettingController::class, 'getTrashedSettings'])->name('trashed');
    });

    // Stream Setting Routes
    Route::prefix('/stream-settings')->name('stream-settings.')->group(function () {
        Route::get('/', [StreamSettingController::class, 'index'])->name('index');
        Route::get('/form-data', [StreamSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [StreamSettingController::class, 'store'])->name('store');
        Route::get('/list', [StreamSettingController::class, 'getList'])->name('list');
        Route::get('/details/{id}', [StreamSettingController::class, 'getDetails'])->name('details');
        Route::put('/update/{id}', [StreamSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [StreamSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [StreamSettingController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Shift Setting Routes
    Route::prefix('/shift-settings')->name('shift-settings.')->group(function () {
        Route::get('/', [ShiftSettingController::class, 'index'])->name('index');
        Route::get('/form-data', [ShiftSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [ShiftSettingController::class, 'store'])->name('store');
        Route::get('/list', [ShiftSettingController::class, 'getList'])->name('list');
        Route::get('/details/{id}', [ShiftSettingController::class, 'getDetails'])->name('details');
        Route::put('/update/{id}', [ShiftSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ShiftSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [ShiftSettingController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Section Setting Routes
    Route::prefix('/section-settings')->name('section-settings.')->group(function () {
        Route::get('/', [SectionSettingController::class, 'index'])->name('index');
        Route::get('/form-data', [SectionSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [SectionSettingController::class, 'store'])->name('store');
        Route::get('/list', [SectionSettingController::class, 'getList'])->name('list');
        Route::get('/details/{id}', [SectionSettingController::class, 'getDetails'])->name('details');
        Route::put('/update/{id}', [SectionSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SectionSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [SectionSettingController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Student Routes
    Route::prefix('/students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/form-data', [StudentController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [StudentController::class, 'store'])->name('store');
        Route::get('/list', [StudentController::class, 'getStudentsList'])->name('list');
        Route::post('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [StudentController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [StudentController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/academic-mapping', [StudentController::class, 'addAcademicMapping'])->name('add-academic-mapping');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::get('/details/{id}', [StudentController::class, 'getDetails'])->name('details');
        Route::get('/profile/{id}', [StudentController::class, 'getProfile'])->name('profile');
    });


    // Attendance Month Setting Routes
    Route::prefix('/attendance-month-settings')->name('attendance-month-settings.')->group(function () {
        Route::get('/', [AttendanceMonthSettingController::class, 'index'])->name('index');
        Route::get('/list', [AttendanceMonthSettingController::class, 'getAttendanceMonthsList'])->name('list');
        Route::get('/details/{id}', [AttendanceMonthSettingController::class, 'getDetails'])->name('details');
        Route::post('/store', [AttendanceMonthSettingController::class, 'store'])->name('store');
        Route::put('/update/{id}', [AttendanceMonthSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AttendanceMonthSettingController::class, 'destroy'])->name('delete');
        Route::post('/reorder', [AttendanceMonthSettingController::class, 'reorder'])->name('reorder');
    });


    // Student Academic Attendance Routes
    Route::prefix('/student-academic-attendance')->name('student-academic-attendance.')->group(function () {
        Route::get('/', [StudentAcademicAttendanceController::class, 'index'])->name('index');
        Route::post('/store', [StudentAcademicAttendanceController::class, 'store'])->name('store');
        Route::post('/bulk-update', [StudentAcademicAttendanceController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/summary', [StudentAcademicAttendanceController::class, 'summary'])->name('summary');
        Route::delete('/delete/{id}', [StudentAcademicAttendanceController::class, 'destroy'])->name('delete');

        // Student attendance routes
        Route::get('/api/student/{student}/academic-years', [StudentAcademicAttendanceController::class, 'getStudentAcademicYears']);
        Route::get('/api/student/{student}/attendance/{academicYear}', [StudentAcademicAttendanceController::class, 'getStudentMonthlyAttendance']);
        Route::post('/api/student-attendance', [StudentAcademicAttendanceController::class, 'saveStudentAttendance']);
        Route::put('/api/student-attendance/{id}', [StudentAcademicAttendanceController::class, 'saveStudentAttendance']);
    });


    // Grade Stream Subject Routes
    Route::prefix('/grade-stream-subjects')->name('grade-stream-subjects.')->group(function () {
        Route::get('/', [GradeStreamSubjectController::class, 'index'])->name('index');
        Route::get('/list', [GradeStreamSubjectController::class, 'getSubjectsList'])->name('list');
        Route::get('/form-data', [GradeStreamSubjectController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [GradeStreamSubjectController::class, 'store'])->name('store');
        Route::put('/update/{id}', [GradeStreamSubjectController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [GradeStreamSubjectController::class, 'destroy'])->name('delete');
        Route::post('/reorder', [GradeStreamSubjectController::class, 'reorder'])->name('reorder');
        Route::post('/toggle-status/{id}', [GradeStreamSubjectController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/details/{id}', [GradeStreamSubjectController::class, 'show']);
    });


    // Exam Setting Routes
    Route::prefix('/exam-settings')->name('exam-settings.')->group(function () {
        Route::get('/', [ExamSettingController::class, 'index'])->name('index');
        Route::get('/list', [ExamSettingController::class, 'getExamSettingsList'])->name('list');
        Route::get('/form-data', [ExamSettingController::class, 'getFormData'])->name('form-data');
        Route::post('/store', [ExamSettingController::class, 'store'])->name('store');
        Route::post('/update/{id}', [ExamSettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ExamSettingController::class, 'destroy'])->name('delete');
        Route::post('/toggle-status/{id}', [ExamSettingController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/details/{id}', [ExamSettingController::class, 'show'])->name('details');
        Route::get('/academic-year/{academicYearId}', [ExamSettingController::class, 'getByAcademicYear'])->name('by-academic-year');
    });


    // Exam Marks Routes
    Route::prefix('exam-marks')->group(function () {
        // Get form data for a student
        Route::get('/form-data/{studentId}', [ExamMarkController::class, 'getFormData']);

        // Get exams by academic mapping
        Route::get('/exams/{academicMapId}', [ExamMarkController::class, 'getExamsByAcademicMapping']);

        // Get subjects by academic mapping
        Route::get('/subjects/{academicMapId}', [ExamMarkController::class, 'getSubjectsByAcademicMapping']);

        // Get exam marks with filters
        Route::get('/list', [ExamMarkController::class, 'getExamMarks']);

        // Store or update single exam mark
        Route::post('/store', [ExamMarkController::class, 'storeOrUpdate']);

        // Bulk store or update exam marks
        Route::post('/bulk-store', [ExamMarkController::class, 'bulkStoreOrUpdate']);

        // Delete exam mark
        Route::delete('/delete/{id}', [ExamMarkController::class, 'destroy']);

        // Get student performance summary
        Route::get('/performance/{studentId}', [ExamMarkController::class, 'getStudentPerformanceSummary']);
        Route::get('/performance/{studentId}/{academicMapId}', [ExamMarkController::class, 'getStudentPerformanceSummary']);

        Route::get('/students/profile/{student}/exam-marks', [ExamMarkController::class, 'showMarksManagement'])
            ->name('students.exam-marks');

        Route::get('/exam-marks/form-data/{studentId}', [ExamMarkController::class, 'getFormData']);
    });


    // Student promotion routes
    Route::prefix('students/{student}/promote')->group(function () {
        Route::get('/', [StudentPromoteController::class, 'showPromoteForm'])->name('students.promote.show');
        Route::post('/', [StudentPromoteController::class, 'promoteStudent'])->name('students.promote');
        Route::get('/history', [StudentPromoteController::class, 'promotionHistory'])->name('students.promote.history');

        // API routes for promotion data
        Route::get('/data', [StudentPromoteController::class, 'getPromotionData'])->name('students.promote.data');
        Route::get('/mapping/{mapping}/dependencies', [StudentPromoteController::class, 'checkDependencies'])->name('students.promote.mapping.dependencies');
        Route::delete('/mapping/{mapping}', [StudentPromoteController::class, 'destroyMapping'])->name('students.promote.mapping.destroy');
    });


    // Discipline Note routes

    Route::prefix('discipline-notes')->group(function () {
        // Student-specific discipline notes
        Route::prefix('students/{student}')->group(function () {
            // Get all notes for a student
            Route::get('/', [DisciplineNoteController::class, 'index'])->name('discipline.notes.index');

            // Get academic mappings for a student
            Route::get('/mappings', [DisciplineNoteController::class, 'getAcademicMappings'])->name('discipline.notes.mappings');

            // Academic mapping specific notes
            Route::prefix('mappings/{academicMapping}')->group(function () {
                Route::get('/', [DisciplineNoteController::class, 'getNotesByMapping'])->name('discipline.notes.by-mapping');
                Route::post('/', [DisciplineNoteController::class, 'store'])->name('discipline.notes.store');

                // Individual note operations
            });

            Route::prefix('notes/{note}')->group(function () {
                Route::get('/', [DisciplineNoteController::class, 'show'])->name('discipline.notes.show');
                Route::put('/', [DisciplineNoteController::class, 'update'])->name('discipline.notes.update');
                Route::delete('/', [DisciplineNoteController::class, 'destroy'])->name('discipline.notes.destroy');
            });
        });

        // Filter routes
        Route::prefix('filter')->group(function () {
            Route::get('students/{student}', [DisciplineNoteController::class, 'filter'])->name('discipline.notes.filter.student');
        });
    });


    Route::prefix('corrective-measures')->group(function () {
        // Student-specific corrective measures
        Route::prefix('students/{student}')->group(function () {
            // Get all measures for a student
            Route::get('/', [CorrectiveMeasureController::class, 'index'])->name('corrective.measures.index');

            // Get academic mappings for a student
            Route::get('/mappings', [CorrectiveMeasureController::class, 'getAcademicMappings'])->name('corrective.measures.mappings');

            // Academic mapping specific measures
            Route::prefix('mappings/{academicMapping}')->group(function () {
                Route::get('/', [CorrectiveMeasureController::class, 'getMeasuresByMapping'])->name('corrective.measures.by-mapping');
                Route::post('/', [CorrectiveMeasureController::class, 'store'])->name('corrective.measures.store');
            });

            // Individual measure operations
            Route::prefix('measures/{measure}')->group(function () {
                Route::get('/', [CorrectiveMeasureController::class, 'show'])->name('corrective.measures.show');
                Route::put('/', [CorrectiveMeasureController::class, 'update'])->name('corrective.measures.update');
                Route::delete('/', [CorrectiveMeasureController::class, 'destroy'])->name('corrective.measures.destroy');
                Route::patch('/resolve', [CorrectiveMeasureController::class, 'resolve'])->name('corrective.measures.resolve');
            });
        });

        // Filter routes
        Route::prefix('filter')->group(function () {
            Route::get('students/{student}', [CorrectiveMeasureController::class, 'filter'])->name('corrective.measures.filter.student');
        });
    });

    Route::prefix('remarks')->group(function () {
        // Student-specific remarks
        Route::prefix('students/{student}')->group(function () {
            // Get all remarks for a student
            Route::get('/', [RemarkController::class, 'index'])->name('remarks.index');

            // Get academic mappings for a student
            Route::get('/mappings', [RemarkController::class, 'getAcademicMappings'])->name('remarks.mappings');

            // Get remark roles
            Route::get('/roles', [RemarkController::class, 'getRemarkRoles'])->name('remarks.roles');

            // Academic mapping specific remarks
            Route::prefix('mappings/{academicMapping}')->group(function () {
                Route::get('/', [RemarkController::class, 'getRemarksByMapping'])->name('remarks.by-mapping');
                Route::post('/', [RemarkController::class, 'store'])->name('remarks.store');
            });

            // Individual remark operations
            Route::prefix('remarks/{remark}')->group(function () {
                Route::get('/', [RemarkController::class, 'show'])->name('remarks.show');
                Route::put('/', [RemarkController::class, 'update'])->name('remarks.update');
                Route::delete('/', [RemarkController::class, 'destroy'])->name('remarks.destroy');
            });
        });

        // Filter routes
        Route::prefix('filter')->group(function () {
            Route::get('students/{student}', [RemarkController::class, 'filter'])->name('remarks.filter.student');
        });
    });

    // Student Report routes
    Route::prefix('students/{student}/report')->group(function () {
        Route::get('/', [StudentReportController::class, 'generateReport'])->name('students.report');
        Route::get('/pdf', [StudentReportController::class, 'exportPdf'])->name('students.report.pdf');
        Route::get('/comprehensive-report', [StudentReportController::class, 'showReport'])->name('students.comprehensive-report');
    });
});


Route::get('/artisan/{command}', function ($command) {

    if ($command === 'migrate') {
        Artisan::call('migrate', ['--force' => true]);
        return "Migration executed.";
    }
    return "Not allowed.";
});
