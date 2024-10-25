<?php

namespace App\Models;

use App\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConservationStatus extends Model
{
    use HasFactory;
    use ReadOnlyModel;

    protected $connection = 'wildalert';
}
