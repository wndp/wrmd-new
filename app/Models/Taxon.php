<?php

namespace App\Models;

use App\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxon extends Model
{
    use HasFactory;
    use ReadOnlyModel;

    protected $connection = 'wildalert';

    protected $appends = [
        'binomen',
        'bow_url',
        'iucn_url',
        'inaturalist_url'
    ];

    public function metas(): HasMany
    {
        return $this->hasMany(TaxonMeta::class, 'taxon_id');
    }

    public function conservationStatuses(): HasMany
    {
        return $this->hasMany(ConservationStatus::class, 'taxon_id');
    }

    public function binomen(): Attribute
    {
        return Attribute::get(
            fn () => is_null($this->genus) ? 'Undescribed species' : trim($this->genus.' '.mb_strtolower($this->species))
        );
    }

    public function bowUrl(): Attribute
    {
        return Attribute::get(
            fn () => is_null($this->bow_code) ? '' : "https://birdsoftheworld.org/bow/species/{$this->bow_code}/cur/introduction"
        );
    }

    public function iucnUrl(): Attribute
    {
        return Attribute::get(
            fn () => is_null($this->iucn_id) ? '' : "https://apiv3.iucnredlist.org/api/v3/taxonredirect/{$this->iucn_id}"
        );
    }

    public function inaturalistUrl(): Attribute
    {
        return Attribute::get(
            fn () => is_null($this->inaturalist_taxon_id) ? '' : "https://www.inaturalist.org/taxa/{$this->inaturalist_taxon_id}"
        );
    }
}
