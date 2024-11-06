@component('mail::message')
# Dear {{ $user->name }},

<p>Appointment that was scheduled on {{ $date }} at {{ $time }} regarding case {{ $case->title }} has been {{ strtolower($status) }} by admin.</p>

<br>

@if ( strtolower($status) == "approved" )
Add to <a href="{{ $googleCalendarLink }}" target="_blank">Google Calendar</a>
<br>
@endif

<p>
    Thank You
<br>
    {{ config('app.name') }}
</p>
@endcomponent
