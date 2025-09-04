<?php

namespace App\Imports;

use App\Models\SubContractor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SubContractorImport implements ToCollection, WithChunkReading
{
    protected $contractorId;
    protected $chunk;

    public function __construct($contractorId, $chunk = 200)
    {
        $this->contractorId = $contractorId;
        $this->chunk = $chunk;
    }

    public function collection(Collection $rows)
    {
        if ($rows->count() < 5) return;

        // 🔹 Row 4 = headers
        $headers = array_map('strtolower', $rows[3]->toArray());

        $batch = [];

        // 🔹 Rows after row 4 = data
        foreach ($rows->skip(4) as $row) {
            if ($row->filter()->isEmpty()) continue;

            $data = array_combine($headers, $row->toArray());

            $batch[] = [
                'contractor_id'        => $this->contractorId,
                'forename'             => $data['forename'] ?? null,
                'surname'              => $data['surname'] ?? null,
                'utr'                  => $data['sub-contractor utr'] ?? null,
                'verification_number'  => $data['verification number'] ?? null,
                'deduction_liability'  => $data['deduction liability'] ?? 0,
                'total_payment'        => $data['total payments'] ?? 0,
                'cost_of_materials'    => $data['cost of materials'] ?? 0,
                'total_deducted'       => $data['total deducted'] ?? 0,
                'unique_id'            => $this->generateUniqueId(),
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        // 🔹 Insert in chunks
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
