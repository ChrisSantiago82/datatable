<?php


namespace Chrissantiago82\Datatable\Classes;


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

    public function tableButton($key, $option, $value)
    {
        $this->Result['Buttons'][$key][$option] = $value;
    }

    public function tableFilter($key, $value)
    {
        $this->Result['Filters'][$key] = $value;
    }

    public function excelButton($value)
    {
        $this->Result['ExcelButton'] = $value;
    }

    public function excelExceptions($key, $value)
    {
        $this->Result['ExcelExceptions'][$key] = $value;
    }

    public function makeExcel($query)
    {
        return $query;
    }
    public function getDatatableStruct()
    {
        return $this->Result;
    }

}
