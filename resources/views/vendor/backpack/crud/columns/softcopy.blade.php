@php
$values = $entry->{$column['name']}

@endphp

@if($values && count($values) > 0)
    @foreach($values as $val)
        <a href="{{ asset($val) }}">
            @php $type = (new \App\Helpers\AppHelper())->getFileType($val); @endphp
            @if($type == 'image')
                <img src="{{ asset($val) }}" height="150" />
                @else
                {!! $type !!}
            @endif
        </a>
    @endforeach
@endif