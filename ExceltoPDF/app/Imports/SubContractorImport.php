<?php

namespace App\Imports;

use App\Models\SubContractor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubContractorImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    protected $contractorId;
    protected $chunk;

    public function headingRow(): int{
        return 4;
    }

    public function __construct($contractorId, $chunk = 200)
    {
        $this->contractorId = $contractorId;
        $this->chunk = $chunk;
    }

    public function collection(Collection $rows)
    {
        $batch = [];

        foreach ($rows as $row) {
            if ($row->filter()->isEmpty()) continue;
            // Log::info('row_content: ' . $row);

            $batch[] = [
                'contractor_id'        => $this->contractorId,
                'forename'             => $row['forename'] ?? null,
                'surname'              => $row['surname'] ?? null,
                'utr'                  => $row['sub_contractor_utr'] ?? null,
                'verification_number'  => $row['verification_number'] ?? null,
                'deduction_liability'  => round($row['deduction_liability']) ?? 0,
                'total_payment'        => round($row['total_payments']) ?? 0,
                'cost_of_materials'    => round($row['cost_of_materials']) ?? 0,
                'total_deducted'       => $row['total_deducted'] ?? 0,
                'people_id'            => $row['people_id'] ?? null,
                'unique_id'            => $this->generateUniqueId(),
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        // Insert in chunks
        foreach (array_chunk($batch, $this->chunk) as $chunk) {
            SubContractor::insert($chunk);
        }
    }

    public function chunkSize(): int
    {
        return $this->chunk;
    }

    protected function generateUniqueId()
    {
        return strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 7));
    }
}
