<?php

use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $heading = 'Heading';
    public string $subheading = 'Sub Heading';
    public array $otp = ['', '', '', '', '', '']; // Array to store individual OTP digits
    public string $fullOtp = ''; // Combined OTP
    public bool $otpSent = false;
    public bool $otpExpired = false;
    public bool $resendDisabled = true;
    public int $resendTimeout = 90; // Resend timeout in seconds
    protected string $generated_otp;
    public string $mobile_no;
    public string $email_id;
    public bool $is_mobile;

    #[On('showOtpModal')]
    public function showModal($heading, $subheading, $is_mobile, $mobile_no = '', $email_id = '')
    {
        $this->is_mobile = $is_mobile;
        $this->is_mobile ? $this->mobile_no = $mobile_no : $this->email_id = $email_id;
        $this->heading = $heading;
        $this->subheading = $subheading;
        $this->otp = ['', '', '', '', '', ''];
        $this->fullOtp = '';
        $this->otpSent = true;
        $this->otpExpired = false;
        $this->resendDisabled = true;

        $this->resendOtp();
        $this->dispatch('show-bootstrap-modal');
    }

    public function resendOtp(): void
    {
        config('app.debug')  ? $this->generated_otp = Carbon::now()->format('dmy') : $this->generated_otp = str_pad(random_int(0, 999999),6,"0",STR_PAD_LEFT);
        $otpExpiry = Carbon::now()->addMinutes(5); // OTP expires in 5 minutes
        if ($this->is_mobile) {
            if (RateLimiter::tooManyAttempts('mobile_otp', 3)) {
                session()->flash('error', 'Too many attempts. Please try again later.');
                return;
            }
            RateLimiter::hit('mobile_otp');
            session()->put('mobile_otp', $this->generated_otp);
            session()->put('mobile_otp_expiry', $otpExpiry);
            $this->sendMobileOTP($this->mobile_no, $this->generated_otp);
        } else {

            if (RateLimiter::tooManyAttempts('email_otp', 3)) {
                session()->flash('error', 'Too many attempts. Please try again later.');
                return;
            }

            RateLimiter::hit('email_otp');
            session()->put('email_otp', $this->generated_otp);
            session()->put('email_otp_expiry', $otpExpiry);
            $this->sendEmailOTP($this->email_id, $this->generated_otp);
        }
        $this->resendDisabled = true;
        $this->stopTimer();
        $this->startTimer();
        session()->flash('message', 'OTP sent successfully!');
    }

    protected function sendMobileOTP($mobile, $otp)
    {
        //gateway api
        // Send OTP via SMS (e.g., using Twilio)
        \App\Helpers\Helper::sendSMS($mobile,$otp);

    }

    protected function sendEmailOTP($email, $otp): void
    {
        $mail = new \App\Services\BrevoMailService();
        $html = view('emails.otp', ['otp'=>$otp])->render();
        $mail->send($email, config('app.name'), 'One Time Password', $html);
    }

    public function verifyOtp(): void
    {
        $this->fullOtp = implode('', $this->otp);
        if ($this->is_mobile) {
            if (RateLimiter::tooManyAttempts('mobile_verify', 3)) {
                session()->flash('error', 'Too many attempts. Please try again later.');
                return;
            }
            RateLimiter::hit('mobile_verify');
        } else {
            if (RateLimiter::tooManyAttempts('email_verify', 3)) {
                session()->flash('error', 'Too many attempts. Please try again later.');
                return;
            }

            RateLimiter::hit('email_verify');
        }

        if (strlen($this->fullOtp) === 6) {
            $storedOtp = $this->is_mobile ? session('mobile_otp') : session('email_otp');
            $otpExpiry = $this->is_mobile ? session('mobile_otp_expiry') : session('email_otp_expiry');
            if (Carbon::now()->gt($otpExpiry)) {
                $this->otpExpired = true;
                session()->flash('error', 'OTP has expired. Please request a new one.');
                return;
            }
            if ($this->fullOtp == $storedOtp) {
                if ($this->is_mobile) {
                    $this->dispatch('mobileVerified', verified: true);
                    session()->put('mobile', $this->mobile_no);
                } else {
                    $this->dispatch('emailVerified', verified: true);
                    session()->put('email', $this->email_id);
                }
                $this->stopTimer();
                $this->hideModal();
            } else {
                session()->flash('error', 'Invalid OTP. Please try again.');
            }

        } else {
            session()->flash('error', 'Invalid OTP. Please enter a 6-digit code.');
        }

    }
    #[On('hideOtpModal')]
    public function hideModal(): void
    {
        $this->dispatch('hide-bootstrap-modal');
    }

    protected function startTimer(): void
    {
        $this->dispatch('start-resend-timer');
    }

    protected function stopTimer(): void
    {
        $this->dispatch('stop-resend-timer');
    }

};
?>

