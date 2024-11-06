{{-- regular object attribute --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['limit'] = $column['limit'] ?? 32;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['text'] = $column['default'] ?? '-';

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    if(is_array($column['value'])) {
        $column['value'] = json_encode($column['value']);
    }

    if(!empty($column['value'])) {
        $column['text'] = $column['prefix'].Str::limit($column['value'], $column['limit'], '…').$column['suffix'];
    }
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        @if($column['escaped'])
            <span class="text-center"><small>{{ $column['text'] }}</small></span>
        @else
            {!! $column['text'] !!}
        @endif
        @php
            $percentage = 0;
            if( $column['text'] == "Receive Order" )
            {
                $percentage = 10;
            }
            else if( $column['text'] == "Assign P.I.C" )
            {
                $percentage = 20;
            }
            else if( $column['text'] == "Arrange For Signing" )
            {
                $percentage = 30;
            }
            else if( $column['text'] == "Stamping Date" )
            {
                $percentage = 40;
            }
            else if( $column['text'] == "Bank Execution / Request Redemption" )
            {
                $percentage = 50;
            }
            if( $column['text'] == "Advise 1st Release" )
            {
                $percentage = 60;
            }
            else if( $column['text'] == "Discharge Of Charge" )
            {
                $percentage = 70;
            }
            else if( $column['text'] == "Present MOT" )
            {
                $percentage = 80;
            }
            else if( $column['text'] == "Advise Final Release" )
            {
                $percentage = 90;
            }
            else if( $column['text'] == "Completion" )
            {
                $percentage = 100;
            }
        @endphp
        <br/>
        <div class="progress">
          <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ $percentage }}%</div>
        </div>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>
