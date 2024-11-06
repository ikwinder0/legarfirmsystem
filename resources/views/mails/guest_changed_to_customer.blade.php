@component('mail::message')
# Dear Admin,

<p>Guest user {{ $user->name }} is changed to customer.</p>

<br>
<p>
    Thank You
<br>
    {{ config('app.name') }}
</p>
@endcomponent
