<?php

namespace App\Jobs;

use Geocodio\Exceptions\GeocodioException;
use Geocodio\GeocodioFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

class GeocodeAddress implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Model $model, public string $coordinatesAttribute)
    {
        $this->model = $model;
        $this->coordinatesAttribute = $coordinatesAttribute;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = GeocodioFacade::geocode($this->model->address_to_geocode, [], 1);
        } catch (GeocodioException $e) {
            return;
        }

        if (count($response?->results) === 1) {
            $this->model->{$this->coordinatesAttribute} = new GeographyPoint(
                data_get($response, 'results.0.location.lat'),
                data_get($response, 'results.0.location.lng')
            );
            $this->model->save();
        }
    }
}
