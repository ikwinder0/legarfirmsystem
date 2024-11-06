@component('mail::message')
# Dear {{$user->name}}

Thank You for connecting with us. <br>

Please, Verify your email for access., <br>
Email is : {{$user->email}}<br>
This link will expires in 10 minutes.

@component('mail::button', ['url' => $verification_data->url])
Verify Email
@endcomponent

Thanks<br>
{{ config('app.name') }}
@endcomponent
