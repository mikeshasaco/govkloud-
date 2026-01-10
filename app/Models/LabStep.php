<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabStep extends Model
{
    protected $fillable = [
        'lab_id',
        'order_index',
        'type',
        'payload_json',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'order_index' => 'integer',
    ];

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    /**
     * Check if this is an instruction step
     */
    public function isInstruction(): bool
    {
        return $this->type === 'instruction';
    }

    /**
     * Check if this is a quiz step
     */
    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    /**
     * Check if this is a task step
     */
    public function isTask(): bool
    {
        return $this->type === 'task';
    }

    /**
     * Check if this is a validate step
     */
    public function isValidate(): bool
    {
        return $this->type === 'validate';
    }
}
