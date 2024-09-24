<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Laravel\Paddle\Customer as CashierCustomer;

class Customer extends CashierCustomer
{
    use HasVersion7Uuids;
}
