<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Media;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaOrderController extends Controller
{
    /**
     * Store the order of the media objects
     *
     * @return void
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'media' => 'required|array',
            'media.*' => 'numeric',
        ]);

        Media::setNewOrder($request->media);
    }
}
