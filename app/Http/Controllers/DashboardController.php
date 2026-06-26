<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\TeamInvitation;
use App\Services\Whmcs\CreditWalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected CreditWalletService $walletService,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $email = strtolower($user->email);

        $pendingInvitations = TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (TeamInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'team' => [
                    'name' => $invitation->team->name,
                    'slug' => $invitation->team->slug,
                ],
            ]);

        // Wallet data (optional credit balance feature).
        $balance = $this->walletService->getBalance($user->id);
        $recentTransactions = $this->walletService->getHistory($user->id, 10);

        // Native products available for purchase (real IDs drive checkout).
        $products = Product::active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format((float) $product->price, 2, ',', '.'),
                'description' => $product->description ? strip_tags($product->description) : null,
                'checkoutUrl' => route('checkout.store', $product),
            ]);

        // The customer's provisioned services (their IPTV codes).
        $services = $user->services()
            ->with('product')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'service_number' => $service->service_number,
                'product_name' => $service->product?->name,
                'status' => $service->status->value,
                'status_label' => $service->status->label(),
                'status_color' => $service->status->color(),
                'activation_code' => $service->activation_code,
                'expires_at' => $service->expires_at?->toDateString(),
            ]);

        return Inertia::render('Dashboard', [
            'pendingInvitations' => $pendingInvitations,
            'wallet' => [
                'balance' => $balance,
                'formattedBalance' => number_format($balance, 2, ',', '.'),
            ],
            'recentTransactions' => $recentTransactions,
            'products' => $products,
            'services' => $services,
        ]);
    }
}
