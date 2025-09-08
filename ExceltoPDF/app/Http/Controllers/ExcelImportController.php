<?php

namespace App\Http\Controllers;

use App\Imports\DataRowsImport;
use App\Imports\HeaderRowsImport;
use App\Imports\SubContractorImport;
use App\Models\Contractor;
use App\Models\PdfQueue;
use App\Models\SubContractor;
use Carbon\Carbon;
use Carbon\Traits\Timestamp;
use Date;
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
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('import_file');
        $size = $file->getSize(); // in bytes

        if ($size <= 500_000) { // small file ~500KB
            $chunk = 300;
        } elseif ($size <= 5_000_000) { // ~5MB
            $chunk = 4000;
        } else {
            $chunk = 4000;
        }


        // 🔹 Load all rows (raw values)
        $rows = Excel::toCollection(null, $file)->first();

        if ($rows->isEmpty()) {
            return response()->json(['message' => 'Empty file'], 422);
        }

        // ✅ Contractor headers (row 1)
        $contractorHeaders = $rows[0]->toArray();

        // ✅ Contractor data (row 2)
        $contractorData = $rows[1]->toArray();

        // 🔹 Combine headers + values
        $contractorRow = array_combine($contractorHeaders, $contractorData);
       log::info('contractor row' . json_encode($contractorRow));
        // ✅ Save contractor
        $contractor = Contractor::create([
            'contractor_name' => $contractorRow['contractor name'] ?? null,
            'employer_tax_reference' => $contractorRow['Contractor UTR'] ?? null,
            'address_line1' => $contractorRow['Address line 1'] ?? null,
            'address_line2' => $contractorRow['Address line 2'] ?? null,
            'address_line3' => $contractorRow['Address line 3'] ?? null,
            'period_end' => $this->formatDate($contractorRow['Period End'] ?? null),
            'email' => $contractorRow['Email Address'] ?? null,
        ]);

        // ✅ SubContractors start at row 4
        Excel::import(new SubContractorImport($contractor->contractor_id, $chunk), $file);

        $subcontractors = SubContractor::where('contractor_id', $contractor->contractor_id)
            ->paginate(10);

        log::info($subcontractors);
        $html = view('ImportFileDetails', ['subcontractors' => $subcontractors])->render();

        return response()->json(['html' => $html]);
    }
    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }
        
        // date input as timestamp
        if (is_numeric($date)) {
            $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($date);
            return $carbonDate->format('Y-m-d');
        }

        try {
            // If Excel exports as "3/12/2023" (d/m/Y), convert to Y-m-d
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            // Try m/d/Y in case Excel used US format
            try {
                return Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null; // Fallback to null
            }
        }
    }


 
    public function download(Request $request)
    {
        $uniqueId = $request->input('unique_id');

        if (!$uniqueId) {
            Log::error('Download request missing unique_id', ['request' => $request->all()]);
            abort(400, 'unique_id is required.');
        }

        // Fetch Subcontractor with Contractor info
        $row = SubContractor::select(
            'sub_contractors.*',
            'contractors.contractor_name',
            'contractors.employer_tax_reference',
            'contractors.address_line1',
            'contractors.address_line2',
            'contractors.address_line3',
            'contractors.period_end'
        )
            ->join('contractors', 'contractors.contractor_id', '=', 'sub_contractors.contractor_id')
            ->where('sub_contractors.unique_id', $uniqueId)
            ->first();

        if (!$row) {
            Log::error('No subcontractor found for unique_id', ['unique_id' => $uniqueId]);
            abort(404, 'Subcontractor not found.');
        }

        // Format all date fields to d/m/Y for Blade view
        $rowArray = $row->toArray();
        $rowArray['period_end'] = $row->period_end ? date('d/m/Y', strtotime($row->period_end)) : null;
        $rowArray['created_at'] = $row->created_at ? date('d/m/Y', strtotime($row->created_at)) : null;
        $rowArray['updated_at'] = $row->updated_at ? date('d/m/Y', strtotime($row->updated_at)) : null;

        Log::info('Generating PDF', $rowArray);

        $pdf = PDF::loadView('CISStatement', $rowArray);

        $filename = 'CISStatement_' . ($row->forename . '_' . $row->surname ?? 'Unknown') . '.pdf';

        return $pdf->download($filename);
    }

    public function zipDownload(Request $request)
    {
        Log::info("request". $request);
        dd($request);
        $row_data = json_decode($request->row_data);
        $header_data = json_decode($request->header_data);
        Log::info('row_data'. $row_data);
        Log::info('header_data' . $header_data);

        $zipFileName = 'CISStatement_' . time() . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Could not create ZIP file.');
        }
        foreach ($row_data[0] as $index => $data) {
            $payload = array_merge((array) $data, (array) $header_data[0]);
            log::info('index: ' . $index);
            log::info('payload' . json_encode($payload));
            $pdf = PDF::loadView('CISStatement', $payload);
            $pdfContent = $pdf->output();
            $filename = 'CISStatement_' . ($payload['forename'] . $payload['surname'] . time() . $index ?? 'Unknown') . '.pdf';
            // return $pdf->download($filename);
            $zip->addFromString($filename, $pdfContent);
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    public function show()
    {
        // Example dummy data – replace with DB query later
        $contractor = [
            'contractor_name' => 'Swift Ltd',
            'utr' => '2325648152',
            'address_line1' => '10',
            'address_line2' => 'uyaysd',
            'address_line3' => 'uyagsdahsd',
            'period_end' => '2023-12-03',
            'email' => 'swift@example.com',
        ];

        $subContractor = [
            'title' => 'Mr',
            'forename' => 'John',
            'surname' => 'Smith',
            'sub_contractor_utr' => '1234567890',
            'verification_number' => 'V123456789/AB',
        ];

        return view('CISStatement', [
            // Contractor
            'period_end' => $contractor['period_end'],
            'aoref' => '123AB45678',   // Employer tax ref example
            'works_ref' => 'Project XYZ',

            // Subcontractor
            'title' => $subContractor['title'],
            'forename' => $subContractor['forename'],
            'surname' => $subContractor['surname'],
            'sub_contractor_utr' => $subContractor['sub_contractor_utr'],
            'verification_number' => $subContractor['verification_number'],

            // Amounts
            'total_payments' => 1000.00,
            'cost_of_materials' => 200.00,
            'liable_amount' => 800.00,
            'total_deducted' => 160.00,
        ]);
    }

    public function queuePdf(Request $request)
    {
        // validate input
        $request->validate([
            'contractor_id' => 'required',
        ]);

        $contractorId = $request->input('contractor_id');

       

            PdfQueue::create([
                'contractor_id' => $contractorId,
                'processed'     => false,
            ]);
        

        return response()->json([
            'success' => true,
            'message' => 'Contractor added to PDF queue successfully.'
        ]);
    }


    public function list(Request $request)
{
    $contractorId = $request->get('contractor_id');
    log::info($contractorId);
    $subcontractors = SubContractor::where('contractor_id', $contractorId)
        ->paginate(10);

    // Render partial
    log::info($subcontractors);
    $html = view('ImportFileDetails',['subcontractors'=>$subcontractors])->render();

    return response()->json(['html' => $html]);
}


 

}
