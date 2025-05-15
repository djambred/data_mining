<?php

namespace App\Filament\Admin\Resources\LoanPredictionResource\Pages;

use App\Filament\Admin\Resources\LoanPredictionResource;
use App\Services\LoanPredictionService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanPrediction extends CreateRecord
{
    protected static string $resource = LoanPredictionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure loan_date exists — default to now() if missing
        $loanDate = data_get($data, 'loan_date', now()->toDateString());
        $payday = data_get($data, 'payday');

        // Store loan_date back into $data (especially if it's missing due to being disabled)
        $data['loan_date'] = $loanDate;

        // Validate both dates exist before comparing
        if ($loanDate && $payday) {
            if (date('Y-m', strtotime($loanDate)) !== date('Y-m', strtotime($payday))) {
                Notification::make()
                    ->title('Tanggal pinjaman dan bayar harus dalam bulan yang sama.')
                    ->danger()
                    ->send();

                $this->halt(); // prevent saving
            }
        }

        // Send to FastAPI
        $service = new LoanPredictionService();
        $prediction = $service->predictLoan($data);

        if (isset($prediction['error'])) {
            Notification::make()
                ->title('Gagal Prediksi: ' . $prediction['error'])
                ->danger()
                ->send();

            $this->halt(); // prevent saving
        }

        // Append prediction results
        return array_merge($data, [
            'predicted_kemampuan_bayar' => $prediction['predicted_kemampuan_bayar'] ?? false,
            'total_pemberian_pinjaman' => $prediction['total_pemberian_pinjaman'] ?? null,
            'lama_pinjaman' => $prediction['lama_pinjaman'] ?? null,
        ]);
    }
}
