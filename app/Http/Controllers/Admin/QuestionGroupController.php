<?php
// app/Http/Controllers/Admin/QuestionGroupController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class QuestionGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display the index page — Livewire handles data loading.
     */
    public function index(): View
    {
        return view('admin.questions.index');
    }

    /**
     * Display the create/edit form page — Livewire handles logic.
     */
    public function form(?int $groupId = null): View
    {
        return view('admin.questions.group-form', [
            'groupId' => $groupId,
        ]);
    }
}
