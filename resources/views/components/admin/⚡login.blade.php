<?php

use App\Models\Admin;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.auth')]
class extends Component {

    public string $email, $email_reset, $mobile_reset, $password, $pass, $pass_confirmation;
    public ?string $remember = null;
    public bool $mobile_verified = false;
    public bool $email_verified = false;
    public Admin $official;
    public int $createForm = 3;
    protected array $rules = [
        'email' => 'required|max:50',
        'password' => 'required|max:50',
    ];
    protected array $messages = [
        'email' => [
            'required' => 'Please enter your email or mobile no.',
            'max' => 'Too long!'
        ],
        'password' => [
            'required' => 'Please enter your password.',
            'max' => 'Too long!'
        ]
    ];

    public function login()
    {
        //dd($this->email);
        $this->validate($this->rules, $this->messages);
        if (RateLimiter::tooManyAttempts(Str::lower($this->email) . '|' . request()->ip(), $perMinute = 3)) {
            $this->addError('email', 'Too many login attempts. Try again later');
            return null;
        }
        RateLimiter::increment(Str::lower($this->email) . '|' . request()->ip());

        if (is_numeric($this->email)) {
            $this->field = 'mobile';
        } else {
            $this->field = 'email';
        }
        $user = Admin::where([$this->field => $this->email])->with('roles')->active()->first();
        if (!$user) {
            $this->addError('password', 'No account found or your account is not active.');
            return null;
        }

        $rol_id = $user->roles->first();
        if (!$rol_id) {
            $this->addError('password', 'This user does not have access to any services.');
            return null;
        } else {
            if(is_null($user->current_role_id)) $user->update(['current_role_id' => $rol_id->id]);
        }
        if (Auth::guard('admin')->attempt([$this->field => $this->email, 'password' => $this->password], $this->remember)) {
            return redirect()->intended('/admin/home');
        }
        $this->addError('password', 'Invalid credentials. Please try again.');
    }

    public function forgotPassword(): void
    {
        $this->createForm = 1;
    }

    public function sendSMS()
    {
        $this->validate([
            'mobile_reset' => 'required|integer|between:6000000000,9999999999|exists:admins,mobile',
        ], [
            'mobile_reset' => [
                'required' => 'Please enter your 10 digit Indian mobile no.',
                'integer' => 'Should be a valid numeric mobile no',
                'between' => 'Should be a valid Indian mobile no.',
                'exists' => 'This mobile no is not registered with us.'
            ],
        ]);
        $this->dispatch('showOtpModal', heading: "Mobile OTP Verification", subheading: "We sent a verification code to your mobile. Enter the code from the mobile in the field below.", is_mobile: true, mobile_no: $this->mobile_reset);
    }

    public function sendEmail(): void
    {
        $this->validate([
            'email_reset' => 'required|email|min:2|max:50|exists:officials,email',
        ], [
            'email_reset' => [
                'required' => 'Please enter your email ID.',
                'email' => 'Please enter a valid email ID.',
                'min' => 'Email ID is too short.',
                'max' => 'Email ID is too long.',
                'exists' => 'This email ID is not registered with us.'
            ]
        ]);
        $this->dispatch('showOtpModal', heading: "Email OTP Verification", subheading: "We sent a verification code to your email ID. Enter the code from the email in the field below.", is_mobile: false, email_id: $this->email_reset);

    }

    #[On('mobileVerified')]
    public function mobileVerified($verified = true): void
    {
        $this->mobile_verified = $verified;
    }

    #[On('emailVerified')]
    public function emailVerified($verified = true): void
    {
        $this->email_verified = $verified;
    }

    public function cancel(): void
    {
        $this->createForm = 0;
    }

    public function newPassword(): void
    {
        if (!$this->mobile_verified || !$this->email_verified) {
            $this->addError('email_reset', 'Please verify your mobile and email to proceed.');
            return;
        }
        $this->official = Admin::active()->where(['mobile' => $this->mobile_reset, 'email' => $this->email_reset])
            ->first();
        if (!count($this->official) > 0) {
            $this->addError('email_reset', 'No official account found with the provided mobile and email.');
            return;
        }
        $this->createForm = 2;
    }

