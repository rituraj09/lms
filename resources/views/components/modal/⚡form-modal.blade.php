<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $form = '';
    public $title = 'Form';
    public $sub_title = 'Sub Title';


};
?>

<div>
    <div class="modal fade" id="formModal" tabindex="-1" aria-modal="true" role="dialog"
         style="display: block; padding-left: 0px;">
        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-6">
                        <h4 class="role-title mb-2 pb-0">{{ $title  }}</h4>
                        <p>{{ $sub_title }}</p>
                    </div>
                    @if($form)
                        {!! $form !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-form-modal', () => {
            let modal = new bootstrap.Modal(
                document.getElementById('formModal')
            );
            modal.show();
        });
    });
</script>
@endscript
