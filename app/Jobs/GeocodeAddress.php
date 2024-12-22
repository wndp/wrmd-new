<?php

namespace App\Jobs;

use App\ValueObjects\GeocodeComponents;
use App\ValueObjects\SingleStorePoint;
use CommerceGuys\Addressing\Address;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

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
        //$freshModel = $this->model->fresh();

        $result = $this->geocode();

        $this->model->timestamps = false;
        $this->model->{$this->coordinatesAttribute} = new SingleStorePoint($result->latitude, $result->longitude);

        // if (property_exists($attributes, 'county_attribute')) {
        //     $this->model->{$attributes->county_attribute} = $result->county;
        // }

        $this->model->save();
    }

    public function geocode(): GeocodeComponents
    {
        $address = $this->getAddress();

        foreach (config('wrmd.geocoders') as $geocoder) {
            try {
                return $geocoder::run($address);
            } catch (GeocodingException $e) {
            }
        }
    }

    /**
     * Get the model's address to geocode.
     */
    protected function getAddress(): Address
    {
        return $this->model->{'get'.Str::studly($this->coordinatesAttribute).'Address'}();
    }
}
