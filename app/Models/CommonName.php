<?php

namespace App\Models;

use App\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class CommonName extends Model
{
    use HasFactory;
    use ReadOnlyModel;
    use Searchable;

    protected $connection = 'wildalert';

    public function taxon(): HasOne
    {
        return $this->hasOne(Taxon::class);
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'wildalert_common_names';
    }
}
