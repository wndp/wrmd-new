@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

<table class="print-table pt-20">
	<thead>
		<tr>
			<th style="width:75px;">Case</th>
			@foreach($list['headers'] as $header)
				<th>{{ $header['label'] }}</th>
			@endforeach
			<th style="width:140px;">Date Admitted</th>
		</tr>
	</thead>
	<tbody>
		@foreach($list['rows']['data'] as $patient)
		<tr>
			<td>{{ $patient['case_number'] }}</td>
			<td>{{ $patient['common_name'] }}</td>
			@foreach($list['headers'] as $header)
				<td>{{ $patient[$header['key']] }}</td>
			@endforeach
			<td>{{ $patient['admitted_at'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

@stop
