<p>{!! nl2br($body) !!}</p>

<p>This email was sent to you from {{ $user->name }}. All replies should be sent to {{ $user->email }}.</p>

<p>
The Wild Neighbors Database Project<br>
Wildlife Rehabilitation MD (WRMD)<br>
{!! url('/') !!}
</p>
