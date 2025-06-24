<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
    ];

    // State machine untuk status
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    // Relasi
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    // Cek apakah bisa diedit
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function approver()
    {
        return $this->approvalLogs()->latest()->first()?->user;
    }

    public function approvalStatus()
    {
        return $this->approvalLogs()->latest()->first()?->action;
    }
}