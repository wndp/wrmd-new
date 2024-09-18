<?php

namespace App\Http\Controllers\People\Combine;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CombineSearchController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('People/Combine/Search');
    }
}
