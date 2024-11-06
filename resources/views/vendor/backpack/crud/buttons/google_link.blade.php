
@if ($entry->status == 'Approved')
    <a href="{{ $entry->getGoogleCalendarLink() }}" target="_blank" class="btn btn-sm btn-link"><i class="la la-calendar"></i> Add to google calendar </a>
@endif
