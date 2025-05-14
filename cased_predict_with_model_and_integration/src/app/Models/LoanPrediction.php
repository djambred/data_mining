<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPrediction extends Model
{
    protected $table = 'loan_predictions';

    protected $fillable = [
        'name',
        'age',
        'salary',
        'loan_amount',
        'loan_date',
        'payday',
        'predicted_kemampuan_bayar',
        'total_pemberian_pinjaman',
        'lama_pinjaman',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'payday' => 'date',
        'predicted_kemampuan_bayar' => 'boolean',
        'salary' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'total_pemberian_pinjaman' => 'decimal:2',
    ];
}
