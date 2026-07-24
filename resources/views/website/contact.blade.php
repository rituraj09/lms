@include('website.layouts.header')

<header class="page-head">
    <div class="container">
        <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">📩 Get Started</span>
        <h1>Request a Demo</h1>
        <p>Tell us about your institution and we'll set up a personalized walkthrough.</p>
    </div>
</header>

<section>
    <div class="container grid grid-2" style="align-items:start;gap:48px">
        <!-- FORM -->
        <div class="card reveal">

            <!-- Success State (hidden by default) -->
            <div id="successState" style="display:none;text-align:center;padding:30px 10px;">
                <div
                    style="width:70px;height:70px;border-radius:50%;background:rgba(34,197,94,.12);
                                display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <span style="font-size:34px;">✅</span>
                </div>
                <h3 style="margin-bottom:10px;">Request Received!</h3>
                <p class="text-muted" id="successText" style="max-width:340px;margin:0 auto 24px;">
                    Thank you! Your request has been received. Our team will reach out within 1 business day.
                </p>
                <button type="button" id="backToRequestBtn" class="btn btn-primary" style="justify-content:center;">
                    ← Back to Request
                </button>
            </div>

            <!-- Error Message -->
            <div id="errorMsg"
                style="display:none;background:rgba(239,68,68,.12);
                     color:#ef4444;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;">
                ⚠️ <span id="errorText">Something went wrong. Please try again.</span>
            </div>

            <!-- Form Wrapper -->
            <div id="formWrapper">
                <form id="demoForm" novalidate>
                    @csrf
                    <div class="form-grid">

                        <div class="field">
                            <label>Full Name <span class="req">*</span></label>
                            <input type="text" name="full_name" id="full_name" placeholder="Your name">

                        </div>

                        <div class="field">
                            <label>Institution <span class="req">*</span></label>
                            <input type="text" name="institution" id="institution"
                                placeholder="School / College / Org">
                        </div>

                        <div class="field">
                            <label>Role <span class="req">*</span></label>
                            <select name="role" id="role">
                                <option value="">Select role</option>
                                <option>Principal / Head</option>
                                <option>Teacher / Educator</option>
                                <option>Administrator</option>
                                <option>Parent</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="email" id="email" placeholder="you@example.com">

                        </div>

                        <div class="field">
                            <label>Phone <span class="req">*</span></label>
                            <input type="tel" name="phone" id="phone" placeholder="+91 ...">
                        </div>

                        <div class="field">
                            <label>Preferred Demo Slot</label>
                            <input type="datetime-local" name="preferred_slot" id="preferred_slot">
                        </div>

                        <div class="field full">
                            <label>Message</label>
                            <textarea rows="4" name="message" id="message" placeholder="Tell us what you're looking for..."></textarea>

                        </div>

                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary"
                        style="margin-top:18px;width:100%;justify-content:center">
                        <span id="btnText">Submit Request →</span>
                        <span id="btnLoader" style="display:none;">Submitting...</span>
                    </button>
                </form>
            </div>
            <!-- /Form Wrapper -->

        </div>

        <!-- VALUE SIDE -->
        <div class="reveal">
            <h2>Why Schedule a Demo?</h2>
            <ul style="list-style:none;display:grid;gap:16px;margin-top:20px">
                <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                        style="margin:0">🎯</span>
                    <div><strong>Tailored Walkthrough</strong>
                        <p class="text-muted">See modules relevant to your learners.</p>
                    </div>
                </li>
                <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                        style="margin:0">📊</span>
                    <div><strong>Live Analytics</strong>
                        <p class="text-muted">Explore real dashboards & reports.</p>
                    </div>
                </li>
                <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                        style="margin:0">🔒</span>
                    <div><strong>Data Privacy First</strong>
                        <p class="text-muted">Educational focus only. Your data stays protected.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- ADDRESS + MAP SECTION -->
