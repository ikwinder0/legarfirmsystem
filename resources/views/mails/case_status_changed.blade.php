@component('mail::message')
# Dear {{$user->name}},
{{--<p>Case Name: <a href="{{ route('case-detail.show', $case->id) }}" target="_blank">{{$case->title}}</a></p>--}}
<p>Case Name: {{$case->title}}</p>
<p>Your case {{ $case->title }}'s status has been changed from {{ $old_status }} to {{ $case->status }} by {{ $admin->name }}</p>
<p>Status Remark : <br/>{!! $remarks !!}</p>
<br>
<p>Thank You
    <br>
    {{ config('app.name') }}
</p>
@endcomponent
