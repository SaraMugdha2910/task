<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public $rows;
    public function collection(Collection $collections)
    {
        $this->rows = $collections;
        foreach($collections as $collection){
            Log::info('Row data:', $collection->toArray());
        }
    }
}
