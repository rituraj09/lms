<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_organisation_id',
        'to_organisation_id',
        'transferred_by',
        'reason',
        'transferred_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fromOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'from_organisation_id');
    }

    public function toOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'to_organisation_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(Admin::class, 'transferred_by');
    }
}
