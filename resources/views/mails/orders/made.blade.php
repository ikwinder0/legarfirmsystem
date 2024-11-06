@component('mail::message')
# Hello Admins,

<p>Below is the new order info.</p>

<div>
    <strong>Order Details</strong>
    <table style="font-size: 16px;border: none">
        <tr>
            <td>Order title</td>
            <td>: {{ $order->title}}</td>
        </tr>
        @if ($order->description)
        <tr>
            <td>Order description</td>
            <td>: {{ $order->description}}</td>
        </tr>
        @endif
        @if($order->guest)
        <tr>
            <td>User</td>
            <td>: {{ $order->guest->name}}</td>
        </tr>
        @endif
        @if($order->businessPartner)
        <tr>
            <td>In Charge Person</td>
            <td>: {{ $order->businessPartner->name}}</td>
        </tr>
        @endif
        <tr>
            <td>Order amount</td>
            <td>: RM {{ number_format($order->amount)}}</td>
        </tr>
        @if ($order->remarks)
        <tr>
            <td>Order remarks</td>
            <td>: {{ $order->remarks}}</td>
        </tr>
        @endif
    </table>
</div>
<br>
<p>Payment slip is attached.</p>
<p>Thank You
    <br>
    {{ config('app.name') }}
</p>

@endcomponent
