<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Laravel\Paddle\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    use HasVersion7Uuids;
}
