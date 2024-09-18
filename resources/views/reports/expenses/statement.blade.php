<table class="table table-striped">
    <thead>
        <tr>
            <th style="width:200px">Date</th>
            <th style="width:auto">Category / Memo</th>
            <th style="width:100px">Debit</th>
            <th style="width:100px">Credit</th>
        </tr>
    </thead>
    @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->transacted_at_formatted }}</td>
            <td>
                {{ $transaction->category->parent ? $transaction->category->parent->name.'::' : '' }}{{ $transaction->category->name }}
                <br><i class="text-foot-note">{{ $transaction->memo }}</i>
            </td>
            <td>{{ $transaction->debit ? $transaction->debit_formatted : null }}</td>
            <td>{{ $transaction->credit ? $transaction->credit_formatted : null }}</td>
        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th></th>
            <th style="text-align: right">Total</th>
            <th>{{ $transactions->totalDebits(true) }}</th>
            <th>{{ $transactions->totalCredits(true) }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: right">Cost of Care</th>
            <th colspan="2">{{ $transactions->costOfCare(true) }}</th>
        </tr>
    </tfoot>
</table>
