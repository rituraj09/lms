{{--
    partials/alerts.blade.php
    Flash messages and global validation errors.
    Reusable across form and preview views.
--}}

{{-- Success --}}
@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="ri ri-checkbox-circle-line me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Validation errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center mb-1">
            <i class="ri ri-error-warning-fill me-2 fs-5"></i>
            <strong>Please fix the following errors:</strong>
        </div>
        <ul class="mb-0 mt-1 small ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
