<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImport;
use App\Imports\HeaderRows;
use Illuminate\Http\Request;
use App\Imports\ExcelImport as ExcelImports;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as Excels;
use Barryvdh\DomPDF\facade\Pdf as PDF;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);
        $file = $request->file('import_file');

        $dataRows = Excel::toArray(new ExcelImport, $file);
        $headerRows = Excel::toArray(new HeaderRows, $file);
        $rows[] = [
            'data' => $dataRows,
            'header' => array_slice($headerRows[0], 0, 1)
        ];
        log::info(json_encode($rows));
        $html = view('ImportFileDetails', ['rows' => $rows])->render();

        return response()->json(['html' => $html]);
    }


   
    public function download(Request $request)
    {
        
        $payload= $request->input('payload') ;
$row=json_decode($payload,true);

        if (!$row) {
            Log::error('PDF payload is null or invalid', ['payload' => $request->input('payload')]);
            abort(400, 'Invalid PDF data.');
        }

        Log::info('Generating PDF', $row);

        $pdf = PDF::loadView('CISStatement',  $row);

        $filename = 'CISStatement_' . ($row['contractor_name'] ?? 'Unknown') . '.pdf';

        return $pdf->download($filename);
    }


}
