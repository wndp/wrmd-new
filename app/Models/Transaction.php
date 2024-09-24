<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Laravel\Paddle\Transaction as CashierTransaction;

class Transaction extends CashierTransaction
{
    use HasVersion7Uuids;
}
