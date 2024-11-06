
@if ( (backpack_user()->hasRole("Admin") || backpack_user()->hasRole("Super Admin") ) && $entry->guest_id)
    <a href="{{ url('/admin/user/'.$entry->guest_id.'/edit') }}" class="btn btn-sm btn-link"><i class="la la-user"></i> Guest Info</a>
@endif
