<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HeaderRows implements ToArray, WithHeadingRow
{
    public function headingRow(){
        return 1;
    }
   public function array(array $array)
    {
        return $array;
    }
}
