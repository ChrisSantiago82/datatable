<?php

namespace Chrissantiago82\Datatable\Http\Livewire;

use Chrissantiago82\Datatable\Classes\ExportExcelClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Laravie\SerializesQuery\Eloquent;
use Illuminate\Support\Facades\Crypt;

class Main extends Component
{

    use WithPagination;

    public $search = '';
    public $perPage;
    public $sortBy = '';
    public $sortDirection;
    public $sortArr = [];
    public $Data = [];
    public $Exceptions = [];
    public $ExtraData;
    public $StatusData;
    public $query;
    public $queryResult;
    public $showExcel;
    public $tableArr = [];
    public $showOptions;
    public $counterResult = [];
    public $excelFormat = [];
    public $OptionsPosition;

    public $filter = [];


    protected $listeners = ['sortBy', 'loadDataTable', 'Pagination'];

    protected $paginationTheme = 'bootstrap';

    public function mount($tableArr = null, $query = null, $filter = null)
    {
        $this->perPage = 30;
        $this->getFilterSortFromSession();
        $this->tableStruct();
        $this->getFilterFromSession();
    }

    public function tableStruct()
    {
        foreach ($this->tableArr as $itemKey => $itemArr) {
            if ($itemKey == 'Columns') {
                $this->Data = $itemArr;
            }

            if ($itemKey == 'Buttons') {
                $this->ExtraData = $itemArr;
            }

            if ($itemKey == 'Status') {
                $this->StatusData = $itemArr;
            }

            if ($itemKey == 'ExcelButton') {
                $this->showExcel = $itemArr;
            }

            if ($itemKey == 'ExcelExceptions') {
                $this->Exceptions = $itemArr;
            }

            if ($itemKey == 'Options') {
                $this->showOptions = $itemArr;
            }

            if ($itemKey == 'Position') {
                $this->OptionsPosition = $itemArr;
            }

            if ($itemKey == 'Counter') {
                $this->loadDataCounter();
            }

            if ($itemKey == 'ExcelFormat') {
                $this->excelFormat = $itemArr;
            }

            if ($itemKey == 'DefaultSort' and $this->sortDirection == '' and $this->sortBy == '') {
                $firstKey = array_key_first($itemArr);
                $this->sortDirection = reset($itemArr);

                $this->sortBy($firstKey);
            }
        }

    }

    public function queryStruct()
    {
        return;

        $newQuery = $this->queryResult;

        if ($this->sortBy !== '') {
            if ($this->sortDirection == 'asc') {
                $newQuery = $this->query->sortBy($this->sortBy, SORT_NATURAL | SORT_FLAG_CASE);

            } else {
                $newQuery = $this->query->sortByDesc($this->sortBy, SORT_NATURAL | SORT_FLAG_CASE);
            }
        } else {
            $newQuery = $this->query->sortBy('id');
        }

        if ($this->search !== null and $this->search != '') {

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

        if ($this->filter != null) {
            foreach ($this->filter as $itemKey => $itemValue) {
                if ($itemValue !== null) {
                    $newQuery = $newQuery->where($itemKey, '=', $itemValue);
                } else {
                    $newQuery = $newQuery->where($itemKey, '!=', $itemValue);
                }

            }
        }

        //Load Counts
        if (isset($this->tableArr['withCount'])) {
            foreach ($this->tableArr['withCount'] as $withcount) {
                $newQuery->loadCount($withcount);
            }
        }

        return $newQuery;
    }

    public function loadDataCounter()
    {
        foreach ($this->Data as $itemKey => $dataValue) {
            if ($dataValue['type'] == 'counter') {
                $counterData = $dataValue['value'];

                foreach ($this->query as $query) {
                    $this->counterResult[$query->$itemKey] = $query->$counterData->count();
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
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;

        $this->queryStruct();
        $this->saveFilterSortToSession();

        return $this->sortBy;
    }

    public function updatedSearch()
    {
        $this->saveFilterToSession();
    }

    public function loadDataTable($filterData)
    {
        $this->filter = $filterData;
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

        //Full Collection

        return Excel::download(new ExportExcelClass('full_collection', $query, $this->Data, $this->Exceptions, $this->excelFormat), now()->toDateString() . ' excel.xlsx');
    }

    public function saveFilterToSession()
    {
        Session::put('tb_' . $this->tableArr['key'], $this->search);
    }

    public function getFilterFromSession()
    {
        $this->search = Session::get('tb_' . $this->tableArr['key'], '');
    }

    public function saveFilterSortToSession()
    {
        Session::put('sort_' . $this->tableArr['key'], $this->sortBy);
        Session::put('sort_d' . $this->tableArr['key'], $this->sortDirection);

    }

    public function getFilterSortFromSession()
    {
        $this->sortBy = Session::get('sort_' . $this->tableArr['key'], '');
        $this->sortDirection = Session::get('sort_d' . $this->tableArr['key'], '');

    }


    protected function buildQuery()
    {
        $query = Eloquent::unserialize(Crypt::decrypt($this->tableArr['Query']));

        if ($this->sortBy !== '') {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            $query->orderBy('id');
        }


        $query->where(function($q) {
            foreach ($this->tableArr['Columns'] as $key => $column) {

                if (Str::contains($key, '.') === false) {
                    $q->orWhere($key, 'LIKE', '%'.$this->search.'%');
                } else {
                    $exploted = explode('.', $key);
                    $q->orWhereHas($exploted[0], function ($re) use ($exploted){
                        $re->where($exploted[1], 'LIKE', '%'.$this->search.'%');
                    });
                }
            }
        });



        return $query;
    }


    public function render()
    {
        /*
        $perPage = $this->perPage;
        $collection = $this->queryStruct();
        $items = $collection->forPage($this->page, $perPage);
        $paginator = new LengthAwarePaginator($items, $collection->count(), $perPage, $this->page);
        $this->resetPage();
        */

        return view('datatable::main', ['dataResult' => $this->buildQuery()->paginate($this->perPage)]);

    }
}
