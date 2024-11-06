@php
$value = $entry->{$column['name']}

@endphp

@if($value)
    @if (is_array($value))
        @foreach ($value as $v)
            <a href="{{ asset($v) }}" target="_blank">
                @php $type = (new \App\Helpers\AppHelper())->getFileType($v); @endphp
                @if($type == 'image')
                    <img src="{{ asset($v) }}" height="150" class="mb-5" />
                @else
                    {!! $type !!}
                @endif
                <br>
            </a>
        @endforeach
    @else
        <a href="{{ asset($value) }}" target="_blank">
            @php $type = (new \App\Helpers\AppHelper())->getFileType($value); @endphp
            @if($type == 'image')
                <img src="{{ asset($value) }}" height="150" />
            @else
                {!! $type !!}
            @endif
        </a>
    @endif
@endif
