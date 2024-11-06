@component('mail::message')
# Dear Admin,

<p>New appointment has been scheduled by {{ $user->name }} on {{ $date }} at {{ $time }} regarding case {{ $case->title }}.</p>

<br>
<p>
    Thank You
<br>
    {{ config('app.name') }}
</p>
@endcomponent
