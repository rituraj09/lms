<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl">
            <div
                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                    &#169;
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    , by
                    <a href="#" target="_blank" class="footer-link fw-medium">{{ config('app.name') }}</a>
                </div>
                <div class="d-none d-lg-inline-block">
                    <a href="#" class="footer-link me-4" target="_blank"
                    >License</a
                    >

                    <a
                        href="#"
                        target="_blank"
                        class="footer-link me-4"
                    >Documentation</a
                    >

                    <a href="#" target="_blank" class="footer-link d-none d-sm-inline-block"
                    >Support</a
                    >
                </div>
            </div>
        </div>
    </footer>
</div>
