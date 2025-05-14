<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoanPredictionService
{
        public function predictLoan(array $data)
    {
        try {
            $response = Http::post('http://fastapi_app:8000/predict', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API request failed', ['response' => $response->body()]);

            return ['error' => 'API request failed: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('API request exception', ['error' => $e->getMessage()]);
            return ['error' => 'API request failed: ' . $e->getMessage()];
        }
    }

}
