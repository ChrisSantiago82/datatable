<?php


namespace Chrissantiago82\Datatable\Classes;


use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportExcelClass implements FromCollection, WithHeadings
{
    private $type;
    private $collection;
    private $headings;

    public function __construct($type, $collection_to_export, $collection_headings = null) {
        
        $this->type = $type;
        $this->collection = $collection_to_export;
        $this->headings = $collection_headings;
    }

    public function collection()
    {
        if($this->type == 'full_collection') {
            return $this->collection;

        }elseif($this->type == 'merged_collection')
        {
            $subCollection = $this->collection->map(function ($user) {
                $result = [];

                foreach ($this->headings as $itemKey => $itemValue) {
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

    }

    public function headings(): array
    {
        if($this->type == 'full_collection')
        {
            return array_keys($this->collection->first()->toArray());

        }elseif ($this->type == 'merged_collection') {


            $headingsResult = [];
            foreach ($this->headings as $items) {
                if ($items['columnName'] !== null) {
                    $headingsResult[] = $items['columnName'];
                }
            }

            return $headingsResult;
        }
    }
}
