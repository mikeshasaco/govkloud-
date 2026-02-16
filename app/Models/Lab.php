<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'estimated_minutes',
        'ttl_minutes',
        'workbench_image',
        'validator_image',
        'lab_config_json',
        'is_published',
    ];

    protected $casts = [
        'lab_config_json' => 'array',
        'is_published' => 'boolean',
        'estimated_minutes' => 'integer',
        'ttl_minutes' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(LabStep::class)->orderBy('order_index');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LabSession::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Get resource limits from lab_config_json or defaults
     */
    public function getResourceLimits(): array
    {
        $config = $this->lab_config_json ?? [];

        return [
            'cpu' => $config['resources']['cpu'] ?? config('govkloud.resources.default_cpu_limit'),
            'memory' => $config['resources']['memory'] ?? config('govkloud.resources.default_memory_limit'),
            'storage' => $config['resources']['storage'] ?? config('govkloud.resources.default_storage_limit'),
        ];
    }
}
