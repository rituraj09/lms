<?php

namespace App\Livewire\Admin\Demo;

use App\Models\DemoRequest;
use Livewire\Component;
use Livewire\Attributes\On;

class PendingBadge extends Component
{
    public string $mode = 'badge'; // 'dot' | 'badge'
    public int $count = 0;

    public function mount(string $mode = 'badge'): void
    {
        $this->mode = $mode;
        $this->loadCount();
    }

    public function loadCount(): void
    {
        $this->count = DemoRequest::where('status', 'pending')->count();
    }

    #[On('demo-request-updated')]
    public function refreshCount(): void
    {
        $this->loadCount();
    }

    public function render()
    {
        return view('livewire.admin.demo.pending-badge');
    }
}
