<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToArray, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function headingRow(){
        return 4;
    }
    public function array(array $rows)
    {
        return $rows;
    }
}
