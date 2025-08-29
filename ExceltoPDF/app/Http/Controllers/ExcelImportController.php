<?php

namespace App\Http\Controllers;

use App\Imports\DataRowsImport;
use App\Imports\HeaderRowsImport;
use Carbon\Traits\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\facade\Pdf as PDF;
use ZipArchive;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);
        $file = $request->file('import_file');

        $dataRows = Excel::toArray(new DataRowsImport, $file);
        $headerRows = Excel::toArray(new HeaderRowsImport, $file);
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
        $payload = $request->input('payload');
        $row = json_decode($payload, true);

        if (!$row) {
            Log::error('PDF payload is null or invalid', ['payload' => $request->input('payload')]);
            abort(400, 'Invalid PDF data.');
        }

        Log::info('Generating PDF', $row);

        $pdf = PDF::loadView('CISStatement',  $row);

        $filename = 'CISStatement_' . ($row['forename'].$row['surname'] ?? 'Unknown') . '.pdf';

        return $pdf->download($filename);
    }

    public function zipDownload(Request $request){
        $row_data = json_decode($request->row_data);
        $header_data = json_decode($request->header_data);

        $zipFileName = 'CISStatement_'.time().'.zip';
        $zipPath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Could not create ZIP file.');
        }
        foreach($row_data[0] as $index=>$data){
            $payload = array_merge((array)$data, (array)$header_data[0]);
            log::info('index: '.$index);
            log::info('payload'.json_encode($payload));
            $pdf = PDF::loadView('CISStatement',  $payload);
            $pdfContent = $pdf->output();
            $filename = 'CISStatement_' . ($payload['forename'].$payload['surname'] ?? 'Unknown') . '.pdf';
            // return $pdf->download($filename);
            $zip->addFromString($filename, $pdfContent);
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
