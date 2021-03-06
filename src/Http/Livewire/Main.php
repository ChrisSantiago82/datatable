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
    public $counterResult;
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
        $this->getFilterPageFromSession();
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

            if ($itemKey == 'Filter') {
                $this->filter = $itemArr;
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

    public function Pagination($page)
    {
        $this->perPage = $page;
        $this->saveFilterPageToSession();
    }

    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;

        $this->buildQuery();
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
        $query = $this->buildQuery();

        //Full Collection

        return Excel::download(new ExportExcelClass('full_collection', $query->get(), $this->Data, $this->Exceptions, $this->excelFormat), now()->toDateString() . ' excel.xlsx');
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

    public function saveFilterPageToSession()
    {
        Session::put('page_' . $this->tableArr['key'], $this->perPage);
    }

    public function getFilterPageFromSession()
    {
        $this->perPage = Session::get('page_' . $this->tableArr['key'], 30);
    }

    protected function buildQuery()
    {
        $query = Eloquent::unserialize(Crypt::decrypt($this->tableArr['Query']));

        if (count($this->filter) > 0) {
            $key = key($this->filter);
            $value = reset($this->filter);

            if ($value !== null) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            } else {
                $query->where($key, '=', null);
            }
        }


        if ($this->search !== null and $this->search != '') {

            $baseTableName = $query->newModelInstance()->getTable();

            $query->where(function ($q) use($baseTableName) {
                foreach ($this->tableArr['Columns'] as $key => $column) {
                    if($column['search'] === true) {
                        if (Str::contains($key, '.') === false) {
                            $q->orWhere($baseTableName . '.' . $key, 'LIKE', '%' . $this->search . '%');
                        } else {
                            $exploted = explode('.', $key);
                            $q->orWhereHas($exploted[0], function ($re) use ($exploted) {
                                $re->where($exploted[1], 'LIKE', '%' . $this->search . '%');
                            });
                        }
                    }
                }
            });
        }

        if ($this->sortBy !== '') {

            if (Str::contains($this->sortBy, '.') === false) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            } elseif($this->Data[$this->sortBy]['model'] !== null) {
                $baseTableName = $query->newModelInstance()->getTable();
                $relatedModelName = Str::before($this->sortBy, '.');
                $relatedSearchField = Str::afterLast($this->sortBy, '.');
                $relatedModel = '\\App\\Models\\'.$this->Data[$this->sortBy]['model'];
                $relatedTableName = (new $relatedModel)->getTable();
                $query->select($baseTableName.'.*');
                $query->joinRelation($relatedModelName);
                $query->orderBy($relatedTableName . '.'.$relatedSearchField, $this->sortDirection);
            } else {
                $baseTableName = $query->newModelInstance()->getTable();
                $relatedModelName = Str::before($this->sortBy, '.');
                $relatedSearchField = Str::afterLast($this->sortBy, '.');
                $relatedModel = '\\App\\Models\\'.$relatedModelName;
                $relatedTableName = (new $relatedModel)->getTable();
                $query->select($baseTableName.'.*');
                $query->joinRelation($relatedModelName);
                $query->orderBy($relatedTableName . '.'.$relatedSearchField, $this->sortDirection);
            }
        } else {
            $query->orderBy('id');
        }


        return $query;
    }


    public function render()
    {
        $query = $this->buildQuery()->paginate($this->perPage);
        $this->resetPage();

        return view('datatable::main', ['dataResult' => $query]);
    }
}
