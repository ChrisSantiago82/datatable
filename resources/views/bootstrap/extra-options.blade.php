<td class="text-center text-nowrap">
    @foreach($ExtraData as $optionKey => $option)
        <a style="padding-right: 8px;cursor: pointer" wire:click="emitOptions(`{{$option['event']}}`, {{$id}})" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$option['title']}}">
            <i class="{{$option['icon']}}" aria-hidden="true" style="{{$option['style']}}"></i>
        </a>
    @endforeach
</td>
