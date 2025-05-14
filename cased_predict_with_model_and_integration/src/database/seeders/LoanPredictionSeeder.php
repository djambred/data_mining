<?php

namespace Database\Seeders;

use App\Models\LoanPrediction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanPrediction::create([
            'name' => 'Test User',
            'age' => 25,
            'salary' => 5000000,
            'loan_amount' => 7000000,
            'loan_date' => '2025-06-01',
            'payday' => '2025-06-28',
            'predicted_kemampuan_bayar' => true,
            'total_pemberian_pinjaman' => 6500000,
            'lama_pinjaman' => 6,
        ]);
    }
}
