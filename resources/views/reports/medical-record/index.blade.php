@extends('report')

@section('body')

@foreach($admissions as $index => $admission)
    @if($index > 0)
        <p style="page-break-before: always">
    @endif

    @include('reports.medical-record.cage-card')

    @if(in_array('homecare', $shareOptions) && $locationId = request('locationId'))
        @include('reports.medical-record.homecare')
    @endif

    @if(in_array('rescuer', $shareOptions))
        @include('reports.medical-record.rescuer')
    @endif

    @include('reports.medical-record.intake')

    @if(in_array('location_history', $shareOptions))
        @include('reports.medical-record.location-history')
    @endif

    @if(in_array('intake_exams', $shareOptions))
        @include('reports.medical-record.intake-exam')
    @endif

    @include('reports.medical-record.diagnosis')
    @include('reports.medical-record.treatment-log')

    {{-- @if(in_array('rechecks', $shareOptions) || in_array('homecare', $shareOptions))
        @include('reports.medical-record.rechecks')
    @endif --}}

    @include('reports.medical-record.outcome')

    @if(in_array('necropsy', $shareOptions))
        @include('reports.medical-record.necropsy')
    @endif

    @if(in_array('banding_morphometrics', $shareOptions))
        @include('reports.medical-record.banding-morphometrics')
    @endif

    {{-- @foreach($shareOptions as $option)
        {!! \Illuminate\Support\Arr::first(event("report.admission.$option", [$admission])) !!}
    @endforeach --}}
@endforeach

@stop
