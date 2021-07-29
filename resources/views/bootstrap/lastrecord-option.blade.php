<td>
    @foreach($lastRecord as $recordKey => $record)
        @if($recordKey == $id)
            {{$record}}
        @endif
    @endforeach
</td>
