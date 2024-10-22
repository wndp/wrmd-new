<?php

namespace App\Models;

use App\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CommonName extends Model
{
    use HasFactory;
    use ReadOnlyModel;

    protected $connection = 'wildalert';

    public function taxon(): HasOne
    {
        return $this->hasOne(Taxon::class);
    }
}
