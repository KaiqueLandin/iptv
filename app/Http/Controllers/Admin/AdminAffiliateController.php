<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminAffiliateController extends Controller
{
    public function index(Request $request): Response
    {
        $affiliates = Affiliate::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('referral_code', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Affiliates/Index', [
            'affiliates' => $affiliates,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Affiliate $affiliate): Response
    {
        $affiliate->load(['user', 'commissions.user', 'commissions.order', 'payouts']);

        return Inertia::render('Admin/Affiliates/Show', [
            'affiliate' => $affiliate,
        ]);
    }

    public function update(Request $request, Affiliate $affiliate): RedirectResponse
    {
        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $affiliate->update($validated);

        return back()->with('success', 'Afiliado atualizado com sucesso.');
    }

    public function approve(Affiliate $affiliate): RedirectResponse
    {
        $affiliate->update([
            'is_active' => true,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Afiliado aprovado com sucesso.');
    }

    public function commissions(Request $request): Response
    {
        $commissions = AffiliateCommission::query()
            ->with(['affiliate.user', 'user', 'order'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Affiliates/Commissions', [
            'commissions' => $commissions,
            'filters' => $request->only(['status']),
        ]);
    }

    public function approveCommission(AffiliateCommission $commission): RedirectResponse
    {
        $commission->approve();

        return back()->with('success', 'Comissão aprovada com sucesso.');
    }

    public function payouts(Request $request): Response
    {
        $payouts = AffiliatePayout::query()
            ->with('affiliate.user')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Affiliates/Payouts', [
            'payouts' => $payouts,
            'filters' => $request->only(['status']),
        ]);
    }

    public function completePayout(AffiliatePayout $payout): RedirectResponse
    {
        $payout->complete();

        return back()->with('success', 'Pagamento completado com sucesso.');
    }
}
