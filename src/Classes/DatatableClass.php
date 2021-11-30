<?php


namespace Chrissantiago82\Datatable\Classes;

use Laravie\SerializesQuery\Eloquent;
use Illuminate\Support\Facades\Crypt;

class DatatableClass
{
    public $Result = [];
    protected $hasString;

    public function tableColumn($key, $columnName)
    {
        $this->Result['Columns'][$key]['columnName'] = $columnName;
        $this->Result['Columns'][$key]['type'] = null;
        $this->Result['Columns'][$key]['format'] = null;
        $this->Result['Columns'][$key]['event'] = null;
        $this->Result['Columns'][$key]['value'] = null;
        $this->Result['Columns'][$key]['limit'] = null;
        $this->buildKey($columnName);
    }

    protected function buildKey($columnName)
    {
        $this->hasString = $this->hasString.$columnName;
        $this->Result['key'] = hash('md5', $this->hasString);
    }


    public function tableColumnType($key, $type)
    {
        $this->Result['Columns'][$key]['type'] = $type;
    }

    public function tableColumnFormat($key, $format)
    {
        $this->Result['Columns'][$key]['format'] = $format;
    }

    public function tableColumnLimit($key, $limit)
    {
        $this->Result['Columns'][$key]['type'] = 'limit';
        $this->Result['Columns'][$key]['limit'] = $limit;
    }

    public function formatDate($key, $format)
    {
        $this->Result['Columns'][$key]['type'] = 'date';
        $this->Result['Columns'][$key]['format'] = $format;
    }

    public function formatEmail($key, $format)
    {
        $this->Result['Columns'][$key]['type'] = 'email';
        $this->Result['Columns'][$key]['format'] = $format;
    }

    public function formatNumber($key, $format)
    {
        $this->Result['Columns'][$key]['type'] = 'number';
        $this->Result['Columns'][$key]['format'] = $format;
    }

    public function formatLink($key, $event, $title)
    {
        $this->Result['Columns'][$key]['type'] = 'link';
        $this->Result['Columns'][$key]['event'] = $event;
        $this->Result['Columns'][$key]['title'] = $title;
    }


    public function formatDisableBtn($key, $event)
    {
        $this->Result['Columns'][$key]['type'] = 'disable';
        $this->Result['Columns'][$key]['event'] = $event;
    }

    public function formatCounter($key, $value)
    {
        $this->Result['Columns'][$key]['type'] = 'counter';
        $this->Result['Columns'][$key]['value'] = $value;
    }

    public function withCount($relation)
    {
        $newArray[] = $relation;
        $this->Result['withCount'] = $newArray;
    }

    public function formatLastRecord($key, $value, $format)
    {
        $this->Result['Columns'][$key]['type'] = 'lastRecord';
        $this->Result['Columns'][$key]['value'] = $value;
        $this->Result['Columns'][$key]['format'] = $format;
    }

    public function tableAvailableLastRecord($value)
    {
        $this->Result['LastRecord'] = $value;
    }

    public function tableAvailableCounter($value)
    {
        $this->Result['Counter'] = $value;
    }

    public function tableAvailableButtons($value)
    {
        $this->Result['Options'] = $value;
    }

    public function tablePositionOptions($value)
    {
        $this->Result['Position'] = $value;
    }

    public function tableButton($key, $option, $value)
    {
        $this->Result['Buttons'][$key][$option] = $value;
    }

    public function excelButton($value)
    {
        $this->Result['ExcelButton'] = $value;
    }

    public function excelExceptions($key, $value)
    {
        $this->Result['ExcelExceptions'][$key] = $value;
    }

    public function excelDateFormat($key, $format)
    {
        $this->Result['ExcelFormat'][$key]['format'] = $format;
        $this->Result['ExcelFormat'][$key]['type'] = 'date';
    }

    public function defaultSortableItem($key, $value)
    {
        $this->Result['DefaultSort'][$key] = $value;
    }

    public function makeExcel($query)
    {
        return $query;
    }
    public function getDatatableStruct()
    {
        return $this->Result;
    }

    public function addQuery($query)
    {
        $this->Result['Query'] = Crypt::encrypt(Eloquent::serialize($query));
    }

}
