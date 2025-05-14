<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LoanPredictionResource\Pages;
use App\Services\LoanPredictionService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\LoanPrediction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Log;  // Import Log facade for debugging

class LoanPredictionResource extends Resource
{
    protected static ?string $model = LoanPrediction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Dependency Injection is handled in controller/service
    protected static LoanPredictionService $predictionService;

    // Static method to call LoanPredictionService
    public static function setPredictionService(LoanPredictionService $service)
    {
        self::$predictionService = $service;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Nama'),
                TextInput::make('age')->label('Usia'),
                TextInput::make('salary')->label('Gaji')->prefix('Rp'),
                TextInput::make('loan_amount')->label('Jumlah Pinjaman')->prefix('Rp'),
                DatePicker::make('payday')->label('Tanggal Gajian'),
                DatePicker::make('loan_date')->label('Tanggal Pinjam')->default(now())->disabled(),
                Toggle::make('predicted_kemampuan_bayar')
                    ->label('Kemampuan Bayar')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),
                TextInput::make('total_pemberian_pinjaman')->label('Total Disetujui')->prefix('Rp')->disabled(),
                TextInput::make('lama_pinjaman')->label('Lama Pinjaman (bulan)')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('age')->label('Usia'),
                TextColumn::make('salary')->money('IDR')->label('Gaji'),
                TextColumn::make('loan_amount')->money('IDR')->label('Jumlah Pinjaman'),
                TextColumn::make('payday')->label('Tanggal Gajian'),
                TextColumn::make('loan_date')->label('Tanggal Pinjam'),
                IconColumn::make('predicted_kemampuan_bayar')
                    ->label('Mampu Bayar')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('total_pemberian_pinjaman')->money('IDR')->label('Total Disetujui'),
                TextColumn::make('lama_pinjaman')->label('Durasi (bulan)'),
                TextColumn::make('created_at')->label('Waktu Prediksi')->since(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanPredictions::route('/'),
            'create' => Pages\CreateLoanPrediction::route('/create'),
            'edit' => Pages\EditLoanPrediction::route('/{record}/edit'),
        ];
    }

    // Hook before save to call the prediction service
    public static function beforeSave(array $data): array
    {
        // Debug: Check if $data is being passed correctly
        Log::debug('beforeSave data:', $data);

        // Check if prediction service is available
        if (!self::$predictionService) {
            Log::debug('Prediction service is not set.');
            return $data; // Proceed without prediction
        }

        // Call prediction service
        $predictionResult = self::$predictionService->predictLoan($data);

        // Debug: Log the prediction result
        Log::debug('Prediction Result:', $predictionResult);

        // If the prediction result is valid, modify the data
        if (isset($predictionResult['predicted_kemampuan_bayar'])) {
            $data['predicted_kemampuan_bayar'] = $predictionResult['predicted_kemampuan_bayar'];
            $data['total_pemberian_pinjaman'] = $predictionResult['total_pemberian_pinjaman'];
            $data['lama_pinjaman'] = $predictionResult['lama_pinjaman'];
        } else {
            Log::debug('Prediction result is invalid or missing fields.');
        }

        return $data;
    }
}
