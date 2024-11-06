@component('mail::message')
# Dear Admin,
<p>Task Title: {{$task->title}}</p>
<p>Runner Task {{ $task->title }}'s status has been changed from {{ $old_status }} to {{ $status }} by {{ $user->name }}</p>
<p>Remark : <br/>{!! $task->remarks !!}</p>
<br>
<p>Thank You
    <br>
    {{ config('app.name') }}
</p>
@endcomponent
