<?php

namespace App\Http\Controllers;

use App\Models\LoanPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoanPredictionController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:18',
            'salary' => 'required|numeric|min:0',
            'loan_amount' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'payday' => 'required|date|after_or_equal:loan_date',
        ]);

        try {
            // Send POST request to FastAPI
            $response = Http::post('http://fastapi_app:8000/predict', $validated);

            // Check response status
            if (!$response->ok()) {
                logger()->error('FastAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'message' => 'Gagal melakukan prediksi',
                    'error' => $response->json()
                ], 500);
            }

            $result = $response->json();

            // Log FastAPI result
            logger()->info('FastAPI prediction result', $result);

            // Validate keys from response
            if (
                !isset($result['predicted_kemampuan_bayar']) ||
                !isset($result['total_pemberian_pinjaman']) ||
                !isset($result['lama_pinjaman'])
            ) {
                logger()->error('FastAPI response missing expected keys', $result);

                return response()->json([
                    'message' => 'Response tidak lengkap dari model prediksi',
                    'data' => $result
                ], 422);
            }

            // Save prediction result
            $prediction = LoanPrediction::create([
                ...$validated,
                'predicted_kemampuan_bayar' => $result['predicted_kemampuan_bayar'],
                'total_pemberian_pinjaman' => $result['total_pemberian_pinjaman'],
                'lama_pinjaman' => $result['lama_pinjaman'],
            ]);

            return response()->json([
                'message' => 'Prediksi berhasil disimpan',
                'data' => $prediction
            ], 201);

        } catch (\Exception $e) {
            logger()->error('Exception during prediction', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses prediksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
