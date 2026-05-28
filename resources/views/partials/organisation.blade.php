

                    <div class="row g-4">
                         <div class="d-flex align-items-center">
                        <img
                            src="{{ $viewOrganisation->logo_path
                                    ? asset('storage/'.$viewOrganisation->logo_path)
                                    : asset('assets/img/avatars/2.png') }}"
                            alt="logo"
                            class="rounded border border-2 border-white me-3"
                            style="width:80px;height:80px;object-fit:cover;">

                        <div>

                            <h4 class="modal-title text-dark mb-1">
                                {{ $viewOrganisation->name }}
                            </h4>

                            <div class="d-flex flex-wrap gap-2">

                                <span class="badge bg-white text-primary">
                                    {{ $viewOrganisation->organisation_type }}
                                </span>

                                @if($viewOrganisation->state)
                                    <span class="badge bg-label-light">
                                        {{ $viewOrganisation->state->name }}
                                    </span>
                                @endif

                                @if($viewOrganisation->district)
                                    <span class="badge bg-label-light">
                                        {{ $viewOrganisation->district->name }}
                                    </span>
                                @endif

                            </div>

                        </div>
                    </div>
                        {{-- Contact Information --}}
                        <div class="col-md-6">
                            <div class="card shadow-none border h-100">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">
                                        Contact Information
                                    </h6>
                                </div>
                                <div class="card-body ">
                                    <div class="row mb-4 mt-4">

                                        <label class="col-sm-4 text-muted">
                                            Phone
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->phone ?: '-' }}
                                        </div>

                                    </div>
                                    <div class="row mb-4">

                                        <label class="col-sm-4 text-muted">
                                            Email
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->email ?: '-' }}
                                        </div>

                                    </div>
                                    <div class="row">
                                        <label class="col-sm-4 text-muted">
                                            Website
                                        </label>
                                        <div class="col-sm-8">

                                            @if($viewOrganisation->website)

                                                <a href="{{ $viewOrganisation->website }}"
                                                target="_blank"
                                                class="fw-medium">

                                                    {{ $viewOrganisation->website }}

                                                </a>

                                            @else
                                                -
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Information --}}
                        <div class="col-md-6">

                            <div class="card shadow-none border h-100">

                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">
                                        Address Information
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <div class="row mb-4 mt-4">

                                        <label class="col-sm-4 text-muted">
                                            State
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->state->name ?? '-' }}
                                        </div>

                                    </div>

                                    <div class="row mb-4">

                                        <label class="col-sm-4 text-muted">
                                            District
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->district->name ?? '-' }}
                                        </div>

                                    </div>

                                    <div class="row mb-4">

                                        <label class="col-sm-4 text-muted">
                                            Pincode
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->pincode ?: '-' }}
                                        </div>

                                    </div>

                                    <div class="row">

                                        <label class="col-sm-4 text-muted">
                                            Address
                                        </label>

                                        <div class="col-sm-8 fw-medium">
                                            {{ $viewOrganisation->address ?: '-' }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                        {{-- Social Links --}}
                        @if(!empty(array_filter($viewOrganisation->social_links ?? [])))

                            <div class="col-md-12">

                                <div class="card shadow-none border">

                                    <div class="card-header border-bottom">
                                        <h6 class="mb-0">
                                            Social Media Links
                                        </h6>
                                    </div>

                                    <div class="card-body">

                                        <div class="row g-3">

                                            @foreach($viewOrganisation->social_links as $key => $value)

                                                @if($value)

                                                    <div class="col-md-4">

                                                        <a href="{{ $value }}"
                                                        target="_blank"
                                                        class="text-decoration-none">

                                                            <div class="border rounded p-3 h-100">

                                                                <div class="d-flex align-items-center">

                                                                    <div class="avatar avatar-sm me-3">

                                                                        <span class="avatar-initial rounded bg-label-primary">

                                                                            @if($key == 'facebook')
                                                                                <i class="ri-facebook-fill"></i>
                                                                            @elseif($key == 'instagram')
                                                                                <i class="ri-instagram-line"></i>
                                                                            @elseif($key == 'twitter')
                                                                                <i class="ri-twitter-x-line"></i>
                                                                            @else
                                                                                <i class="ri-global-line"></i>
                                                                            @endif

                                                                        </span>

                                                                    </div>

                                                                    <div>

                                                                        <small class="text-muted d-block">
                                                                            {{ ucfirst($key) }}
                                                                        </small>

                                                                        <span class="fw-medium text-dark">
                                                                            Visit Link
                                                                        </span>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </a>

                                                    </div>

                                                @endif

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endif
                    </div>
