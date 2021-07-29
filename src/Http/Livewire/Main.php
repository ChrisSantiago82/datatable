<?php

namespace Chrissantiago82\Datatable\Http\Livewire;

use Chrissantiago82\Datatable\Classes\ExportExcelClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Main extends Component
{

    use WithPagination;

    public $search = '';
    public $perPage;
    public $sortBy = '';
    public $sortDirection = true;
    public $sortArr = [];
    public $Data = [];
    public $ExtraData;
    public $StatusData;
    public $query;
    public $queryResult;
    public $FilterData;
    public $showExcel;
    public $tableArr = [];
    public $showOptions;
    public $counterResult = [];
    public $lastRecord = [];


    protected $listeners = ['sortBy', 'loadDataTable', 'Pagination'];

    protected $paginationTheme = 'bootstrap';

    public function mount($tableArr = null, $query = null)
    {
        $this->perPage = 10;
        $this->tableStruct();
        $this->getFilterFromSession();
    }

    public function tableStruct()
    {
        foreach ($this->tableArr as $itemKey => $itemArr)
        {
            if($itemKey == 'Columns')
            {
                $this->Data = $itemArr;
            }

            if($itemKey == 'Buttons')
            {
                $this->ExtraData = $itemArr;
            }

            if($itemKey == 'Status')
            {
                $this->StatusData = $itemArr;
            }

            if($itemKey == 'Filters')
            {
                $this->FilterData = $itemArr;
            }

            if($itemKey == 'ExcelButton')
            {
                $this->showExcel = $itemArr;
            }

            if($itemKey == 'Options')
            {
                $this->showOptions = $itemArr;
            }

            if($itemKey == 'Counter')
            {
                $this->loadDataCounter();
            }

            if($itemKey == 'LastRecord')
            {
                $this->loadLastRecord();
            }
        }

    }

    public function queryStruct()
    {
        $newQuery = $this->queryResult;

        if($this->sortBy !== ''){
            if($this->sortDirection == 'asc'){
                $newQuery = $this->query->sortBy($this->sortBy, SORT_NATURAL | SORT_FLAG_CASE);

            }else{
                $newQuery = $this->query->sortByDesc($this->sortBy, SORT_NATURAL | SORT_FLAG_CASE);
            }
        }else{
            $newQuery = $this->query->sortBy('id');
        }

        if ($this->search !== null AND $this->search != '') {

            $newQuery = $newQuery->filter(function ($value, $key) {

                $search = strtolower($this->search);

                foreach ($this->Data as $data => $data2) {
                    if (Str::contains($data, '.')) {
                        $collection = Str::of($data)->explode('.');
                        $val = $value;

                        foreach ($collection as $rel) {
                            if ($val === null) {
                                break;
                            }
                            $val = optional($val)->$rel;
                        }
                        if (strpos(strtolower((string)$val), $search) !== false) {
                            return true;
                        }

                    } else {
                        if (strpos(strtolower((string)$value->$data), $search) !== false) {
                            return true;
                        }
                    }
                }

                return false;
            });
        }

        if($this->FilterData != null)
        {
            foreach ($this->FilterData as $itemKey => $itemValue) {
                if($itemValue !== null)
                {
                    $newQuery = $newQuery->where($itemKey, '=', $itemValue);
                }else{
                    $newQuery = $newQuery->where($itemKey, '!=', $itemValue);
                }

            }
        }

        return $newQuery;

    }

    public function loadDataCounter()
    {
        foreach ($this->Data as $itemKey => $dataValue)
        {
            if($dataValue['type'] == 'counter')
            {
                $counterData= $dataValue['value'];

                foreach ($this->query as $query)
                {
                    $this->counterResult[$query->$itemKey] = $query->$counterData->count();
                }
            }

        }
    }

    public function loadLastRecord()
    {
        foreach ($this->Data as $itemKey => $dataValue)
        {
            if($dataValue['type'] == 'lastRecord')
            {
                $lastValue= $dataValue['value'];

                foreach ($this->query as $query)
                {
                    $lastRecord = $query->$lastValue->last();

                    if($lastRecord != null)
                    {
                        $recordValue = $lastRecord->$itemKey->format($dataValue['format']);

                        $this->lastRecord[$query->id] = $recordValue;
                    }
                }
            }

        }
    }

    public function Pagination($page)
    {
        $this->perPage = $page;
        $this->queryStruct();
    }

    public function sortBy($field)
    {
        if($this->sortDirection == 'asc'){
            $this->sortDirection = 'desc';
        }else{
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;

        $this->queryStruct();

        return $this->sortBy;
    }

    public function updatedSearch()
    {
        $this->saveFilterToSession();
        $this->queryStruct();
    }

    public function loadDataTable($filterData)
    {
        $this->FilterData = $filterData;
        $this->queryStruct();
    }

    public function emitOptions($method, $id)
    {
        $this->emit($method, $id);
    }

    public function emitStatus($method, $id, $status)
    {
        $this->emit($method, $id, $status);
    }

    public function downloadExcel()
    {
        $query = $this->queryStruct();

        return Excel::download(new ExportExcelClass($query, $this->Data),now()->toDateString() . ' excel.xlsx');
    }

    public function saveFilterToSession()
    {
        Session::put('search_filter', $this->search);
    }

    public function getFilterFromSession()
    {
        $this->search = Session::get('search_filter', '');
    }

    public function render()
    {
        $perPage = $this->perPage;

        $collection = $this->queryStruct();

        $items = $collection->forPage($this->page, $perPage);

        $paginator = new LengthAwarePaginator($items, $collection->count(), $perPage, $this->page);

        return view('datatable::main', ['dataResult' => $paginator] );

    }
}
