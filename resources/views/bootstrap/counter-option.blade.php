<td>
    @foreach($counterResult as $counterKey => $counterValue)
        @if($counterKey == $id)
            {{$counterValue}}
        @endif
    @endforeach
</td>
