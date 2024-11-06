@if (request()->route()->getName() === 'time-slot.index')
    <a href="{{ url($crud->route . '/generate') }} " class="btn btn-xs btn-default"><i
            class="fa fa-ban"></i> Generate Time Slot</a>
@endif
