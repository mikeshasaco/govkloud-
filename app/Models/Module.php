<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Module $module) {
            if (empty($module->order_index)) {
                $module->order_index = static::max('order_index') + 1;
            }
        });
    }

    protected $fillable = [
        'slug',
        'title',
        'description',
        'category',
        'level',
        'banner_image',
        'resource_files',
        'order_index',
        'is_published',
        'requires_subscription',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order_index' => 'integer',
        'resource_files' => 'array',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index');
    }

    public function labs(): HasMany
    {
        return $this->hasMany(Lab::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    /**
     * Get download URLs for all resource files
     */
    public function getResourceFileUrls(): array
    {
        if (empty($this->resource_files)) {
            return [];
        }

        return collect($this->resource_files)->map(function ($path) {
            return [
                'name' => basename($path),
                'url' => \Storage::disk('azure')->url($path),
            ];
        })->toArray();
    }
}
