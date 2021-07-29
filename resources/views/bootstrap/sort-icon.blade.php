@if($sortBy !== $field)
    <i class="fa fa-sort" aria-hidden="true" style="color: #0e1950;"></i>
@elseif($sortDirection == 'asc')
    <i class="fa fa-chevron-up" aria-hidden="true" style="color: #0e1950;"></i>
@else
    <i class="fa fa-chevron-down" aria-hidden="true" style="color: #0e1950;"></i>
@endif
