<?php


namespace Chrissantiago82\Datatable\Classes;


use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportExcelClass implements FromCollection, WithHeadings
{
    private $collection;
    private $headings;

    public function __construct($collection_to_export, $collection_headings) {
        $this->collection = $collection_to_export;
        $this->headings = $collection_headings;
    }

    public function collection()
    {
        $subCollection = $this->collection->map(function ($user) {
            $result = [];

            foreach ($this->headings as $itemKey => $itemValue)
            {
                $collection = Str::of($itemKey)->explode('.');
                $val = $user;

                foreach ($collection as $rel) {
                    if ($val === null) {
                        break;
                    }
                    $val = optional($val)->$rel;
                    $result[$itemKey] = $val;
                }

            }
            return collect($result)
                ->all();
        });

        return $subCollection;

    }

    public function headings(): array
    {
        $headingsResult = [];
        foreach ($this->headings as $items)
        {
            if($items['columnName'] !== null)
            {
                $headingsResult[] = $items['columnName'];
            }
        }

        return $headingsResult;
    }
}
