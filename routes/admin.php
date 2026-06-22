<?php

use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;
use App\Livewire\Admin\Assessments\AssessmentManager;
use App\Livewire\Admin\Assessments\AssessmentList;
use App\Livewire\Admin\Assessments\AssignAssessment;
use App\Livewire\Admin\Auth\Login;
use App\Services\OrganisationContext;

// ── Guest Routes (not logged in) ──────────────────────────────────
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ── Authenticated Routes ──────────────────────────────────────────

Route::group(['middleware' => 'redirect.notauth:admin'], function ($router) {

    // Logout
    Route::post('/logout', function () {
        OrganisationContext::clear();
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    $router->livewire('home', 'admin.home')->name('home');

    // ════════════════════════════════════════════════════════════════
    // SYSTEM MODE ROUTES (System-wide access)
    // ════════════════════════════════════════════════════════════════

    Route::group(['middleware' => ['auth:admin']], function () {

        // ────────────────────────────────────────────────────────────
        // SYSTEM LEVEL - ORGANISATIONS
        // ────────────────────────────────────────────────────────────
        Route::prefix('organisations')->name('organisations.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Organisation\OrganisationList::class)
                ->name('index')
                ->middleware('can:system.organisation.view');

            Route::get('/create', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('create')
                ->middleware('can:system.organisation.create');

            // ✅ SYSTEM LEVEL - Edit organisation (system admin only)
            Route::get('/{id}/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('edit')
                ->middleware('can:system.organisation.edit');


            Route::get('/{organisationId}/dashboard', \App\Livewire\Admin\Organisation\OrganisationDashboard::class)
                ->name('dashboard')
                ->middleware('can:system.organisation.view');
        });

        // ────────────────────────────────────────────────────────────
        // SYSTEM LEVEL - ADMINS MANAGEMENT
        // ────────────────────────────────────────────────────────────
        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', \App\Livewire\Admin\AdminManagement\AdminList::class)
                ->name('index')
                ->middleware('can:system.admin.view');

            Route::get('/create', \App\Livewire\Admin\AdminManagement\AdminForm::class)
                ->name('create')
                ->middleware('can:system.admin.create');

            Route::get('/{id}/edit', \App\Livewire\Admin\AdminManagement\AdminForm::class)
                ->name('edit')
                ->middleware('can:system.admin.edit');

            // ✅ SYSTEM LEVEL - Permission Management
            Route::get('/{admin}/permissions', \App\Livewire\Admin\RolePermission\AdminPermissionManager::class)
                ->name('permissions')
                ->middleware(['can:system.admin.assign_permissions', 'protect.superadmin']);
        });

        // ────────────────────────────────────────────────────────────
        // SYSTEM LEVEL - ROLES MANAGEMENT
        // ────────────────────────────────────────────────────────────
        Route::get('roles', \App\Livewire\Admin\RolePermission\RoleManager::class)
            ->name('roles.index')
            ->middleware('can:system.role.view');

        // ────────────────────────────────────────────────────────────
        // SYSTEM LEVEL - QUESTION BANK
        // ────────────────────────────────────────────────────────────
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', QuestionGroupIndex::class)
                ->name('index')
                ->middleware('can:system.question.view');

            Route::get('/create', QuestionGroupForm::class)
                ->name('create')
                ->middleware('can:system.question.create');

            Route::get('/{groupId}/edit', QuestionGroupForm::class)
                ->name('edit')
                ->middleware('can:system.question.edit');
        });

        // ────────────────────────────────────────────────────────────
        // SYSTEM LEVEL - ASSESSMENTS
        // ────────────────────────────────────────────────────────────
        Route::prefix('assessments')->name('assessments.')->group(function () {
            Route::get('/view', AssessmentManager::class)
                ->name('view')
                ->middleware('can:system.assessment.view');

            Route::get('/assessment_list', AssessmentList::class)
                ->name('assessment_list')
                ->middleware('can:system.assessment.assign_view');

            Route::get('/assessments/assign/{encryptedId}', AssignAssessment::class)
                ->name('assign')
                ->middleware('can:system.assessment.assign');
        });

    });

   // ════════════════════════════════════════════════════════════════
    // ORGANISATION MODE ROUTES (Organisation-specific access)
    // ════════════════════════════════════════════════════════════════

    Route::group(['middleware' => ['auth:admin']], function () {

        // ────────────────────────────────────────────────────────────
        // ORGANISATION LEVEL - ORGANISATION MANAGEMENT
        // ────────────────────────────────────────────────────────────

        Route::prefix('org')->name('org.')->group(function () {

            // ✅ Remove middleware from route, handle in component
            Route::get('/{organisationId}/dashboard', \App\Livewire\Admin\Organisation\OrganisationDashboard::class)
                ->name('dashboard');

            Route::get('/{organisationId}/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('edit');

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

            Route::prefix('{organisationId}/settings')->name('settings.')->group(function () {
                // Route::get('/', \App\Livewire\Admin\Setting\SettingView::class)
                //     ->name('index');

                Route::get('/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                    ->name('edit');
            });

            // Route::get('{organisationId}/activity', \App\Livewire\Admin\Activity\ActivityLog::class)
            //     ->name('activity');

        });

    });

});
