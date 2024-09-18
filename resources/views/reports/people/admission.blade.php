
<table>
    <thead>
        <tr>
            @foreach($admissions[0] as $headers => $admission)
                <th>{{ $headers }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                @foreach($admission as $headers => $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
