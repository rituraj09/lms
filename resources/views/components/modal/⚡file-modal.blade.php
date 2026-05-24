<?php

use App\Models\Billing\Invoice;
use App\Models\Billing\InvoicePayment;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $file = '';

};
?>

<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="fileModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Preview
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body p-0">

                    @if($file)

                        <iframe
                            src="{{ $file }}"
                            width="100%"
                            style="border:none;height: 80vh"
                        ></iframe>

                    @endif

                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('open-file-modal', () => {

                let modal = new bootstrap.Modal(
                    document.getElementById('fileModal')
                );

                modal.show();
            });

        });
    </script>
</div>
