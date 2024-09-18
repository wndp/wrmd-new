<div class="grid grid-cols-6 gap-2 text-base text-black border-b border-black pb-4 mt-20">
    <div class="font-bold">Permittee</div>
    <div class="col-span-2">{{ $account->organization }}</div>
    <div class="font-bold">Phone number</div>
    <div class="col-span-2">{{ $account->phone_number }}</div>
    <div class="font-bold">Address</div>
    <div class="col-span-2">{!! $account->full_address !!}</div>
    <div class="font-bold">Email</div>
    <div class="col-span-2">{{ $account->contact_email }}</div>
    <div class="font-bold">Federal Permit #</div>
    <div class="col-span-2">{!! $account->federal_permit_number !!}</div>
    <div class="font-bold">{{ ucwords(locale()->subdivision()) }} Permit #</div>
    <div class="col-span-2">{{ $account->subdivision_permit_number }}</div>
</div>
