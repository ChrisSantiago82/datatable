<div>
    <div class="row col-md-12">
        <div class="col form-inline" style="margin-bottom: 15px">
            <span>Show: &nbsp;</span>
            <div class="col-md-2" style="margin-top: 15px">
                <a class="btn btn-light btn-xs dropdown-toggle" style="width: 140px;margin-bottom: 20px;" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20px">
                    @if($perPage == '10')
                        10
                    @elseif($perPage == '20')
                        20
                    @elseif($perPage == '30')
                        30
                    @elseif($perPage == '50')
                        50
                    @elseif($perPage == '100')
                        100
                    @endif
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','10')" style="margin-top:10px">
                        <span>10</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','20')" style="margin-top:10px">
                        <span>20</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','30')" style="margin-top:10px">
                        <span>30</span>
                    </a>
                    <a class="dropdown-item" href="javascript:window.livewire.emit('Pagination','50')" style="margin-top:10px">
                        <span>50</span>
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
        @if($OptionsPosition == 'front')
            <th></th>
        @endif

        @foreach($Data as $itemKey => $itemName)
                @if ($itemName['type'] == 'number')

                    <th class="text-nowrap" wire:click="sortBy(`{{$itemKey}}`)" style="cursor: pointer;text-align: right">
                        {{$itemName['columnName']}}
                        @include('datatable::sort-icon', ['field'=> $itemKey])
                    </th>
                @else
                    <th class="text-nowrap" wire:click="sortBy(`{{$itemKey}}`)" style="cursor: pointer">
                        {{$itemName['columnName']}}
                        @include('datatable::sort-icon', ['field'=> $itemKey])
                    </th>
                @endif
            @endforeach
        </tr>

        </thead>

        <tbody>
        @foreach($dataResult as $item)
            <tr wire:key="datatable-{{$item->id}}">
                @if($OptionsPosition == 'front')
                    @if($showOptions)
                        @include('datatable::extra-options', ['id'=> $item->id])
                    @endif
                @endif

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
                         @elseif ($itemName['type'] == 'limit')
                             <td>{{Str::limit($val, $itemName['limit'], '.')}}</td>
                        @elseif ($itemName['type'] == 'date')
                            <td>{{optional($val)->format($itemName['format'])}}</td>
                        @elseif ($itemName['type'] == 'email')
                            <td><a href="mailto:{{$val}}" target="_top">{{$val}}</a></td>
                        @elseif ($itemName['type'] == 'number')
                            <td style="text-align: right">{{number_format((float)$val,2,'.',"'")}}</td>
                         @elseif ($itemName['type'] == 'link')
                             <td style="color:#0e1950;  text-decoration: underline;">
                                 <a style="padding-right: 15px;cursor: pointer;" wire:click="emitOptions(`{{$itemName['event']}}`, {{$item->id}})" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$itemName['title']}}">
                                     @if($itemName['limit'] == null)
                                     {{$val}}
                                     @else
                                         {{Str::limit($val, $itemName['limit'], '.')}}
                                     @endif
                                 </a>
                             </td>
                         @elseif ($itemName['type'] == 'phone')
                             @php
                                 $number = preg_replace("/[^\d]/","",$val);

                                if(strlen($number) == 10) {
                                    $number = preg_replace($itemName['pattern'], $itemName['replacement'], $number);
                                }
                             @endphp
                            <td>
                                {{$number}}
                            </td>
                         @elseif ($itemName['type'] == 'lastRecord')
                             @include('datatable::lastrecord-option', ['id' => $item->id])

                         @elseif ($itemName['type'] == 'disable')
                            @include('datatable::status-option', ['optionKey' => $itemKey, 'itemValue' => $item->$itemKey, 'event' => $itemName['event'], 'id'=> $item->id])
                        @endif

                    @endforeach

                @if($OptionsPosition == 'end')
                    @if($showOptions)
                        @include('datatable::extra-options', ['id'=> $item->id])
                    @endif
                @endif

            </tr>
        @endforeach
        </tbody>

    </table>
    {{$dataResult->links()}}

</div>


