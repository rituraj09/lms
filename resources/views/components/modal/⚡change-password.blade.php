<?php

use App\Traits\WithAdmin;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    use WithAdmin;

    public bool $show = false;

    public ?string $current_password = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    protected function rules(): array
    {
        return [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ];
    }

    protected array $messages = [
        'current_password.required' => 'Current password is required',
        'password.required' => 'New password is required',
        'password.min' => 'Password must be at least 6 characters',
        'password.confirmed' => 'Password confirmation does not match',
    ];

    #[On('show-change-password-modal')]
    public function showModal(): void
    {
        $this->resetFields();
        $this->resetValidation();

        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
    }

    protected function resetFields(): void
    {
        $this->reset([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    }

    public function submit(): void
    {
        $this->validate();

        if (!Hash::check($this->current_password, $this->admin->password)) {

            $this->addError(
                'current_password',
                'Current password is incorrect'
            );

            return;
        }

        $this->admin->update([
            'password' => bcrypt($this->password)
        ]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Password changed successfully!'
        );

        $this->closeModal();
    }
};
?>

<div>
    <div>
        @if($show)
            <div class="modal fade show d-block" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Change Password
                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    wire:click="closeModal"></button>
                        </div>

                        <form wire:submit.prevent="submit">

                            <div class="modal-body">

                                <div class="mb-5">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password"
                                               id="current_password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               wire:model="current_password">

                                        <label for="current_password">
                                            Current Password
                                        </label>
                                    </div>

                                    @error('current_password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-5">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password"
                                               id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               wire:model="password">

                                        <label for="password">
                                            New Password
                                        </label>
                                    </div>

                                    @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password"
                                               id="password_confirmation"
                                               class="form-control"
                                               wire:model="password_confirmation">

                                        <label for="password_confirmation">
                                            Confirm Password
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        wire:click="closeModal">
                                    Cancel
                                </button>

                                <button type="submit"
                                        class="btn btn-primary">
                                    Update Password
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <div class="modal-backdrop fade show"></div>
        @endif
    </div>
</div>

