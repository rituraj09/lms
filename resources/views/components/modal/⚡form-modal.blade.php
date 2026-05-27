<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $form = '';
    public $title = 'Form';
    public $sub_title = 'Sub Title';

    #[On('viewOrganisation')]
    public function viewOrganisation($id)
    {
        $viewOrganisation = \App\Models\Master\Organisation::with('state')->findOrFail($id);
        $this->title = "Organisation Details";
        $this->sub_title = "View organisation details here.";
        $this->form = view('partials.organisation', compact('viewOrganisation'))->render();
        $this->dispatch('open-form-modal');
    }

};
?>

<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="formModal"
        tabindex="-1"
        aria-hidden="true"
    >
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
</div>