<section style="padding-top:0">
    <div class="container">
        <div class="card reveal" style="padding:0;overflow:hidden;border-radius:16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;min-height:320px;">
                <div
                    style="padding:32px 28px;display:flex;flex-direction:column;justify-content:center;
                                border-right:1px solid rgba(148,163,184,.15);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <span style="font-size:26px;">📍</span>
                        <div>
                            <p style="font-weight:700;font-size:15px;margin:0;color:var(--text);line-height:1.3;">
                                MindShiksha EdTech<br>Private Limited</p>
                            <p style="font-size:12px;margin:4px 0 0;color:#06B6D4;font-weight:600;">
                                Serving learners across India 🇮🇳</p>
                        </div>
                    </div>
                    <hr style="border:none;border-top:1px solid rgba(148,163,184,.2);margin-bottom:20px;">
                    <div style="display:grid;gap:16px;">
                        <div style="display:flex;gap:12px;align-items:flex-start;">
                            <span style="font-size:16px;min-width:24px;margin-top:2px;">🏢</span>
                            <div>
                                <p
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;letter-spacing:.8px;text-transform:uppercase;">
                                    Registered Office</p>
                                <p style="font-size:13px;color:var(--text);margin:0;line-height:1.7;">
                                    Ward No. 4, Bhehpara Baruah Chuk,<br>Dhemaji, Assam – 787057, India</p>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;align-items:flex-start;">
                            <span style="font-size:16px;min-width:24px;margin-top:2px;">🔢</span>
                            <div>
                                <p
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;letter-spacing:.8px;text-transform:uppercase;">
                                    CIN</p>
                                <p
                                    style="font-size:13px;color:var(--text);margin:0;font-family:monospace;letter-spacing:.6px;">
                                    U62011AS2026PTC030649</p>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;align-items:flex-start;">
                            <span style="font-size:16px;min-width:24px;margin-top:2px;">📅</span>
                            <div>
                                <p
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;letter-spacing:.8px;text-transform:uppercase;">
                                    Date of Incorporation</p>
                                <p style="font-size:13px;color:var(--text);margin:0;line-height:1.6;">18th June, 2026
                                </p>
                                <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Under The Companies Act, 2013
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr style="border:none;border-top:1px solid rgba(148,163,184,.2);margin:20px 0;">
                    <a href="https://www.google.com/maps/search/Dhemaji,+Assam+787057,+India" target="_blank"
                        rel="noopener noreferrer"
                        style="display:inline-flex;align-items:center;gap:8px;background:rgba(6,182,212,.12);color:#06B6D4;
                                  padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;width:fit-content;
                                  transition:background .2s,transform .2s;"
                        onmouseover="this.style.background='rgba(6,182,212,.25)';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.background='rgba(6,182,212,.12)';this.style.transform='translateY(0)'">
                        🗺️ Get Directions
                    </a>
                </div>
                <div style="position:relative;min-height:320px;overflow:hidden;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3571.234567890123!2d94.5833!3d27.4833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3742b0000000000%3A0x0!2sDhemaji%2C%20Assam%20787057!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin"
                        width="100%" height="100%"
                        style="border:0;display:block;position:absolute;top:0;left:0;width:100%;height:100%;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        title="MindShiksha Office Location - Dhemaji, Assam"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

@include('website.layouts.footer')

<style>
    .field-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: block;
        min-height: 16px;
    }

    .field input.is-invalid,
    .field select.is-invalid,
    .field textarea.is-invalid {
        border-color: #ef4444 !important;
    }

    #submitBtn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('demoForm');
        const formWrapper = document.getElementById('formWrapper');
        const successState = document.getElementById('successState');
        const successText = document.getElementById('successText');
        const backToRequestBtn = document.getElementById('backToRequestBtn');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const errorMsg = document.getElementById('errorMsg');
        const errorText = document.getElementById('errorText');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            clearErrors();
            errorMsg.style.display = 'none';

            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline';

            const formData = new FormData(form);

            fetch("{{ route('demo-request.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(async (response) => {
                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Show success state, hide form
                        successText.textContent = data.message;
                        formWrapper.style.display = 'none';
                        successState.style.display = 'block';
                        form.reset();
                        successState.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                    } else if (response.status === 422) {
                        showValidationErrors(data.errors);

                    } else {
                        errorText.textContent = data.message ||
                            'Something went wrong. Please try again.';
                        errorMsg.style.display = 'block';
                    }
                })
                .catch((err) => {
                    console.error(err);
                    errorText.textContent =
                        'Network error. Please check your connection and try again.';
                    errorMsg.style.display = 'block';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoader.style.display = 'none';
                });
        });

        // Back to Request button — reset UI to show the form again
        backToRequestBtn.addEventListener('click', function() {
            successState.style.display = 'none';
            formWrapper.style.display = 'block';
            errorMsg.style.display = 'none';
            clearErrors();
            formWrapper.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        function showValidationErrors(errors) {
            for (const field in errors) {
                const input = document.getElementById(field);
                const errorSpan = document.querySelector(`[data-error="${field}"]`);

                if (input) input.classList.add('is-invalid');
                if (errorSpan) errorSpan.textContent = errors[field][0];
            }

            const firstErrorField = Object.keys(errors)[0];
            const firstInput = document.getElementById(firstErrorField);
            if (firstInput) {
                firstInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInput.focus();
            }
        }

        function clearErrors() {
            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorSpan = document.querySelector(`[data-error="${this.name}"]`);
                if (errorSpan) errorSpan.textContent = '';
            });
        });

    });
</script>
