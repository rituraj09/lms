<?php
// app/Livewire/Admin/Auth/Login.php
namespace App\Livewire\Admin\Auth;

use App\Models\Admin;
use App\Services\OrganisationContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    // ── Login Fields ───────────────────────────────────────────
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;
    public string $field    = 'email';

    // ── Forgot Password Fields ─────────────────────────────────
    public string $email_reset       = '';
    public string $mobile_reset      = '';
    public string $pass              = '';
    public string $pass_confirmation = '';

    // ── State ──────────────────────────────────────────────────
    public bool   $mobile_verified = false;
    public bool   $email_verified  = false;
    public int    $createForm      = 0;  // 0=login | 1=forgot | 2=new password
    public ?Admin $official        = null;

    // ─────────────────────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────────────────────

    public function login(): void
    {
        $this->validate(
            [
                'email'    => 'required|max:50',
                'password' => 'required|max:50',
            ],
            [
                'email.required'    => 'Please enter your email or mobile no.',
                'email.max'         => 'Too long!',
                'password.required' => 'Please enter your password.',
                'password.max'      => 'Too long!',
            ]
        );

        // Rate Limiting
        $key = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('email', 'Too many login attempts. Please try again later.');
            return;
        }

        RateLimiter::increment($key);

        // Determine login field
        $this->field = is_numeric($this->email) ? 'mobile' : 'email';

        // Find active admin
        $admin = Admin::where($this->field, $this->email)
                      ->with('roles')
                      ->active()
                      ->first();

        if (!$admin) {
            $this->addError('password', 'No account found or your account is not active.');
            return;
        }

        // Check role
        $role = $admin->roles->first();

        if (!$role) {
            $this->addError('password', 'This account does not have access to any services.');
            return;
        }

        // Attempt Auth
        if (Auth::guard('admin')->attempt(
            [$this->field => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            RateLimiter::clear($key);
            $this->handleOrganisationContext($admin);
            $this->redirect(route('admin.home'), navigate: true);
            return;
        }

        $this->addError('password', 'Invalid credentials. Please try again.');
    }

    protected function handleOrganisationContext(Admin $admin): void
    {
        if (!$admin->isSuperAdmin()) {
            $singleOrg = $admin->getSingleOrganisation();
            if ($singleOrg) {
                $admin->setCurrentOrganisation($singleOrg->id);
                OrganisationContext::set($singleOrg->id);
            }
        }
    }

    // ─────────────────────────────────────────────────────────
    // FORGOT PASSWORD — STEP 1
    // ─────────────────────────────────────────────────────────

    public function forgotPassword(): void
    {
        $this->reset(['email_reset', 'mobile_reset', 'mobile_verified', 'email_verified']);
        $this->createForm = 1;
    }

    public function cancel(): void
    {
        $this->reset([
            'email_reset', 'mobile_reset',
            'pass', 'pass_confirmation',
            'mobile_verified', 'email_verified',
            'official',
        ]);
        $this->createForm = 0;
    }

    public function sendSMS(): void
    {
        $this->validate(
            [
                'mobile_reset' => 'required|digits:10|exists:admins,mobile',
            ],
            [
                'mobile_reset.required' => 'Please enter your 10 digit mobile no.',
                'mobile_reset.digits'   => 'Should be a valid 10 digit mobile no.',
                'mobile_reset.exists'   => 'This mobile no is not registered with us.',
            ]
        );

        $this->dispatch('showOtpModal',
            heading:    'Mobile OTP Verification',
            subheading: 'We sent a verification code to your mobile. Enter the code below.',
            is_mobile:  true,
            mobile_no:  $this->mobile_reset,
        );
    }

    public function sendEmail(): void
    {
        $this->validate(
            [
                'email_reset' => 'required|email|min:2|max:50|exists:admins,email',
            ],
            [
                'email_reset.required' => 'Please enter your email ID.',
                'email_reset.email'    => 'Please enter a valid email ID.',
                'email_reset.min'      => 'Email ID is too short.',
                'email_reset.max'      => 'Email ID is too long.',
                'email_reset.exists'   => 'This email ID is not registered with us.',
            ]
        );

        $this->dispatch('showOtpModal',
            heading:    'Email OTP Verification',
            subheading: 'We sent a verification code to your email. Enter the code below.',
            is_mobile:  false,
            email_id:   $this->email_reset,
        );
    }

    // ─────────────────────────────────────────────────────────
    // FORGOT PASSWORD — STEP 2
    // ─────────────────────────────────────────────────────────

    public function newPassword(): void
    {
        if (!$this->mobile_verified || !$this->email_verified) {
            $this->addError('email_reset', 'Please verify both your mobile and email to proceed.');
            return;
        }

        $this->official = Admin::active()
            ->where('mobile', $this->mobile_reset)
            ->where('email', $this->email_reset)
            ->first();

        if (!$this->official) {
            $this->addError('email_reset', 'No account found with the provided mobile and email.');
            return;
        }

        $this->createForm = 2;
    }

    // ─────────────────────────────────────────────────────────
    // FORGOT PASSWORD — STEP 3
    // ─────────────────────────────────────────────────────────

    public function resetPassword(): void
    {
        $this->validate(
            [
                'pass' => [
                    'required',
                    'confirmed',
                    'regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,16}$/',
                ],
            ],
            [
                'pass.required'  => 'Password is required.',
                'pass.confirmed' => 'Password and confirm password do not match.',
                'pass.regex'     => 'Password must be 8-16 characters with uppercase, lowercase, number & special character.',
            ]
        );

        if (!$this->official) {
            $this->addError('pass', 'Session expired. Please start again.');
            $this->createForm = 0;
            return;
        }

        $this->official->update([
            'password' => Hash::make($this->pass),
        ]);

        $this->reset();
        $this->createForm = 0;

        session()->flash('success', 'Password reset successfully. Please login.');
    }

    // ─────────────────────────────────────────────────────────
    // OTP EVENT LISTENERS
    // ─────────────────────────────────────────────────────────

    #[On('mobileVerified')]
    public function mobileVerified(bool $verified = true): void
    {
        $this->mobile_verified = $verified;
    }

    #[On('emailVerified')]
    public function emailVerified(bool $verified = true): void
    {
        $this->email_verified = $verified;
    }

    // ─────────────────────────────────────────────────────────
    // RENDER
    // ─────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.auth.login');
    }
}
