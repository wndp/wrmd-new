@extends('report')

@section('body')

<style>
    body {
        font-size: 12px
    }
    .table>tbody>tr>td {
        border-top-color: #474546
    }
    .text-field.text-field {
        height: auto;
    }
    .table .text-field {
        height: 18px;
        line-height: 16px;
    }
</style>

<div class="row">
    <div class="col-md-6">
        @include('reports.paperforms.large-cagecard-details')
    </div>
    <div class="col-md-6">
        @include('reports.paperforms.large-cagecard-log')
    </div>
</div>

@stop
