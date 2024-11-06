@component('mail::message')
# Dear {{$user->name}},
{{--<p>Case Name: <a href="{{ route('case-detail.show', $case->id) }}" target="_blank">{{$case->title}}</a></p>--}}
<p>Case Name: {{$case->title}}</p>
<p>A Case {{ $case->title }} has been created by {{ $admin->name }}</p>

<br>

@if ($show_creds)
<p>Your login credentials: <br>
    User: {{$user->email}} <br>
    Password: {{$user->email}} <br>
    Please change your user credental when you first login.
{{--    <a href="{{ backpack_url('edit-account-info') }}">Edit--}}
{{--        password--}}
{{--    </a>--}}
</p>
@endif

<p>
    Thank You
    <br>
    {{ config('app.name') }}
</p>
@endcomponent