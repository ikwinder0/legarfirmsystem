@if (backpack_user()->hasRole('Super Admin'))
<a href="{{ route('calculator.report', [ "id"=> $entry->id ]) }}" class="btn btn-sm btn-link"><i
        class="la la-calendar"></i> Show report</a>
@endif