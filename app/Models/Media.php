<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;

class Media extends BaseMedia
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;

    public $incrementing = false;

    /**
     * @see https://github.com/spatie/laravel-medialibrary/issues/1112#issuecomment-531477078
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // static::creating(function (Model $model) {
        //     if (is_null($model->getOriginal('id'))) {
        //         $model->id = Str::uuid7()->toString();
        //     }
        // });

        static::retrieved(function ($model) {
            static::setObtainedAtDateAttribute($model);
            static::setObtainedAtForHumansAttribute($model);
        });

        static::created(function ($model) {
            static::setObtainedAtDateAttribute($model);
            static::setObtainedAtForHumansAttribute($model);
        });
    }

    protected $appends = [
        'original_url',
        'medium_url',
        'preview_url',
        'human_readable_size',
    ];

    protected $hidden = [
        'collection_name',
        'conversions_disk',
        'disk',
        'generated_conversions',
        'manipulations',
        'model_id',
        'model_type',
        'responsive_images',
        'updated_at',
        'uuid',
    ];

    protected function previewUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->hasGeneratedConversion('preview') ? $this->getUrl('preview') : $this->getUrl(),
        );
    }

    protected function mediumUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->hasGeneratedConversion('medium') ? $this->getUrl('medium') : $this->getUrl(),
        );
    }

    /** TODO: write tests */
    public static function setObtainedAtDateAttribute($model)
    {
        if (! $model->hasCustomProperty('obtained_at')) {
            return null;
        }

        $date = Carbon::parse(
            $model->custom_properties['obtained_at']
        );

        $model->setCustomProperty('obtained_at_date', $date->format('Y-m-d'));
    }

    /** TODO: write tests */
    public static function setObtainedAtForHumansAttribute($model)
    {
        if (! $model->hasCustomProperty('obtained_at')) {
            return null;
        }

        $date = Carbon::parse(
            $model->custom_properties['obtained_at']
        );

        $model->setCustomProperty('obtained_at_for_humans', $date->format(config('wrmd.date_format')));
    }

    public function getUrl(string $conversionName = ''): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);

        $url = $urlGenerator->getUrl();

        /*
         * Laravel requires that minio on a local environment needs to have the bucket name
         * as part of the url for the AWS_URL env variable. Without the bucket name all preview urls
         * generated for uploaded files will fail since the bucket will not be in the url.
         *
         * https://laravel.com/docs/10.x/filesystem#amazon-s3-compatible-filesystems
         *
         * However Vapor.store() in the frontend automatically adds the bucket name to
         * the url of the file being uploaded. If the bucket name is specified in the AWS_URL
         * env variable then uploads fail.
         *
         * The compromise is to have AWS_URL set to the base minio url without the bucket name
         * and add this hack to automatically apply the bucket name to generated preview urls
         * only on the local development environment.
         *
         * This ensures that Vapor file uploads for both local and production will function
         * correctly without causing issues with each other.
         */

        if (App::environment('local')) {
            $url = str_replace('9000/', '9000/'.Config::get('filesystems.disks.s3.bucket').'/', $url);
        }

        return $url;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