<div>
    <div
        class="modal fade"
        id="animationModal"
        tabindex="-1"
        aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-1 pt-2">{{ $heading }}</h4>
                                    <p class="text-start mb-4">
                                        {{$subheading}}
                                    </p>
                                    <p class="mb-0 fw-medium">Type your 6 digit security code</p>
                                    <form wire:submit.prevent id="twoStepsForm"
                                          class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                        <div class="mb-3 fv-plugins-icon-container">
                                            <div
                                                class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                                <input wire:model="otp.0" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1" id="focus" autofocus>
                                                <input wire:model="otp.1" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1">
                                                <input wire:model="otp.2" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1">
                                                <input wire:model="otp.3" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1">
                                                <input wire:model="otp.4" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1">
                                                <input wire:model="otp.5" type="tel"
                                                       class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2"
                                                       maxlength="1">
                                            </div>
                                            <input type="hidden" name="otp" wire:model="fullOtp">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                        </div>
                                        <button wire:click.prevent="verifyOtp"
                                                class="btn btn-primary d-grid w-100 mb-3 waves-effect waves-light">
                                            Submit
                                        </button>
                                        <div class="text-center">
                                            Didn't get the code?
                                            <button style="background: none;border: none;padding: 0"
                                                    class="link-primary text-sm-start" wire:click.prevent="resendOtp"
                                                    wire:loading.attr="disabled"
                                                    wire:target="resendOtp" {{ $resendDisabled ? 'disabled' : '' }}>
                                                Resend
                                            </button>
                                            <span id="resend-timer">
                                                @if ($resendDisabled)
                                                    in {{ $resendTimeout }} seconds
                                                @endif
                                                </span>
                                            <br>
                                            @if (session('message'))
                                                <br>
                                                <em class="text-success">
                                                    {{ session('message') }}
                                                </em>
                                            @endif
                                            @if (session('error'))
                                                <br>
                                                <em class="text-danger">
                                                    {{ session('error') }}
                                                </em>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
    let modal = new bootstrap.Modal(document.getElementById('animationModal'));
    let interval;
    window.addEventListener('show-bootstrap-modal', e => {
        modal.show();
    });
    window.addEventListener('hide-bootstrap-modal', e => {
        modal.hide();
    });
    window.addEventListener('start-resend-timer', e => {
        let timer = {{ $resendTimeout }};
        const timerElement = document.getElementById('resend-timer');
        interval = setInterval(() => {
            timer--;
            if (timerElement) {
                timerElement.textContent = 'in '+ timer + ' seconds';
            }
            if (timer <= 0) {
                clearInterval(interval);
                @this.set('resendDisabled', false);
                if (timerElement) {
                    timerElement.remove();
                }
            }
        }, 1000);
    });
    window.addEventListener('stop-resend-timer', e => {
        if(interval) clearInterval(interval);
    });
    (function () {
        let maskWrapper = document.querySelector('.numeral-mask-wrapper');

        for (let pin of maskWrapper.children) {
            pin.onkeyup = function (e) {
                // Check if the key pressed is a number (0-9)
                if (/^\d$/.test(e.key)) {
                    // While entering value, go to next
                    if (pin.nextElementSibling) {
                        if (this.value.length === parseInt(this.attributes['maxlength'].value)) {
                            pin.nextElementSibling.focus();
                        }
                    }
                } else if (e.key === 'Backspace') {
                    // While deleting entered value, go to previous
                    if (pin.previousElementSibling) {
                        pin.previousElementSibling.focus();
                    }
                }
            };
            // Prevent the default behavior for the minus key
            pin.onkeypress = function (e) {
                if (e.key === '-') {
                    e.preventDefault();
                }
            };
        }

        const twoStepsForm = document.querySelector('#twoStepsForm');

        // Form validation for Add new record
        if (twoStepsForm) {

            const numeralMaskList = twoStepsForm.querySelectorAll('.numeral-mask');
            const keyupHandler = function () {
                let otpFlag = true,
                    otpVal = '';
                numeralMaskList.forEach(numeralMaskEl => {
                    if (numeralMaskEl.value === '') {
                        otpFlag = false;
                        twoStepsForm.querySelector('[name="otp"]').value = '';
                    }
                    otpVal = otpVal + numeralMaskEl.value;
                });
                if (otpFlag) {
                    twoStepsForm.querySelector('[name="otp"]').value = otpVal;
                }
            };
            numeralMaskList.forEach(numeralMaskEle => {
                numeralMaskEle.addEventListener('keyup', keyupHandler);
            });
        }
    })();
</script>
@endscript

