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
        // Validate same month/year
        if (date('Y-m', strtotime($data['loan_date'])) !== date('Y-m', strtotime($data['payday']))) {
            Notification::make()
                ->title('Tanggal pinjaman dan bayar harus dalam bulan yang sama.')
                ->danger()
                ->send();

            $this->halt(); // prevent saving
        }

        // Send to FastAPI
        $service = new LoanPredictionService();
        $prediction = $service->predictLoan($data);

        if (isset($prediction['error'])) {
            Notification::make()
                ->title('Gagal Prediksi: ' . $prediction['error'])
                ->danger()
                ->send();

            $this->halt();
        }

        // Append prediction results
        return array_merge($data, [
            'predicted_kemampuan_bayar' => $prediction['predicted_kemampuan_bayar'] ?? false,
            'total_pemberian_pinjaman' => $prediction['total_pemberian_pinjaman'] ?? null,
            'lama_pinjaman' => $prediction['lama_pinjaman'] ?? null,
        ]);
    }
}
