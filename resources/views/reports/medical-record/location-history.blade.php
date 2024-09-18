<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">Location History</h3>

@if($admission->patient->locations->empty())
    <p class="print-text">No location history recorded.</p>
@else
    <div class="grid grid-cols-12 gap-2">
        <strong class="col-span-3">Date Moved</strong>
        <strong class="col-span-2">Holding At</strong>
        <strong class="col-span-7">Location</strong>

        @foreach($admission->patient->locations as $location)
            <div class="print-text col-md-3 whitespace-nowrap">
                {{ $location->moved_in_at_formatted }}
            </div>
            <div class="print-text col-md-2 whitespace-nowrap">
                {{ $location->where_holding }}
            </div>
            <div class="print-text col-md-7">
                {!! $location->location !!}
            </div>
        @endforeach
    </div>
@endif
