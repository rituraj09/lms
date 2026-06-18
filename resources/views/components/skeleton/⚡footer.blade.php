<?php
//views/skeleton/footer.blade.php
use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <footer class="content-footer footer bg-footer-theme border-top">
        <div class="container-xxl">
            <div
                class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column gap-2">

                {{-- Left: Copyright --}}
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="ri ri-copyright-line icon-14px"></i>
                    <span>
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        &nbsp;&mdash; Made with ❤️ by
                        <a href="#" target="_blank" class="footer-link fw-semibold text-primary">
                            {{ config('app.name') }}
                        </a>
                    </span>
                    <span class="badge bg-label-success ms-1" style="font-size: 0.62rem;">
                        v{{ config('app.version', '1.0.0') }}
                    </span>
                </div>

                {{-- Right: Links --}}
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="footer-link small text-muted d-flex align-items-center gap-1"
                        target="_blank">
                        <i class="ri ri-settings-3-line icon-14px"></i>
                        <span class="d-none d-sm-inline">Settings</span>
                    </a>
                    <span class="text-muted opacity-25">|</span>
                    <a href="#" class="footer-link small text-muted d-flex align-items-center gap-1"
                        target="_blank">
                        <i class="ri ri-file-text-line icon-14px"></i>
                        <span class="d-none d-sm-inline">Documentation</span>
                    </a>
                    <span class="text-muted opacity-25">|</span>
                    <a href="#" class="footer-link small text-muted d-flex align-items-center gap-1"
                        target="_blank">
                        <i class="ri ri-lifebuoy-line icon-14px"></i>
                        <span class="d-none d-sm-inline">Support</span>
                    </a>
                    <span class="text-muted opacity-25 d-none d-lg-block">|</span>
                    <a href="#" class="footer-link small text-muted d-none d-lg-flex align-items-center gap-1"
                        target="_blank">
                        <i class="ri ri-shield-check-line icon-14px"></i>
                        Privacy Policy
                    </a>
                </div>

            </div>
        </div>
    </footer>
</div>
