<?php

namespace App\Livewire\Admin\Reports;

use App\Models\User;
use App\Models\TestAttempt\UserPromotionDetail;
use Illuminate\Support\Facades\Crypt; // Make sure this is imported!
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.blank')]
class PromotionCertificate extends Component
{
    // Remove the model type-hints so Livewire doesn't attempt
    // automatic route model binding on the encrypted strings.
    public $student;
    public $history;

    public $promotion;
    public $attempt;
    public $assessment;
    public $percentage = 0;
    public $ageGroup = 'N/A';
    public $typeLabel = '—';
    public $certNo;
    public $issuedDate;

    public function mount(string $student, string $history)
    {
        // 1. Decrypt using decryptString (matching your Blade encryptString)
        $studentId = Crypt::decrypt($student);
        $historyId = Crypt::decrypt($history);


        // 2. Fetch models manually
        $this->student = User::findOrFail($studentId);

        $this->history = UserPromotionDetail::with([
            'promotionDetail.currentPromotion.ageGroup',
            'promotionDetail.nextPromotion',
            'testAttempt.assessment',
        ])->findOrFail($historyId);



        $this->promotion  = $this->history->promotionDetail?->currentPromotion;
        $this->attempt    = $this->history->testAttempt;
        $this->assessment = $this->attempt?->assessment;

        if ($this->attempt && $this->assessment?->total_marks > 0) {
            $this->percentage = round(
                ($this->attempt->total_score / $this->assessment->total_marks) * 100,
                1
            );
        }

        $this->ageGroup   = $this->promotion?->ageGroup?->name ?? 'N/A';
        $this->typeLabel  = strtoupper($this->assessment?->assessment_type_id ?? '—');
        $this->certNo     = $this->attempt?->ack_no ?? ('CERT-' . strtoupper(uniqid()));
        $this->issuedDate = $this->history->created_at?->format('d F Y') ?? now()->format('d F Y');
    }

    #[Title('Promotion Certificate')]
    public function render()
    {
        return view('livewire.admin.reports.promotion-certificate');
    }
}
