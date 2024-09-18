@extends('report')

@section('body')

<h1>{{ $title }}</h1>

@if(isset($admission))
    <dl class="dl-inline" style="margin-top: 20px">
        <dt>Case #</dt> <dd>{{ $admission->caseNumber }}</dd>
        <dt>Species</dt> <dd>{{ $admission->patient->common_name }}</dd>
        <dt>Band</dt> <dd>{{ $admission->band }}</dd>
        <dt>Name</dt> <dd>{{ $admission->name }}</dd>
    </dl>
@endif

<table class="table">
    <tr>
        <th class="text-field" style="width: 180px">Date</th>
        <th class="text-field" style="width: 100px">Weight</th>
        <th class="text-field" style="width: 100px">Temp</th>
        <th class="text-field" style="width: auto">Comments</th>
        <th class="text-field" style="width: 70px">Initials</th>
    </tr>
    @foreach(range(1, $lines) as $index)
        <tr>
            <td class="text-field">&nbsp;</td>
            <td class="text-field">&nbsp;</td>
            <td class="text-field">&nbsp;</td>
            <td class="text-field">&nbsp;</td>
            <td class="text-field">&nbsp;</td>
        </tr>
    @endforeach
</table>

<div style="position: absolute; width: 1000px; top: 130px;">
    <table class="table borderless" style="background-color: transparent">
        @foreach($treatmentLogs as $record)
            <tr>
                <td style="width: 180px">{{ $record->logged_at_for_humans }}</td>
                <td style="width: 100px">{{ $record->model->fullWeight }}</td>
                <td style="width: 100px"></td>
                <td style="width: auto; padding: 0 8px; line-height: 47px;">
                    @if($record->model instanceof \App\Domain\Patients\TreatmentLog)
                        {{ $record->model->comments }}
                    @else
                        {!! $record->body !!}
                    @endif
                </td>
                <td style="width: 70px">
                    @if($record->model->user)
                        {{ str_initials($record->model->user->name) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>

@stop
