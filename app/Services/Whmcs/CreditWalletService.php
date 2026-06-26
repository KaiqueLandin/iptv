<?php

namespace App\Services\Whmcs;

use App\Models\CreditTransaction;
use App\Models\CreditWallet;
use Illuminate\Support\Facades\DB;

class CreditWalletService
{
    /**
     * Get or create wallet for user
     */
    public function getOrCreateWallet(int $userId): CreditWallet
    {
        return CreditWallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0]
        );
    }

    /**
     * Credit amount to wallet
     */
    public function credit(
        int $userId,
        float $amount,
        string $description,
        ?int $whmcsOrderId = null,
        ?int $whmcsServiceId = null,
        array $metadata = []
    ): CreditTransaction {
        return DB::transaction(function () use ($userId, $amount, $description, $whmcsOrderId, $whmcsServiceId, $metadata) {
            $wallet = $this->getOrCreateWallet($userId);

            $newBalance = $wallet->balance + $amount;
            $wallet->update(['balance' => $newBalance]);

            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'whmcs_order_id' => $whmcsOrderId,
                'whmcs_service_id' => $whmcsServiceId,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Debit amount from wallet
     */
    public function debit(
        int $userId,
        float $amount,
        string $description,
        array $metadata = []
    ): CreditTransaction {
        return DB::transaction(function () use ($userId, $amount, $description, $metadata) {
            $wallet = $this->getOrCreateWallet($userId);

            if ($wallet->balance < $amount) {
                throw new \Exception('Saldo insuficiente');
            }

            $newBalance = $wallet->balance - $amount;
            $wallet->update(['balance' => $newBalance]);

            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Refund amount to wallet
     */
    public function refund(
        int $userId,
        float $amount,
        string $description,
        ?int $whmcsOrderId = null,
        array $metadata = []
    ): CreditTransaction {
        return DB::transaction(function () use ($userId, $amount, $description, $whmcsOrderId, $metadata) {
            $wallet = $this->getOrCreateWallet($userId);

            $newBalance = $wallet->balance + $amount;
            $wallet->update(['balance' => $newBalance]);

            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'whmcs_order_id' => $whmcsOrderId,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Manual adjustment (admin only)
     */
    public function adjust(
        int $userId,
        float $amount,
        string $description,
        array $metadata = []
    ): CreditTransaction {
        return DB::transaction(function () use ($userId, $amount, $description, $metadata) {
            $wallet = $this->getOrCreateWallet($userId);

            $newBalance = $wallet->balance + $amount;
            $wallet->update(['balance' => $newBalance]);

            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'adjustment',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Get wallet balance
     */
    public function getBalance(int $userId): float
    {
        $wallet = $this->getOrCreateWallet($userId);

        return (float) $wallet->balance;
    }

    /**
     * Get transaction history
     */
    public function getHistory(int $userId, int $limit = 50): array
    {
        $wallet = $this->getOrCreateWallet($userId);

        return $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
