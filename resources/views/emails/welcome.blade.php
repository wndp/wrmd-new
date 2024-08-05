<x-mail::message>
## Hello {{ $user['name'] }}!

An administrator in {{ config('app.name') }} has created a user profile for you in their {{ $account['organization'] }} account. Below are your login in credentials. Remember them and keep them safe after **you delete this email**.

- Email address: {{ $user['email'] }}
- Password: {{ $password }}

<x-mail::button :url="route('login')">
Go to {{ config('app.name') }}.
</x-mail::button>

### Have Fun!
The {{ config('app.name') }} Team
</x-mail::message>
