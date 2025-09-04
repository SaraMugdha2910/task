<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubContractor extends Model
{
    use HasFactory;

    protected $table = 'sub_contractors';
    protected $primaryKey = 'sub_contractor_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'contractor_id',
        'unique_id',
        'forename',
        'surname',
        'utr',
        'verification_number',
        'total_payment',
        'cost_of_materials',
        'total_deducted',
        'deduction_liability',
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
        'cost_of_materials' => 'decimal:2',
        'total_deducted' => 'decimal:2',
        'deduction_liability' => 'decimal:2',
        'amount_payable' => 'decimal:2',
    ];

    // Accessor (so it's always available in JSON)
    protected $appends = ['amount_payable'];

    public function getAmountPayableAttribute()
    {
        // If DB already calculated, use it
        if (array_key_exists('amount_payable', $this->attributes)) {
            return $this->attributes['amount_payable'];
        }

        // Fallback (in case DB didn’t calculate yet)
        return ($this->total_payment ?? 0) - ($this->total_deducted ?? 0);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id', 'contractor_id');
    }

    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->unique_id)) {
                $model->unique_id = self::generateUniqueId();
            }
        });
    }

    /**
     * Generate a random 7-digit alphanumeric string
     */
    private static function generateUniqueId(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = '';
        for ($i = 0; $i < 7; $i++) {
            $id .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Ensure uniqueness in DB
        while (self::where('unique_id', $id)->exists()) {
            $id = '';
            for ($i = 0; $i < 5; $i++) {
                $id .= $characters[random_int(0, strlen($characters) - 1)];
            }
        }

        return $id;
    }

}
