{{-- resources/views/livewire/admin/auth/login.blade.php --}}

<div>
    {{-- Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- STEP 1: FORGOT PASSWORD — Verify Mobile & Email           --}}
    {{-- ========================================================= --}}
    @if ($createForm === 1)

        <div class="card p-md-7 p-1">
            <h4 class="mb-3 pt-2 text-center">Reset Your Password!</h4>
            <p class="text-center text-muted mb-4">
                Verify your registered mobile and email to continue.
            </p>

            <div class="row g-3">
                <form wire:ignore.self>

                    {{-- Mobile --}}
                    <div class="mb-3 col-sm-12">
                        <label class="form-label" for="mobile_reset">
                            Registered Mobile No <i class="text-danger">*</i>
                        </label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary waves-effect" type="button">+91</button>

                            <input type="hidden" wire:model="mobile_verified">

                            <input wire:model="mobile_reset" type="tel" id="mobile_reset" maxlength="10"
                                minlength="10" class="form-control @error('mobile_reset') is-invalid @enderror"
                                placeholder="10 digit mobile no" @disabled($mobile_verified)>

                            <button wire:click="sendSMS" type="button" @class([
                                'btn waves-effect',
                                'btn-outline-success' => $mobile_verified,
                                'btn-outline-primary' => !$mobile_verified,
                            ])
                                @disabled($mobile_verified)>
                                @if ($mobile_verified)
                                    <i class="ti ti-check me-1"></i> Verified
                                @else
                                    Verify
                                @endif
                            </button>
                        </div>
                        @error('mobile_reset')
                            <em class="text-danger small d-block mt-1">{{ $message }}</em>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3 col-sm-12">
                        <label class="form-label" for="email_reset">
                            Registered Email ID <i class="text-danger">*</i>
                        </label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary waves-effect" type="button">@</button>

                            <input type="hidden" wire:model="email_verified">

                            <input wire:model="email_reset" type="email" id="email_reset" maxlength="50"
                                class="form-control @error('email_reset') is-invalid @enderror"
                                placeholder="Your email ID" @disabled($email_verified)>

                            <button wire:click="sendEmail" type="button" @class([
                                'btn waves-effect',
                                'btn-outline-success' => $email_verified,
                                'btn-outline-primary' => !$email_verified,
                            ])
                                @disabled($email_verified)>
                                @if ($email_verified)
                                    <i class="ti ti-check me-1"></i> Verified
                                @else
                                    Verify
                                @endif
                            </button>
                        </div>
                        @error('email_reset')
                            <em class="text-danger small d-block mt-1">{{ $message }}</em>
                        @enderror
                    </div>

                    {{-- Verification Status --}}
                    <div class="col-12 mb-3">
                        <div class="d-flex gap-3">
                            <span @class([
                                'badge',
                                'bg-success' => $mobile_verified,
                                'bg-secondary' => !$mobile_verified,
                            ])>
                                <i class="ti ti-device-mobile me-1"></i>
                                Mobile {{ $mobile_verified ? 'Verified' : 'Pending' }}
                            </span>
                            <span @class([
                                'badge',
                                'bg-success' => $email_verified,
                                'bg-secondary' => !$email_verified,
                            ])>
                                <i class="ti ti-mail me-1"></i>
                                Email {{ $email_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button wire:click="cancel" type="button" class="btn btn-label-secondary waves-effect">
                            <i class="icon-base ri ri-arrow-left-line me-1"></i> Cancel
                        </button>
                        <button wire:click="newPassword" type="button" class="btn btn-primary waves-effect waves-light"
                            @disabled(!$mobile_verified || !$email_verified)>
                            Next <i class="icon-base ri ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- STEP 2: SET NEW PASSWORD                                  --}}
        {{-- ========================================================= --}}
    @elseif($createForm === 2)
        <div class="card p-md-7 p-1">
            <h4 class="mb-3 pt-2 text-center">Set New Password</h4>
            <p class="text-center text-muted mb-4">
                Choose a strong password for your account.
            </p>

            <div class="row g-3">
                <form wire:ignore.self>

                    {{-- New Password --}}
                    <div class="col-sm-12 mb-3 form-password-toggle">
                        <label for="pass" class="form-label">
                            New Password <em class="text-danger">*</em>
                        </label>
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <input wire:model="pass" type="password" id="pass"
                                    class="form-control @error('pass') is-invalid @enderror" placeholder="············">
                                <label for="pass">New Password</label>
                            </div>
                            <span class="input-group-text cursor-pointer">
                                <i class="ti ti-eye-off"></i>
                            </span>
                        </div>
                        @error('pass')
                            <em class="text-danger small d-block mt-1">{{ $message }}</em>
                        @enderror
                        <small class="text-muted">
                            8-16 chars · uppercase · lowercase · number · special character
                        </small>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-sm-12 mb-3 form-password-toggle">
                        <label for="pass_confirmation" class="form-label">
                            Confirm Password <em class="text-danger">*</em>
                        </label>
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <input wire:model="pass_confirmation" type="password" id="pass_confirmation"
                                    class="form-control @error('pass_confirmation') is-invalid @enderror"
                                    placeholder="············">
                                <label for="pass_confirmation">Confirm Password</label>
                            </div>
                            <span class="input-group-text cursor-pointer">
                                <i class="ti ti-eye-off"></i>
                            </span>
                        </div>
                        @error('pass_confirmation')
                            <em class="text-danger small d-block mt-1">{{ $message }}</em>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button wire:click.prevent="cancel" class="btn btn-label-secondary waves-effect waves-light">
                            <i class="icon-base ri ri-arrow-left-line me-1"></i> Cancel
                        </button>
                        <button wire:click.prevent="resetPassword" class="btn btn-primary waves-effect waves-light">
                            Reset Password
                            <i class="icon-base ri ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- STEP 0: MAIN LOGIN                                        --}}
        {{-- ========================================================= --}}
    @else
        <div class="card p-md-7 p-1">

            {{-- Logo --}}
            <div class="d-flex justify-content-center mb-4">
                <img src="{{ asset('assets/img/favicon/favicon.png') }}" class="w-px-75 h-px-75"
                    alt="{{ config('app.name') }}">
            </div>

            {{-- App Name --}}
            <div class="app-brand justify-content-center mb-1">
                <a href="#" class="app-brand-link gap-2">
                    <span class="app-brand-text demo text-heading fw-semibold">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>

            <div class="card-body mt-1">
                <h4 class="mb-1 text-center">Admin Login</h4>
                <p class="mb-5 text-center text-muted">Please sign-in to your account</p>

                <form wire:submit.prevent="login" id="formAuthentication" class="mb-5" novalidate>

                    {{-- Email / Mobile --}}
                    <div class="form-floating form-floating-outline mb-5 form-control-validation">
                        <input wire:model="email" type="text"
                            class="form-control @error('email') is-invalid @enderror" id="email"
                            placeholder="Enter your email or mobile no" autofocus>
                        <label for="email">Email or Mobile No</label>
                        @error('email')
                            <div
                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <div class="form-password-toggle form-control-validation">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input wire:model="password" type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="············" aria-describedby="password">
                                    <label for="password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer">
                                    <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                                </span>
                            </div>
                            @error('password')
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Remember Me + Forgot Password --}}
                    <div class="mb-5 d-flex justify-content-between mt-5">
                        <div class="form-check mt-2">
                            <input wire:model="remember" class="form-check-input" type="checkbox" id="remember-me">
                            <label class="form-check-label" for="remember-me">
                                Remember Me
                            </label>
                        </div>
                        <a wire:click.prevent="forgotPassword" href="javascript:void(0)" class="float-end mb-1 mt-2">
                            Forgot Password?
                        </a>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-12 d-flex justify-content-around my-8">
                        <a class="btn btn-secondary waves-effect waves-light" href="{{ route('welcome') }}">
                            <span class="icon-base ri ri-arrow-left-long-line me-1"></span>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            Sign in
                            <span class="icon-base ri ri-arrow-right-long-line ms-1"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    @endif

    {{-- Loading Backdrop --}}
    <div wire:loading wire:target="forgotPassword,cancel,login,newPassword,resetPassword,sendSMS,sendEmail">
        @include('utilities.backdrop')
    </div>

</div>
