<?php

namespace App\Filament\Admin\Resources\LoanPredictionResource\Pages;

use App\Filament\Admin\Resources\LoanPredictionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanPrediction extends EditRecord
{
    protected static string $resource = LoanPredictionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
