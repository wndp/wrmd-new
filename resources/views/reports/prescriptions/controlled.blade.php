@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">{{ __('Prescriptions Starting On or After') }}: {{ $dateFrom }} {{ __('to') }} {{ $dateTo }}</h2>

<table class="print-table mt-10">
	<thead>
		<tr>
			<th style="width:100px;">{{ __('Case') }}</th>
			<th style="width:auto;">{{ __('Species') }}</th>
			<th style="width:150px;">{{ __('Date') }}</th>
			<th style="width:70px;">{{ __('Volume') }}</th>
			<th style="width:300px">{{ __('Drug') }}</th>
		</tr>
	</thead>
	@foreach($controlled as $prescription)
		<tr>
			<td>{{ $prescription->patient->admissions($account->id)->caseNumber }}</td>
			<td>{{ $prescription->patient->common_name }}</td>
			<td>{{ $prescription->administered_at->format(settings('date_format')) }}</td>
			<td>{{ $prescription->dose.$prescription->dose_unit }}</td>
			<td>
				{{ $prescription->drug }}
				@if($prescription->total_administrations === INF)
					<br>(<em>{{ __('given infinite number of times') }}</em>)
				@endif
			</td>
		</tr>
	@endforeach
</table>

@stop
