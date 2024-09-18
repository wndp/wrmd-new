<table class="table borderless" style="margin-top: 0; margin-bottom: 0;">
    <caption>Treatment Log</caption>
    @php $runningCharacterCount = 0 @endphp
    @php $allowedLines = $charactersPerLine * $linesPerPage @endphp
    @foreach($treatmentLogs as $record)
        <tr>
            <td>
                <b>{{ $record->logged_at_for_humans }}</b>
                {{ $record->model->fullWeight }}
                @php $currentCharacterCount = mb_strwidth(strip_tags("$record->logged_at_for_humans $record->body")) @endphp
                @if ($runningCharacterCount + $currentCharacterCount > $allowedLines)
                    {!! \Illuminate\Support\Str::limit($record->body, $allowedLines - $runningCharacterCount, "...<br><br><b>Read Electronic Record For More</b>") !!}
                    @break
                @else
                    @php $runningCharacterCount += $currentCharacterCount + $charactersPerLine @endphp
                    {!! $record->body !!}
                @endif
                @if($record->model->user)
                    ({{ str_initials($record->model->user->name) }})
                @endif
            </td>
        </tr>
    @endforeach
</table>
