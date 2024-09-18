<?php

namespace App\Http\Controllers;

use App\Concerns\LoadsAdmissionAndSharesPagination;

abstract class Controller
{
    use LoadsAdmissionAndSharesPagination;
}
