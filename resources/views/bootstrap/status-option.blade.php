<td>
    @if($itemValue !== null)
        <a style="padding-left: 8px; padding-right: 4px;cursor: pointer;" wire:click="emitStatus(`{{$event}}`, {{$id}}, false)">
            <i class="fa fa-times" style="color: red" aria-hidden="true"></i>
        </a>
    @else
        <a style="padding-left: 8px; padding-right: 4px;cursor: pointer;" wire:click="emitStatus(`{{$event}}`,{{$id}}, true)">
            <i class="fa fa-check" aria-hidden="true" style="color:darkgreen; "></i>
        </a>
    @endif
</td>
