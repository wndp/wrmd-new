<x-mail::message>
# Contact Message From {{ $data['name'] }}
## Organization: {{ $data['organization'] }}
## Subject: {{ $data['subject'] }}

{{ $data['message'] }}

{{ config('app.name') }}
</x-mail::message>
