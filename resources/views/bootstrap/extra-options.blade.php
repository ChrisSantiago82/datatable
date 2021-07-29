<td>
    @foreach($ExtraData as $optionKey => $option)

        @if($optionKey != 'risk' AND $optionKey != 'riskModal')
            <a class="pull-right" style="padding-left: 8px; padding-right: 4px;cursor: pointer" wire:click="emitOptions(`{{$option['event']}}`, {{$id}})">
                <i class="{{$option['icon']}}" aria-hidden="true" style="{{$option['style']}}"></i>
            </a>
        @else
            @if($optionKey == 'risk')
            <a class="btn btn-info btn-xs" style="color: white" wire:click="emitOptions(`{{$option['event']}}`,{{$id}})">Risk</a>
            @endif

            @if($optionKey == 'riskModal')
                <a class="btn btn-primary btn-xs" style="color: white" wire:click="emitOptions(`{{$option['event']}}`,{{$id}})">Form</a>
            @endif
        @endif

    @endforeach
</td>
