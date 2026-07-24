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
        <div class="card">{{-- ✅ removed "reveal" to prevent re-hide on scroll --}}

            <!-- Success State -->
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

            <!-- Form Wrapper -->
            <div id="formWrapper">

                <!-- Error Summary Box — INSIDE formWrapper -->
                <div id="errorMsg"
                    style="display:none;background:rgba(239,68,68,.10);
                     border:1px solid rgba(239,68,68,.25);color:#ef4444;padding:16px 18px;
                     border-radius:12px;margin-bottom:20px;font-size:14px;">
                    <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:8px;">
                        <span style="font-size:16px;">⚠️</span>
                        <span id="errorTitle">Please fix the following errors:</span>
                    </div>
                    <ul id="errorList" style="margin:0;padding-left:20px;display:grid;gap:4px;"></ul>
                </div>

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
                        style="margin-top:18px;width:100%;justify-content:center;position:relative;overflow:hidden;">
                        <span id="btnText">Submit Request →</span>
                        <span id="btnLoader" style="display:none;align-items:center;gap:10px;">
                            <span class="spinner"></span>
                            <span>Sending...</span>
                        </span>
                    </button>
                </form>

            </div>
            <!-- /Form Wrapper -->

        </div>

        <!-- VALUE SIDE -->
        <div class="reveal">
            <h2>Why Schedule a Demo?</h2>
            <ul style="list-style:none;display:grid;gap:16px;margin-top:20px">
                <li class="card" style="display:flex;gap:12px;align-items:center">
                    <span class="icon" style="margin:0">🎯</span>
                    <div><strong>Tailored Walkthrough</strong>
                        <p class="text-muted">See modules relevant to your learners.</p>
                    </div>
                </li>
                <li class="card" style="display:flex;gap:12px;align-items:center">
                    <span class="icon" style="margin:0">📊</span>
                    <div><strong>Live Analytics</strong>
                        <p class="text-muted">Explore real dashboards & reports.</p>
                    </div>
                </li>
                <li class="card" style="display:flex;gap:12px;align-items:center">
                    <span class="icon" style="margin:0">🔒</span>
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
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;
                                          letter-spacing:.8px;text-transform:uppercase;">
                                    Registered Office</p>
                                <p style="font-size:13px;color:var(--text);margin:0;line-height:1.7;">
                                    Ward No. 4, Bhehpara Baruah Chuk,<br>Dhemaji, Assam – 787057, India</p>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;align-items:flex-start;">
                            <span style="font-size:16px;min-width:24px;margin-top:2px;">🔢</span>
                            <div>
                                <p
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;
                                          letter-spacing:.8px;text-transform:uppercase;">
                                    CIN</p>
                                <p
                                    style="font-size:13px;color:var(--text);margin:0;font-family:monospace;
                                          letter-spacing:.6px;">
                                    U62011AS2026PTC030649</p>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;align-items:flex-start;">
                            <span style="font-size:16px;min-width:24px;margin-top:2px;">📅</span>
                            <div>
                                <p
                                    style="font-size:10px;font-weight:700;color:#94a3b8;margin:0 0 4px;
                                          letter-spacing:.8px;text-transform:uppercase;">
                                    Date of Incorporation</p>
                                <p style="font-size:13px;color:var(--text);margin:0;line-height:1.6;">
                                    18th June, 2026</p>
                                <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">
                                    Under The Companies Act, 2013</p>
                            </div>
                        </div>
                    </div>
                    <hr style="border:none;border-top:1px solid rgba(148,163,184,.2);margin:20px 0;">
                    <a href="https://www.google.com/maps/search/Dhemaji,+Assam+787057,+India" target="_blank"
                        rel="noopener noreferrer"
                        style="display:inline-flex;align-items:center;gap:8px;background:rgba(6,182,212,.12);
                               color:#06B6D4;padding:10px 18px;border-radius:10px;font-size:13px;
                               font-weight:600;text-decoration:none;width:fit-content;transition:background .2s,transform .2s;"
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
    /* ── Error box animation ── */
    #errorMsg {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Invalid field highlight ── */
    .field input.is-invalid,
    .field select.is-invalid,
    .field textarea.is-invalid {
        border-color: #ef4444 !important;
        background: rgba(239, 68, 68, 0.04) !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
    }

    /* ── Spinner ── */
    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: inline-block;
        flex-shrink: 0;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    #submitBtn.is-loading {
        pointer-events: none;
        opacity: 0.85;
    }

    #submitBtn.is-loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.08) 50%,
                transparent 100%);
        background-size: 200% 100%;
        animation: shimmer 1.2s infinite;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }

        100% {
            background-position: 200% 0;
        }
    }

    #submitBtn:disabled {
        cursor: not-allowed;
    }
