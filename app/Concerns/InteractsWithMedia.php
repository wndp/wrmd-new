<?php

namespace App\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia as SpatieInteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    use SpatieInteractsWithMedia;

    /*
     * Register the media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 368, 232)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 1000, 800)
            ->sharpen(10);
    }
}
