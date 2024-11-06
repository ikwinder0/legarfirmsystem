<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item">
    <a class="nav-link d-flex mx-1" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}
    </a>
</li>
@if (backpack_user()->hasRole(['Admin', 'Super Admin']))
<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
    <a class="nav-link d-flex mx-1 nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('user') }}">
                <i class="nav-icon la la-user"></i>
                <span>
                    Users
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('role') }}">
                <i class="nav-icon la la-id-badge"></i>
                <span>Roles</span>
            </a>
        </li>
    </ul>
</li>
@endif

@if (backpack_user()->hasRole('Super Admin'))
<li class="nav-item nav-dropdown">
    <a class="nav-link d-flex mx-1 nav-dropdown-toggle" href="#"><i class="nav-icon la la-cog"></i> Settings</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('transfer-memo') }}'><i class='nav-icon la la-exchange'></i> Memorandum of transfer</a></li>

        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('sale-purchase-agreement') }}'><i class='nav-icon la la-dollar'></i> Property legal fees</a></li>

        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('transfer-memo-stamp-duty') }}'><i class='nav-icon la la-home'></i> Property stamp duty</a></li>

        <li class='nav-item'>
            <a class='nav-link d-flex mx-1' href='{{ backpack_url('case-size-point-setting') }}'>
                <i class='nav-icon la la-user-cog'></i>
                Case size point settings
            </a>
        </li>

        <li class='nav-item'>
            <a class='nav-link d-flex mx-1' href='{{ backpack_url('setting') }}'>
                <i class='nav-icon la la-cog'></i>
                Settings
            </a>
        </li>

        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('calculator') }}'><i class='nav-icon la la-calculator'></i>Calculators</a></li>

        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('calculator-item') }}'><i class='nav-icon la la-plus'></i>Add section item</a></li>
    </ul>
</li>
@endif

@if (!backpack_user()->hasRole(['Customer','Runner']))
<li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('order') }}'><i class='nav-icon la la-cart-arrow-down'></i> Orders</a></li>
@endif

@if (backpack_user()->hasRole(['Admin','Super Admin','Runner']))
 <li class='nav-item'><a class='nav-link' href='{{ backpack_url('runner-task') }}'><i class='nav-icon las la-tasks'></i> Runner Tasks</a></li>
@endif
@if (!backpack_user()->hasRole(['Runner']))
<li class='nav-item'>
    <a class='nav-link d-flex mx-1' href='{{ backpack_url('case-detail') }}'>
        <i class='nav-icon la la-file-alt'></i>
        Case Management
    </a>
</li>
@endif
@if (!( backpack_user()->hasRole('Business Partner') || backpack_user()->hasRole('Senior Business Partner') || backpack_user()->hasRole('Runner') ) )
<li class="nav-item nav-dropdown">
    <a class="nav-link d-flex mx-1 nav-dropdown-toggle" href="#"><i class="nav-icon la la-handshake"></i> Appointment
        Schedule</a>
    <ul class="nav-dropdown-items">

        @if (backpack_user()->hasRole(['Admin', 'Super Admin']))
        <li class='nav-item'>
            <a class='nav-link' href='{{ backpack_url('time-slot') }}'>
                <i class='nav-icon la la-clock'></i> Time slots
            </a>
        </li>
        @endif
        <li class='nav-item'>
            <a class='nav-link' href='{{ backpack_url('appointment') }}'>
                <i class='nav-icon la la-calendar'></i> Appointments
            </a>
        </li>
    </ul>
</li>
@endif
@if (backpack_user()->hasRole(['Admin', 'Senior Business Partner', 'Super Admin']))
<li class="nav-item nav-dropdown">
    <a class="nav-link d-flex mx-1 nav-dropdown-toggle" href="#"><i class="nav-icon la la-folder-open"></i> Reports</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'>
            <a class='nav-link' href='{{ backpack_url('reports/point-earned') }}'>
                <i class='nav-icon la la-file-powerpoint'></i>
                Point earned reports
            </a>
        </li>
    </ul>
</li>
@endif

@if (!backpack_user()->hasRole(['Customer','Runner']))
@php
$calculators = \App\Models\Calculator::all();
@endphp
<li class="nav-item nav-dropdown">
    <a class="nav-link d-flex mx-1 nav-dropdown-toggle" href="#"><i class="nav-icon la la-money-bill"></i> Quotations</a>
    <ul class="nav-dropdown-items">
        @foreach ($calculators as $calculator)
        @if($calculator->type == 'spa')
        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('purchase') }}'><i class='nav-icon la la-money-bill'></i>{{ $calculator->name }}</a></li>
        @elseif($calculator->type == 'loan')
        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('loan') }}'><i class='nav-icon la la-money-bill-alt'></i>{{ $calculator->name }}</a></li>
        @elseif($calculator->type == 'master_title_loan')
        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('master-title-loan') }}'><i class='nav-icon la la-money'></i>{{ $calculator->name }}</a></li>
        @elseif($calculator->type == 'cost_of_assist_vendor')
        <li class='nav-item'><a class='nav-link d-flex mx-1' href='{{ backpack_url('cost-of-assist-vendor') }}'><i class='nav-icon la la-money-bill'></i>{{ $calculator->name }}</a></li>
		@elseif($calculator->type == 'loan_refinance')
		<li class='nav-item'><a class='nav-link' href='{{ backpack_url('refinance-loan') }}'><i class='nav-icon la la-money'></i> {{ $calculator->name }}</a></li>
        @endif
        @endforeach
    </ul>
</li>

@endif


