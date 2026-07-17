<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StudentReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'student_details' => $this->when($this->relationLoaded('details'), function() {
                return [
                    'first_name' => $this->details->first_name,
                    'last_name' => $this->details->last_name,
                    'full_name' => $this->details->first_name . ' ' . $this->details->last_name,
                    'student_id' => $this->details->student_id,
                    'date_of_birth' => $this->details->date_of_birth,
                    'physical_age' => $this->calculatePhysicalAge(),
                ];
            }),
            'organisation' => $this->when($this->relationLoaded('organisation'), function() {
                return [
                    'id' => $this->organisation->id,
                    'name' => $this->organisation->name,
                ];
            }),
            'current_promotions' => $this->when(isset($this->current_promotions), function() {
                return [
                    'iq' => $this->current_promotions['iq'] ?? null,
                    'eq' => $this->current_promotions['eq'] ?? null,
                    'lq' => $this->current_promotions['lq'] ?? null,
                ];
            }),
        ];
    }

    private function calculatePhysicalAge()
    {
        if (!$this->details || !$this->details->date_of_birth) {
            return null;
        }

        $dob = Carbon::parse($this->details->date_of_birth);
        $now = Carbon::now();

        $years = $dob->diffInYears($now);

        if ($years > 0) {
            return $years . ' ' . ($years == 1 ? 'year' : 'years');
        }

        $months = $dob->diffInMonths($now);
        return $months . ' ' . ($months == 1 ? 'month' : 'months');
    }
}
