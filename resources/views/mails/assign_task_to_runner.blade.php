@component('mail::message')
# Dear {{$user->name}},

<p>Runner Task {{ $title }} has been Assigned to You.</p>
<p>Description : <br/>{{ $description }}</p><br/>
<p>Status Remark : <br/>{{ $remarks }}</p>
<br>
<p>
    Thank You
<br>
    {{ config('app.name') }}
</p>
@endcomponent