    public function resetPassword(): void
    {
        $this->validate([
            'pass' => 'required|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,16}$/|confirmed',
        ], [
            'pass' => [
                'required' => 'Password is required.',
                'regex' => 'Password must contain one digit from 0 to 9, one lowercase letter, one uppercase letter, one special character, no space, and it must be 8-16 characters long.',
                'confirmed' => 'Password and confirm password doesn\'t match.'
            ],
        ]);
        $this->official->password = bcrypt($this->pass);
        $this->official->save();
        $this->reset();
        session()->flash('message', 'Your password has been reset successfully');
        $this->createForm = 0;
    }
};
?>
<div>
    @if($createForm == 1)

        <div class="card p-md-7 p-1">
            <h4 class="mb-3 pt-2 text-center">Reset Your Password!</h4>
            <div class="row g3">
                <form wire:ignore.self>
                    <div class="mb-3 col-sm-12">
                        <label class="form-label" for="mobile">Your Registered Mobile No: <i class="text-danger">*</i>
                        </label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary waves-effect" type="button" id="button-mobile">+91
                            </button>
                            <input type="hidden" wire:model="mobile_verified">
                            <input wire:model="mobile_reset" type="tel" id="mobile_reset" maxlength="10" minlength="10"
                                   name="mobile" class="form-control" placeholder="Mobile No" aria-label="Mobile No"
                                   aria-describedby="button-mobile" @if($mobile_verified) disabled @endif>
                            <button wire:click="sendSMS"
                                    class="btn @if($mobile_verified) btn-outline-success @else btn-outline-primary @endif waves-effect"
                                    type="button" id="button-mobile"
                                    @if($mobile_verified) disabled @endif>@if($mobile_verified)
                                    <span class="ti ti-check"></span> Verified
                                @else
                                    Verify
                                @endif</button>
                        </div>
                        @error('mobile_reset')<em class="error">{{$message}}</em> @enderror
                    </div>
                    <div class="mb-3 col-sm-12">
                        <label class="form-label" for="email">Your Registered Email ID: <i class="text-danger">*</i></label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary waves-effect" type="button" id="button-mobile">@</button>
                            <input type="hidden" wire:model="email_verified">
                            <input wire:model="email_reset" type="email" id="email_reset" maxlength="50" minlength="2"
                                   name="email" class="form-control" placeholder="Email ID" aria-label="Email ID"
                                   aria-describedby="button-email" @if($email_verified) disabled @endif>
                            <button wire:click="sendEmail"
                                    class="btn @if($email_verified) btn-outline-success @else btn-outline-primary @endif waves-effect"
                                    type="button" id="button-email"
                                    @if($email_verified) disabled @endif>@if($email_verified)
                                    <span class="ti ti-check"></span> Verified
                                @else
                                    Verify
                                @endif</button>
                        </div>
                        @error('email_reset')<em class="error">{{$message}}</em> @enderror
                    </div>
                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button wire:click="cancel" type="button" class="btn btn-label-secondary btn-prev waves-effect">
                            <i class="icon-base ri ri-arrow-left-line"></i>
                            <span class="align-middle d-sm-inline-block d-none">Cancel</span>
                        </button>
                        <button wire:click="newPassword" type="button"
                                class="btn btn-primary btn-next waves-effect waves-light">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                            <i class="icon-base ri ri-arrow-right-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @elseif($createForm == 2)
        <div class="card p-md-7 p-1">
            <h4 class="mb-3 pt-2 text-center">Set New Password</h4>
            <div class="row g-3">
                <form wire:ignore.self>
                    <div class="col-sm-12 mb-3 form-password-toggle">
                        <label for="pass" class="form-label">Password<em class="text-danger">*</em></label>
                        <div class="input-group input-group-merge">
                            <input wire:model="pass" type="password" id="pass" class="form-control"  placeholder="············" aria-describedby="pass" required>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('pass')<em class="error">{{$message}}</em>@enderror
                    </div>
                    <div class="col-sm-12 mb-3 form-password-toggle">
                        <label for="pass_confirmation" class="form-label">Confirm Password<em class="text-danger">*</em></label>
                        <div class="input-group input-group-merge">
                            <input wire:model="pass_confirmation" type="password" id="pass_confirmation" class="form-control"  placeholder="············" aria-describedby="pass_confirmation" required>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('pass_confirmation')<em class="error">{{$message}}</em>@enderror
                    </div>
                    <div class="col-12 d-flex justify-content-between">
                        <button wire:click.prevent="cancel" class="btn btn-label-secondary btn-next waves-effect waves-light float-right mt-4">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Cancel</span>
                            <i class="icon-base ri ri-folder-2-line me-sm-1 me-0"></i>
                        </button>
                        <button wire:click.prevent="resetPassword" class="btn btn-primary btn-next waves-effect waves-light float-right mt-4">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Reset</span>
                            <i class="icon-base ri ri-arrow-right-line me-sm-1 me-0"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="card p-md-7 p-1">
            <div class="d-flex justify-content-center">
                <img src="{{ asset('assets/img/favicon/favicon.png') }}" class="w-px-75 h-px-75"/>
            </div>
            <div class="app-brand justify-content-center mt-5">
                <a href="#" class="app-brand-link gap-2">
                    <span class="app-brand-text demo text-heading fw-semibold">{{ config('app.name') }}</span>
                </a>
            </div>
            <div class="card-body mt-1">
                <h4 class="mb-1 text-center">Employee Login</h4>
                <p class="mb-5 text-center">Please sign-in to your account</p>

                <form wire:submit.prevent="login" id="formAuthentication" class="mb-5" novalidate="novalidate">
                    <div class="form-floating form-floating-outline mb-5 form-control-validation">
                        <input
                            wire:model="email"
                            type="text"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            placeholder="Enter your email or mobile no"
                            autofocus/>
                        <label for="email">Email or Mobile No</label>
                        @error('email')
                        <div
                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="mb-5">
                        <div class="form-password-toggle form-control-validation">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input
                                        wire:model="password"
                                        type="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password"/>
                                    <label for="password">Password</label>

                                </div>
                                <span class="input-group-text cursor-pointer"><i
                                        class="icon-base ri ri-eye-off-line icon-20px"></i></span>

                            </div>
                            @error('password')
                            <div
                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">{{$message}}</div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-5 d-flex justify-content-between mt-5">
                        <div class="form-check mt-2">
                            <input wire:model="remember" class="form-check-input" type="checkbox" id="remember-me"/>
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        <a wire:click.prevent="forgotPassword" href="javascript:void(0)" class="float-end mb-1 mt-2">
                            <span>Forgot Password?</span>
                        </a>
                    </div>

                    <div class="col-12 d-flex justify-content-around my-8">
                        <a class="btn btn-secondary waves-effect waves-light" href="{{ route('welcome') }}"><span class="icon-base ri ri-arrow-left-long-line"></span> Cancel</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Sign in<span class="icon-base ri ri-arrow-right-long-line"></span></button>
                    </div>

                </form>
            </div>
        </div>
    @endif
    @livewire('modal.otp-modal')
    <div wire:loading wire:target="forgotPassword,cancel,login,newPassword,resetPassword">
        @include('utilities.backdrop')
    </div>
    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}"/>
    @endpush
</div>

