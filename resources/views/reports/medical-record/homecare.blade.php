@if($location = $admission->patient->locations->where('id', $locationId)->first())
    <div class="border-t border-gray-500 mt-10"></div>
    <h3 class="print-caption my-4">Homecare</h3>

    <p class="print-text"><em class="italic">This animal remains under the jurisdiction of {{ $account->organization }}. Please report the status of the animal at the beginning of every month. Please return this record promptly upon disposition of the animal.</em></p>

    <div class="grid grid-cols-12 gap-2 mt-4">
        <div class="print-text col-span-6">
            <strong class="font-bold mr-2">Care Start Date</strong>
            {{ format_date($location->moved_in_at, $account->settingsStore()->get('date_format')) }}
        </div>
        <div class="print-text col-span-6">
            <strong class="font-bold mr-2">Caregiver</strong>
            {{ $location->location }}
        </div>
        <div class="print-text col-span-12 flex align-end">
            <strong class="font-bold mr-2">Comments</strong>
            <div class="inline border-b border-gray-400 flex-1">{{ $location->comments }}</div>
        </div>

        <?php $rechecks = $admission->patient->rechecks()->whereDate('recheck_start_at', '>=', now())->get() ?>
        @foreach($rechecks as $recheck)
            <div class="row">
                <strong class="font-bold col-span-2">Recheck Date</strong>
                <div class="col-span-2">
                    {{ format_date($recheck->recheck_start_at) }}
                </div>
                <strong class="font-bold col-span-1" style="white-space: nowrap;">Recheck</strong>
                <div class="col-span-7">
                    {{ $recheck->summary_body }}
                </div>
            </div>
        @endforeach

        <div class="print-text col-span-12 flex align-end">
            <strong class="font-bold mr-2">Hours</strong>
            <div class="inline border-b border-gray-400 w-24"></div>
        </div>
        <div class="print-text col-span-8 flex align-end">
            <strong class="font-bold mr-2">Outcome</strong>
            <span class="mr-2">Died</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Euth</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Returned to hosp</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">To other caregiver</span> <div class="inline border-b border-gray-400 flex-1"></div>
        </div>
        <div class="print-text col-span-4 flex align-end">
            <strong class="font-bold mr-2">Outcome Date</strong>
            <div class="inline border-b border-gray-400 flex-1"></div>
        </div>
        <div class="print-text col-span-12 flex align-end">
            <strong class="font-bold mr-2">How Released</strong>
            <span class="mr-2">Hard</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Soft</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Hack</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Self</span> <div class="inline border-b border-gray-400 w-10"></div>
            <span class="mr-2">Other</span> <div class="inline border-b border-gray-400 flex-1"></div>
        </div>
        <div class="print-text col-span-12 flex align-end">
            <strong class="font-bold mr-2">Where Released</strong>
            <div class="inline border-b border-gray-400 flex-1"></div>
        </div>
    </div>
@endif
