<?php

namespace App\Filament\Admin\Resources\LoanPredictionResource\Pages;

use App\Filament\Admin\Resources\LoanPredictionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanPredictions extends ListRecords
{
    protected static string $resource = LoanPredictionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
