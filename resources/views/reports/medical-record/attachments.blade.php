<p style="page-break-before: always">
<h1>Image Attachments</h1>

@forelse($media as $image)
    @php $insecureUrl = str_replace('https', 'http', $image->medium_url); @endphp
    <img src='{{ $image->medium_url }}' alt='{{ $image->name }}' title='{{ $image->name }}' style='margin-bottom:20px'>
    <div class="row">
        <div class="col-md-6">
            <strong>Name:</strong> {{ $image->name }}
        </div>
        <div class="col-md-6">
            <strong>Date:</strong> {{ $image->custom_properties->obtained_at_formatted }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <strong>Source:</strong> {{ $image->custom_properties->source }}</p>
        </div>
        <div class="col-md-6">
            <strong>Description</strong> {{ $image->custom_properties->description }}
        </div>
    </div>
@empty
    <p>No images attached.</p>
@endforelse
