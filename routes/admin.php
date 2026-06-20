<?php
//routes/admin.php
use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;
use App\Livewire\Admin\Assessments\AssessmentManager;
use App\Livewire\Admin\Auth\Login;
use App\Services\OrganisationContext;

// ── Guest Routes (not logged in) ──────────────────────────────────
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ── Authenticated Routes ───────────────────────────────────────────

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
    $router->livewire('role', 'admin.role')->name('role');
    $router->livewire('designation', 'admin.designation')->name('designation');
    $router->livewire('employee', 'admin.employee')->name('employee');

    // Organisations
    Route::group(['middleware' => ['auth:admin']], function () {
        Route::prefix('organisations')->name('organisations.')->group(callback: function () {
            Route::get('/', \App\Livewire\Admin\Organisation\OrganisationList::class)
                ->name('index')->middleware('can:organisation.view');
            Route::get('/create', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('create')->middleware('can:organisation.create');
            Route::get('/{id}/edit', \App\Livewire\Admin\Organisation\OrganisationForm::class)
                ->name('edit')->middleware('can:organisation.edit');
            Route::get('/{orgId}/dashboard', \App\Livewire\Admin\Organisation\OrganisationDashboard::class)
                ->name('dashboard')->middleware('can:organisation.view');
            // Route::get('/{orgId}/students', \App\Livewire\Admin\Student\StudentList::class)
            //     ->name('students')->middleware('can:student.view');
        });
// Admins
        Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', \App\Livewire\Admin\AdminManagement\AdminList::class)
            ->name('index')->middleware('can:admin.view');
        Route::get('/create', \App\Livewire\Admin\AdminManagement\AdminForm::class)
            ->name('create')->middleware('can:admin.create');
        Route::get('/{id}/edit', \App\Livewire\Admin\AdminManagement\AdminForm::class)
            ->name('edit')->middleware('can:admin.edit');
        Route::get('/{adminId}/permissions', \App\Livewire\Admin\RolePermission\AdminPermissionManager::class)
            ->name('permissions')->middleware('can:permission.manage');


    });
// Roles Management
        Route::get('roles', \App\Livewire\Admin\RolePermission\RoleManager::class)
            ->name('roles.index')
            ->middleware('can:role.view');
    });
    Route::get('/manage-questions', QuestionGroupIndex::class)->name('manage-questions');
    Route::get('/manage-questions/create', QuestionGroupForm::class)->name('manage-questions.create');
    Route::get('/manage-questions/{groupId}/edit', QuestionGroupForm::class)->name('manage-questions.edit');


    /* ================================================================
       |  ASSESSMENTS
       * ================================================================*/
    Route::get(
        '/assessments',
        AssessmentManager::class
    )->name('assessments.index');


});
