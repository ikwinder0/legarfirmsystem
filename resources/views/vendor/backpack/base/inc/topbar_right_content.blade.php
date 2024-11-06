<!-- This file is used to store topbar (right) items -->

<style>
    .notifications {
        opacity: 0;
        z-index: 100;
        position: relative;
        transition: all .1s ease-in;
        display: none;
    }

    .notifications ul {
        width: max-content;
        list-style: none;
        padding: 0;
        position: absolute;
        top: 20px;
        right: 15px;
        background-color: #fff;
        border-radius: 5px 0px 5px 5px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .notifications li {
        margin: 0.5em 0;
        padding: 0 2em;
    }

    .notifications li:first-child {
        border-bottom: 1px solid #666;
        margin-bottom: 1em;
    }

    .notifications li:last-child {
        border-bottom: none;
    }

    .notifications.active {
        opacity: 1;
        height: max-content;
        display: block;
    }
</style>

@php
$nots = \App\Models\Notification::where(
[
['is_read', false],
['uid', backpack_user()->id]
]
);
$top5 = $nots->latest()->take(3)->get();
@endphp


<li class="nav-item d-md-down-none"><a class="nav-link" id="bell" href="#"><i class="la la-bell"
            style="font-size: 24px; padding-top: 10px; margin-left: -5px;"></i><span
            class="badge badge-pill badge-danger">{{ $nots->count() }}</span></a></li>
{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-list"></i></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}
<div class="notifications" id="box">
    <ul>
        <li>You have {{ $nots->count() }} new notifications <a href="notification/mark-all-read"
                class="d-inline-block ml-5 btn-link">Mark all as read</a></li>
        @foreach ($top5 as $notification)

        @if ($notification->order_id)
        <li><a class="text-dark"
                href="{{ route('order.show', ['id' => $notification->order_id]) . '?not_id='.$notification->id }}">{{
                $notification->text }}</a></li>
        @elseif($notification->appointment_id)
        <li><a class="text-dark"
                href="{{ route('appointment.show', ['id' => $notification->appointment_id]) . '?not_id='.$notification->id }}">{{
                $notification->text }}</a></li>
        @elseif($notification->case_id)
        <li><a class="text-dark"
                href="{{ route('case-detail.show', ['id' => $notification->case_id]) . '?not_id='.$notification->id }}">{{
                $notification->text }}</a></li>
        @else
        <li><a class="text-dark" href="{{ route('notification.show', ['id' => $notification->id]) }}">{{
                $notification->text }}</a></li>
        @endif

        @endforeach
        <li><a href="{{ backpack_url('notification') }}" class="btn-link">View All</a></li>
    </ul>
</div>

<script>
    const bellEl = document.getElementById('bell');
    const boxEl = document.getElementById('box');

    bellEl.addEventListener('click', () => {
        boxEl.classList.toggle('active');
    });
</script>