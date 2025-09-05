<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubContractor;
use Barryvdh\DomPDF\Facade\Pdf;
use log;
use Illuminate\Support\Facades\File;

class GenerateCISPDF extends Command
{
    protected $signature = 'cis:generate-pdf';
    protected $description = 'Generate CIS PDF for queued contractors';

    public function handle()
    {
        // Pick all contractors in queue that are not processed
        $queue = \DB::table('pdf_queue')->where('processed', 0)->get();
        // log::info($queue);

        foreach ($queue as $item) {
            $contractorId = $item->contractor_id;

            $subcontractors = SubContractor::select(
            'sub_contractors.*',
            'contractors.contractor_name',
            'contractors.employer_tax_reference',
            'contractors.address_line1',
            'contractors.address_line2',
            'contractors.address_line3',
            'contractors.period_end'
        )
            ->join('contractors', 'contractors.contractor_id', '=', 'sub_contractors.contractor_id')
            ->where('sub_contractors.contractor_id', $contractorId)
            ->get();

            if ($subcontractors->isEmpty()) continue;

            foreach ($subcontractors as $sub) {
                $data = $sub->toArray();

                $folder = "D:/CIS statement";
                if (!File::exists($folder)) {
                    File::makeDirectory($folder, 0755, true);
                }

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('CISStatement', $data);

                $filename = $folder . '/CIS_' . $sub->forename . '_' . $sub->surname . '.pdf';
                $pdf->save($filename);

                $sub->update(['is_pdf_generated' => 1]);
            }

            // Mark queue as processed
            \DB::table('pdf_queue')->where('id', $item->id)->update(['processed' => 1]);
        }

        $this->info('PDF generation completed.');
    }
}
