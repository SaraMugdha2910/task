<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ExcelImport as ExcelImports;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as Excels;

class ExcelImportController extends Controller
{
    public function import(Request $request){
        
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);
        // dd($request);
        $file = $request->file('import_file');

        Log::info('Uploaded file:', [
            'originalName' => $file->getClientOriginalName(),
            'extension'    => $file->getClientOriginalExtension(),
            'mimeType'     => $file->getMimeType(),
            'size'         => $file->getSize()
        ]);

        $extension = $request->file('import_file')->getClientOriginalExtension();
        $readerType = match (strtolower($extension)){
            'csv' => Excels::CSV,
            'xls' => Excels::XLS,
            'xlsx' => Excels::XLSX,
            default => Excels::CSV,
        };


        $import = new ExcelImports();
        Excel::import($import, $request->file('import_file'), null, $readerType);
    }
}
