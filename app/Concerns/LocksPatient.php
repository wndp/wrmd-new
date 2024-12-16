<?php

namespace App\Concerns;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait LocksPatient
{
    /**
     * Determine if a patients locked status is being updated.
     */
    public static function isUpdatingLockedStatus(Model $model): bool
    {
        if (! $model instanceof Patient) {
            return false;
        }

        return $model->syncChanges()->wasChanged('locked_at');
    }

    /**
     * Boot the trait for a model.
     */
    protected static function bootLocksPatient()
    {
        // If a locked patient exists do not allow creating.
        static::creating(function ($model) {
            if (static::hasLockedPatientRelationship($model)) {
                return false;
            }
        });

        // If a locked patient exists do not allow updating.
        static::updating(function ($model) {
            if (static::isUpdatingLockedStatus($model)) {
                return true;
            }

            if (static::isLockedPatient($model) || static::hasLockedPatientRelationship($model) || static::hasLockedCollectionRelationship($model)) {
                return false;
            }
        });

        // If a locked patient exists do not allow deleting.
        static::deleting(function ($model) {
            if (static::hasLockedPatientRelationship($model) || static::hasLockedCollectionRelationship($model)) {
                return false;
            }
        });
    }

    /**
     * Determine if the model is a patient and it is locked.
     */
    protected static function isLockedPatient(Model $model): bool
    {
        return $model instanceof Patient && ! is_null($model->locked_at);
    }

    /**
     * Determine if the model has a patient model relationship and it is locked.
     */
    protected static function hasLockedPatientRelationship(Model $model): bool
    {
        // The relationship name on $model might be patient or model;
        // used by morphed relations out of WRMD's control, ie: \Spatie\MediaLibrary\MediaCollections\Models\Media
        $relation = $model->patient ?: $model->model;

        return $relation instanceof Patient && ! is_null($relation->locked_at);
    }

    /**
     * Determine if the model has a patient collection relationship and any of the patients are locked.
     */
    protected static function hasLockedCollectionRelationship(Model $model): bool
    {
        return $model->patients instanceof Collection && $model->patients->filter(fn ($patient) => ! is_null($patient->locked_at))->isNotEmpty();
    }
}
