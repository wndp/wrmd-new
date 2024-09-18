@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

@foreach($map as $caption => $collection)
    @if(! empty($collection))
        <table class="print-table mt-10">
            <caption>{{ Str::title(Str::plural($caption)) }}</caption>
            <thead>
                <tr>
                    <th style="width:250px">{{ __('Common Name') }}</th>
                    @foreach ($years as $year)
                        <th style="width:auto">{{ $year }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($collection as $commonName => $admissions)
                    <tr>
                        <th style="width:250px">{{ $commonName }}</th>
                        @foreach ($years as $year)
                            <td style="width:auto">
                                @if($admissions->offsetExists($year))
                                    {{ $admissions->get($year)->format('M d') }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach

@stop
