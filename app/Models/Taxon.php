<?php

namespace App\Models;

use App\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxon extends Model
{
    use HasFactory;
    use ReadOnlyModel;

    protected $connection = 'wildalert';

    public function metas(): HasMany
    {
        return $this->hasMany(TaxonMeta::class, 'taxon_id');
    }
}
