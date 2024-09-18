<table class="table">
    <caption>Expenses</caption>
    <tbody>
        <thead>
            <tr>
                <th style="width:130px">Case #</th>
                <th style="width:250px">Common Name</th>
                <th style="width:150px">Name / Reference #</th>
                <th style="width:100px">Charge</th>
                <th style="width:170px">Category</th>
                <th style="width:auto">Memo</th>
            </tr>
        </thead>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->patient->admissions($accountId)->case_number }}</td>
                <td>{{ $transaction->patient->common_name }}</td>
                <td>{{ $transaction->patient->name }} {{ $transaction->patient->reference_number }}</td>
                <td>{{ $transaction->charge_formatted }}</td>
                <td>{{ $transaction->category->name }}</td>
                <td>{{ $transaction->memo }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
