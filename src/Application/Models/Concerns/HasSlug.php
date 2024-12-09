<?php

namespace App\Application\Models\Concerns;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $model->slug = $model->generateSlug();
        });
        static::updating(function ($model) {
            $model->slug = $model->generateSlug();
        });
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function scopeWhereSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
