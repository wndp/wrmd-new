@extends('report')

@section('body')

<h1>{{ $title }}</h1>
<h6>Between: {{ $dateFrom }} to {{ $dateTo }}</h6>

<p><strong>Address</strong> {{ $account->mailing_address }}</p>
<p><strong>Date</strong> {{ now(settings('timezone'))->format(settings('date_format').' g:i a') }}</p>

<table class="table">
    <thead>
        <tr>
            <th style="width:60%;">Category</th>
            <th style="width:13.33%;">Amounts</th>
            <th style="width:auto;"></th>
        </tr>
    </thead>
    @foreach($parentCategories as $childCategory => $transactions)
        <tr>
            <td>{{ $childCategory }}</td>
            <td>{{ $transactions->sumOfTransactions(true) }}</td>
            <td></td>
        </tr>
        @foreach($transactions as $category => $childTransactions)
            @if($childCategory !== $category)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $category }}</td>
                    <td></td>
                    <td>{{ $childTransactions->costOfCare(true) }}</td>
                </tr>
            @endif
        @endforeach
    @endforeach
    <tr>
        <th style="text-align: right">Total</th>
        <th>{{ number_format($parentCategories->sum->sumOfTransactions() / 100, 2) }}</th>
        <th></th>
    </tr>
</table>

@stop
