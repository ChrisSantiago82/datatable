<td class="text-center">
    @foreach($ExtraData as $optionKey => $option)
        <a style="padding-right: 15px;cursor: pointer" wire:click="emitOptions(`{{$option['event']}}`, {{$id}})" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$option['title']}}">
            <i class="{{$option['icon']}}" aria-hidden="true" style="{{$option['style']}}"></i>
        </a>
    @endforeach
</td>
