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


    public function PdfDownload()
    {
        $row = [
            'contractor_name' => 'ABC Construction Ltd',
            'contractor_address' => '12 King Street, London, UK',
            'employer_tax_ref' => '123/AB456',
            'subcontractor_name' => 'John Smith',
            'utr' => '1234567890',
            'verification_no' => 'V1234567',
            'tax_month_end' => '31 May 2025',
            'gross_amount' => 5000.10,
            'material_cost' => 1200.23,
            'liable_amount' => 3800.00,
            'deducted_amount' => 760.45,
            'payable_amount' => 4240.00,
        ];

        $pdf = PDF::loadView('CISStatement', $row);
        return $pdf->download('CIS_Statement_John_Smith.pdf');
    }
    public function download(Request $request)
    {
        dd($request);
        $row = $request->input('row');
        log::info('uahsudhwa');
        log::info($row);
        $row = $this->normalizeRowKeys($row);


        $pdf = PDF::loadView('CISStatement', $row);



        return $pdf->download('CISStatement_' . $row['contractor_name'] . '.pdf');
    }


    public function normalizeRowKeys(array $row): array
    {
        // Canonical keys you want in your system
        $expectedKeys = [
            'contractor_name',
            'contractor_address',
            'employer_tax_ref',
            'subcontractor_name',
            'utr',
            'verification_no',
            'tax_month_end',
            'gross_amount',
            'material_cost',
            'liable_amount',
            'deducted_amount',
            'payable_amount',
        ];

        $normalized = array_fill_keys($expectedKeys, null);

        foreach ($row as $rawKey => $value) {
            $bestKey = null;
            $bestScore = -1;

            foreach ($expectedKeys as $expected) {
                similar_text(
                    strtolower(preg_replace('/[^a-z0-9]/', '', $expected)),
                    strtolower(preg_replace('/[^a-z0-9]/', '', $rawKey)),
                    $percent
                );

                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestKey = $expected;
                }
            }

            // Save if similarity good enough
            if ($bestScore >= 40 && $bestKey !== null) {
                $normalized[$bestKey] = $value;
            }
        }

        return $normalized;
    }

}
