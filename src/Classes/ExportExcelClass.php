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
    private $collectionHeadings;
    private $collection_formats;
    private $exceptions;


    public function __construct($type, $collection_to_export, $collection_headings = null, $exceptions = null, $collection_format = null)
    {
        $this->type = $type;
        $this->collection = $collection_to_export;
        $this->headings = $collection_headings;
        $this->collection_formats = $collection_format;
        $this->exceptions = $exceptions;

        $this->getExportedCollection();
    }


    public function getExportedCollection()
    {
        $collectionKeys = array_keys($this->headings);


        $newCollectionItem = [];


        foreach ($this->collection as $collection) {

            foreach ($this->headings as $itemKey => $itemValue) {

                if (Str::contains($itemKey, '.')) {
                    $cols = Str::of($itemKey)->explode('.');

                    $colnew = $collection;
                    foreach ($cols as $col) {
                        if ($colnew !== null) {
                            $colnew = $colnew->$col;
                        }
                    }

                    $result[$itemKey] = $colnew;

                } elseif ($itemValue['type'] == 'date') {
                    $result[$itemKey] = optional($collection->$itemKey)->format($itemValue['format']);
                } else {
                    $result[$itemKey] = $collection->$itemKey;
                }
            }

            $newCollectionItem[] = $result;
        }

        $this->headings = $collectionKeys;
        $this->collection = collect($newCollectionItem);
    }


    public function collection()
    {
        return $this->collection;
    }


    public function headings(): array
    {
        return $this->headings;
    }
}
