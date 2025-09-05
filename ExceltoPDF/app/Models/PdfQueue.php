<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfQueue extends Model
{
    use HasFactory;

    protected $table = 'pdf_queue';

    protected $primaryKey = 'id';

    protected $fillable = [
        'contractor_id',
        'processed',
    ];

    // Define relationship with Contractor
    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id', 'contractor_id');
    }
}