</style>

<script>
    (function() {
        // 🛡️ Prevent duplicate binding if script runs more than once
        if (window.__demoFormInitialized) return;
        window.__demoFormInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('demoForm');
            if (!form) return;

            // 🛡️ Extra safety: remove any existing listeners by cloning the node
            const freshForm = form.cloneNode(true);
            form.parentNode.replaceChild(freshForm, form);

            const formEl = freshForm; // use this instead of `form` from now on
            const formWrapper = document.getElementById('formWrapper');
            const successState = document.getElementById('successState');
            const successText = document.getElementById('successText');
            const backToRequestBtn = document.getElementById('backToRequestBtn');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const errorMsg = document.getElementById('errorMsg');
            const errorTitle = document.getElementById('errorTitle');
            const errorList = document.getElementById('errorList');

            const fieldLabels = {
                full_name: 'Full Name',
                institution: 'Institution',
                role: 'Role',
                email: 'Email',
                phone: 'Phone',
                preferred_slot: 'Preferred Demo Slot',
                message: 'Message',
            };

            function setLoading(state) {
                if (state) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('is-loading');
                    btnText.style.display = 'none';
                    btnLoader.style.display = 'flex';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('is-loading');
                    btnText.style.display = 'inline';
                    btnLoader.style.display = 'none';
                }
            }

            function showValidationErrors(errors) {
                errorList.innerHTML = '';
                errorTitle.textContent = 'Please fix the following errors:';

                for (const field in errors) {
                    const input = document.getElementById(field);
                    if (input) input.classList.add('is-invalid');

                    const li = document.createElement('li');
                    const label = fieldLabels[field] || field;
                    li.textContent = `${label}: ${errors[field][0]}`;
                    errorList.appendChild(li);
                }
                errorMsg.style.display = 'block';
            }

            function showGenericError(message) {
                errorList.innerHTML = '';
                errorTitle.textContent = message || 'Something went wrong. Please try again.';
                errorMsg.style.display = 'block';
            }

            function clearErrors() {
                errorMsg.style.display = 'none';
                errorList.innerHTML = '';
                document.querySelectorAll('.is-invalid')
                    .forEach(el => el.classList.remove('is-invalid'));
            }

            formEl.addEventListener('submit', function(e) {
                e.preventDefault();
                clearErrors();
                setLoading(true);

                fetch("{{ route('demo-request.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formEl.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: new FormData(formEl),
                    })
                    .then(async (response) => {
                        const data = await response.json();

                        if (response.ok && data.success) {
                            successText.textContent = data.message;
                            formWrapper.style.display = 'none';
                            successState.style.display = 'block';
                            formEl.reset();
                        } else if (response.status === 422) {
                            showValidationErrors(data.errors);
                        } else {
                            showGenericError(data.message);
                        }
                    })
                    .catch(() => {
                        showGenericError(
                            'Network error. Please check your connection and try again.');
                    })
                    .finally(() => {
                        setLoading(false);
                    });
            });

            formEl.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    if (!formEl.querySelector('.is-invalid')) {
                        errorMsg.style.display = 'none';
                    }
                });
            });

            backToRequestBtn.addEventListener('click', function() {
                successState.style.display = 'none';
                formWrapper.style.display = 'block';
                clearErrors();
            });

        });
    })();
</script>
