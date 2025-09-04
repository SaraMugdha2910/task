<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    // Table name (if different from plural form)
    protected $table = 'contractors';

    // Primary key
    protected $primaryKey = 'contractor_id';

    // Auto-incrementing & key type
    public $incrementing = true;
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = true; // because you have created_at & updated_at

    // Mass assignable attributes
    protected $fillable = [
        'contractor_name',
        'employer_tax_reference',
        'address_line1',
        'address_line2',
        'address_line3',
        'period_end',
    ];

    // Dates casting
    protected $dates = [
        'period_end',
        'created_at',
        'updated_at',
    ];
}
