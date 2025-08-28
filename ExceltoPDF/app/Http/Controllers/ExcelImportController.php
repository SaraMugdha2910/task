<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ExcelImport as ExcelImports;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as Excels;
use Barryvdh\DomPDF\facade\Pdf as PDF;
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
        if ($import->rows) {
            Log::info('All rows:', $import->rows->toArray());
        }
        $rows =  $import->rows ? $import->rows->toArray() : [];
        return view('ImportFileDetails', compact('rows'));
    }


    public function PdfDownload()
{
    $row = [
        'contractor_name'   => 'ABC Construction Ltd',
        'contractor_address'=> '12 King Street, London, UK',
        'employer_tax_ref'  => '123/AB456',
        'subcontractor_name'=> 'John Smith',
        'utr'               => '1234567890',
        'verification_no'   => 'V1234567',
        'tax_month_end'     => '31 May 2025',
        'gross_amount'      => 5000.10,
        'material_cost'     => 1200.23,
        'liable_amount'     => 3800.00,
        'deducted_amount'   => 760.45,
        'payable_amount'    => 4240.00,
    ];
 
    $pdf = PDF::loadView('CISStatement', $row);
    return $pdf->download('CIS_Statement_John_Smith.pdf');
}
}
