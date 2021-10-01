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

    public function __construct($type, $collection_to_export, $collection_headings = null, $exceptions = null, $collection_format = null) {
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

        $subCollection = $this->collection->map(function ($collectionItem, $collectionKey) use ($collectionKeys) {
            $result = [];

            foreach ($this->headings as $itemKey => $itemValue) {
                $collection = Str::of($itemKey)->explode('.');
                $val = $collectionItem;

                foreach ($collection as $rel) {
                    if ($val === null) {
                        break;
                    }
                    $val = optional($val)->$rel;

                    if($itemValue['type'] == 'date')
                    {
                        $result[$itemKey] = $val->format($itemValue['format']);
                    }else{
                        $result[$itemKey] = $val;
                    }
                }

            }
            $collectionResult = collect($result);

            $newCollectionItem = collect($collectionItem);

            foreach ($this->collection_formats as $keyFormat => $value)
            {
                if($value['type'] == 'date')
                {
                    $newCollectionItem[$keyFormat] = $collectionItem->$keyFormat->format($value['format']);
                }
            }

            if($this->type == 'full_collection') {
                $exceptions = array_keys($this->exceptions);

                $mergeCollection = collect($newCollectionItem)->except($collectionKeys)->except($exceptions);

                $fullCollection = $collectionResult->merge($mergeCollection);
                $this->collectionHeadings = $fullCollection->keys()->toArray();


                return $fullCollection->all();

            }
            elseif($this->type == 'merged_collection')
            {
                return $collectionResult->all();
            }

        });

        $this->headings = $this->collectionHeadings;
        $this->collection = $subCollection;
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
