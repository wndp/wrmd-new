@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

<h3 class="print-caption mt-20">Unfortunately we are unable to produce a preview of this report.</h3>
<p class="print-text mt-4">However, if you click the <strong class="font-bold">Print Report</strong> button above you will see the generated PDF.</p>

@stop
