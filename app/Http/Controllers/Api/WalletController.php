<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Whmcs\CreditWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected CreditWalletService $walletService
    ) {}

    /**
     * Get wallet balance
     */
    public function balance(Request $request): JsonResponse
    {
        try {
            $balance = $this->walletService->getBalance($request->user()->id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => $balance,
                    'formatted' => number_format($balance, 2, ',', '.'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar saldo',
            ], 500);
        }
    }

    /**
     * Get transaction history
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 50);
            $history = $this->walletService->getHistory($request->user()->id, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar histórico',
            ], 500);
        }
    }
}
