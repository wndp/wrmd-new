<hr>
<h2>Future Rechecks</h2>
@php $rechecks = $admission->patient->getFutureRechecks($account); @endphp
@forelse($rechecks as $recheck)
    <div class="row">
        <label class="col-md-2" style="white-space: nowrap;">
            {{ $recheck->scheduled_at_formatted }}
        </label>
        <label class="col-md-2" style="white-space: nowrap;">
            {{ $recheck->assigned_to }}
        </label>
        <div class="col-md-8">
            {!! $recheck->description !!}
        </div>
    </div>
@empty
    <p>No future rechecks recorded.</p>
@endforelse
