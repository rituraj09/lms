<?php

use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;
use App\Livewire\Admin\Assessments\AssessmentList  as OldAssessmentList; // ← alias old one
use App\Livewire\Admin\Assessments\AssignAssessment;
use App\Livewire\Admin\Assessments\PreviewAssessment;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Reports\StudentDetailedReport;
use App\Livewire\Admin\Reports\StudentReportCard;
use App\Services\OrganisationContext;
use App\Livewire\Admin\AssessmentMasters\AssessmentList;
use App\Livewire\Admin\AssessmentMasters\AssessmentManage;
use App\Livewire\Admin\AssessmentMasters\AssessmentBuild;
use App\Livewire\Admin\Student\ManageStudents;
use App\Livewire\Admin\Student\StudentForm;
use App\Livewire\Admin\Student\StudentDetails;
use App\Livewire\Admin\Reports\StudentList;
use App\Livewire\Admin\Dashboard;

// ── Guest Routes (not logged in) ──────────────────────────────────
Route::middleware('redirect.auth:admin')->group(function ($router) {
    $router->get('/login', Login::class)->name('login');
});

// ── Authenticated Routes ──────────────────────────────────────────

Route::group(['middleware' => ['redirect.notauth:admin','auth:admin']], function ($router) {

    $router->get('/home', Dashboard::class)
        ->name('home');
    // ────────────────────────────────────────────────────────────
    // SYSTEM LEVEL - QUESTION BANK
    // ────────────────────────────────────────────────────────────
    Route::prefix('questions')->name('questions.')->group(function ($router) {
        $router->get('/', QuestionGroupIndex::class)
            ->name('index')
            ->middleware('can:system.question.view');

        $router->get('/create', QuestionGroupForm::class)
            ->name('create')
            ->middleware('can:system.question.create');

        $router->get('/{groupId}/edit', QuestionGroupForm::class)
            ->name('edit')
            ->middleware('can:system.question.edit');
    });

    // ────────────────────────────────────────────────────────────
    // SYSTEM LEVEL - ASSESSMENTS
    // ────────────────────────────────────────────────────────────

    Route::prefix('assessment-masters')->name('assessment-masters.')->group(function ($router) {
        $router->get('/list', AssessmentList::class)
            ->name('list')
            ->middleware('can:system.assessment.view');

        $router->get('/manage/{id?}', AssessmentManage::class)
            ->name('manage')
            ->middleware('can:system.assessment.create');

        $router->get('/build/{id}', AssessmentBuild::class)
            ->name('build')
            ->middleware('can:system.assessment.create');
    });

    Route::prefix('assessments')->name('assessments.')->group(function ($router) {


        $router->get('/assessment_list', OldAssessmentList::class)
            ->name('assessment_list')
            ->middleware('can:system.assessment.assign_view');

        $router->get('/assessments/assign/{encryptedId}', AssignAssessment::class)
            ->name('assign')
            ->middleware('can:system.assessment.assign');

        $router->get('/preview/{encryptedId}', PreviewAssessment::class)
            ->name('preview')
            ->middleware('can:system.assessment.view');
    });
    // ────────────────────────────────────────────────────────────
    // SYSTEM LEVEL - ADMINS MANAGEMENT
    // ────────────────────────────────────────────────────────────
    Route::prefix('admins')->name('admins.')->group(function ($router) {
        $router->get('/', \App\Livewire\Admin\AdminManagement\AdminList::class)
            ->name('index')
            ->middleware('can:system.admin.view');

        $router->get('/create', \App\Livewire\Admin\AdminManagement\AdminForm::class)
            ->name('create')
            ->middleware('can:system.admin.create');

        $router->get('/{id}/edit', \App\Livewire\Admin\AdminManagement\AdminForm::class)
            ->name('edit')
            ->middleware('can:system.admin.edit');

        //  SYSTEM LEVEL - Permission Management
        $router->get('/{admin}/permissions', \App\Livewire\Admin\RolePermission\AdminPermissionManager::class)
            ->name('permissions')
            ->middleware(['can:system.admin.assign_permissions', 'protect.superadmin']);
    });
    // ────────────────────────────────────────────────────────────
    // SYSTEM LEVEL - ROLES MANAGEMENT
    // ────────────────────────────────────────────────────────────
    $router->get('roles', \App\Livewire\Admin\RolePermission\RoleManager::class)
        ->name('roles.index')
        ->middleware('can:system.role.view');
    // ────────────────────────────────────────────────────────────
    // SYSTEM LEVEL - ORGANISATIONS
    // ────────────────────────────────────────────────────────────
    Route::prefix('organisations')->name('organisations.')->group(function ($router) {
        $router->get('/', \App\Livewire\Admin\Organisation\OrganisationList::class)
            ->name('index')
            ->middleware('can:system.organisation.view');

        $router->get('/create', \App\Livewire\Admin\Organisation\OrganisationForm::class)
            ->name('create')
            ->middleware('can:system.organisation.create');

        // SYSTEM LEVEL - Edit organisation (system admin only)
        $router->get('/{id}/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
            ->name('edit')
            ->middleware('can:system.organisation.edit');


        $router->get('/dashboard/{organisationId}', \App\Livewire\Admin\Organisation\OrganisationDashboard::class)
            ->name('dashboard')
            ->middleware('can:system.organisation.view');



    });
    // Reports
    Route::prefix('reports')->name('reports.')->group(function ($router) {
    // student list
        $router->get('student-list', StudentList::class)
            ->name('reports.student-list');
        $router->get('/student-report-cards', StudentReportCard::class)->name('report-cards');
        $router->get('/student-detailed-report/{studentId}', StudentDetailedReport::class)->name('detailed-report');
    });

    // ────────────────────────────────────────────────────────────
    // ORGANISATION LEVEL - ORGANISATION MANAGEMENT
    // ────────────────────────────────────────────────────────────

    Route::prefix('org')->name('org.')->group(function ($router) {

        //  Remove middleware from route, handle in component
        $router->get('/dashboard/{organisationId}', \App\Livewire\Admin\Organisation\OrganisationDashboard::class)
            ->name('dashboard');

        $router->get('/{organisationId}/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
            ->name('edit');

        $router->get('/students/{organisationId}', ManageStudents::class)
            ->name('students');
        $router->get('/students/create/{organisationId}', StudentForm::class)
            ->name('students.create');
        $router->get('/students/edit/{organisationId}/{studentId}', StudentForm::class)
            ->name('students.edit');
        $router->get('/students/view/{organisationId}/{studentId}', StudentDetails::class)
            ->name('students.view');

        // Route::prefix('{organisationId}/students')->name('students.')->group(function () {
        //     Route::get('/', \App\Livewire\Admin\Student\StudentList::class)
        //         ->name('index');

        //     Route::get('/create', \App\Livewire\Admin\Student\StudentForm::class)
        //         ->name('create');

        //     Route::get('/{student}/edit', \App\Livewire\Admin\Student\StudentForm::class)
        //         ->name('edit');
        // });

        // Route::prefix('{organisationId}/courses')->name('courses.')->group(function () {
        //     Route::get('/', \App\Livewire\Admin\Course\CourseList::class)
        //         ->name('index');

        //     Route::get('/{course}/assign', \App\Livewire\Admin\Course\CourseAssign::class)
        //         ->name('assign');
        // });

        // Route::prefix('{organisationId}/assessments')->name('assessments.')->group(function () {
        //     Route::get('/', \App\Livewire\Admin\Assessment\AssessmentList::class)
        //         ->name('index');

        //     Route::get('/{assessment}/assign', \App\Livewire\Admin\Assessment\AssessmentAssign::class)
        //         ->name('assign');
        // });

        // Route::prefix('{organisationId}/reports')->name('reports.')->group(function () {
        //     Route::get('/', \App\Livewire\Admin\Report\ReportList::class)
        //         ->name('index');

        //     Route::get('/export', \App\Livewire\Admin\Report\ReportExport::class)
        //         ->name('export');
        // });

        Route::prefix('{organisationId}/settings')->name('settings.')->group(function ($router) {
            // Route::get('/', \App\Livewire\Admin\Setting\SettingView::class)
            //     ->name('index');

            $router->get('/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('edit');
        });

        // Route::get('{organisationId}/activity', \App\Livewire\Admin\Activity\ActivityLog::class)
        //     ->name('activity');

    });
    // Logout
    $router->post('/logout', function () {
        OrganisationContext::clear();
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');
});
