<div>
    <div class="row col-md-12">
        <div class="col form-inline" style="margin-bottom: 15px">
            <span>Show: &nbsp;</span>
            <div class="col-md-2" style="margin-top: 15px">
                <a class="btn btn-light btn-xs dropdown-toggle" style="width: 140px;margin-bottom: 20px;" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20px">
                    @if($perPage == '2')
                        2
                    @elseif($perPage == '10')
                        10
                    @elseif($perPage == '20')
                        20
                    @elseif($perPage == '100')
                        100
                    @endif
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','2')" style="margin-top:10px">
                        <span>2</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','10')" style="margin-top:10px">
                        <span>10</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','20')" style="margin-top:10px">
                        <span>20</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','100')" style="margin-top:10px">
                        <span>100</span>
                    </a>
                </div>
            </div>

        </div>

        <div class="btn-group float-right" style="margin-right: -40px">
            <input wire:model.debounce.300ms="search" class="form-control" placeholder="Search..." type="text">
        </div>

    </div>

    @if($showExcel)
    <div class="col-md-12" style="margin-top: -10px; margin-bottom: 10px;">
        <div class="btn-group">
            <button class="btn btn-success btn-xs" wire:click="downloadExcel">Download Excel</button>
        </div>
    </div>
    @endif

    <table class="table table-striped table-sm" style="width: 100% !important;">

        <thead>
        <tr>
            @foreach($Data as $itemKey => $itemName)
                <th wire:click="sortBy(`{{$itemKey}}`)" style="cursor: pointer">
                    {{$itemName['columnName']}}
                    @include('datatable::sort-icon', ['field'=> $itemKey])
                </th>
            @endforeach
        </tr>

        </thead>

        <tbody>
        @foreach($dataResult as $item)
            <tr wire:key="datatable-{{$item->id}}">
                @foreach($Data as $itemKey => $itemName)
                     @if (Str::contains($itemKey, '.'))
                            @php
                                $collection = Str::of($itemKey)->explode('.');
                                $val = $item;
                                foreach ($collection as $rel) {
                                    if ($val === null) {
                                        break;
                                    }
                                    $val = optional($val)->$rel;
                                }
                            @endphp
                        @else
                            @php
                              $val = $item->$itemKey;
                            @endphp
                        @endif

                        @if ($itemName['type'] === null)
                            <td>{{$val}}</td>
                        @elseif ($itemName['type'] == 'date')
                            <td>{{optional($val)->format($itemName['format'])}}</td>
                        @elseif ($itemName['type'] == 'email')
                            <td><a href="mailto:{{$val}}" target="_top">{{$val}}</a></td>
                        @elseif ($itemName['type'] == 'number')
                            <td>{{number_format((float)$val,2,'.',"'")}}</td>
                         @elseif ($itemName['type'] == 'counter')
                             @include('datatable::counter-option', ['id' => $item->id])
                         @elseif ($itemName['type'] == 'lastRecord')
                             @include('datatable::lastrecord-option', ['id' => $item->id])

                         @elseif ($itemName['type'] == 'disable')
                            @include('datatable::status-option', ['optionKey' => $itemKey, 'itemValue' => $item->$itemKey, 'event' => $itemName['event'], 'id'=> $item->id])
                        @endif

                    @endforeach

                    @if($showOptions)
                        @include('datatable::extra-options', ['id'=> $item->id])
                    @endif

            </tr>
        @endforeach
        </tbody>

    </table>
    {{$dataResult->links()}}

</div>


