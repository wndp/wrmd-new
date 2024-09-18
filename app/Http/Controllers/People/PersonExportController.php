<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Str;

class PersonExportController extends Controller
{
    public function __invoke(ReportRequest $request)
    {
        $data = $request->validate([
            'group' => 'required',
            'date_from' => 'required|date|before_or_equal:date_to',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $request->filters = base64_encode(json_encode([
            [
                'class' => DateFrom::class,
                'value' => $data['date_from'],
            ],
            [
                'class' => DateTo::class,
                'value' => $data['date_to'],
            ],
        ]));

        $request->report(Str::kebab($data['group']))
            ->fromDevice($request->cookie('device-uuid'))
            ->export();

        return back();
    }
}
