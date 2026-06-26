<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentGatewayController extends Controller
{
    public function index(): Response
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();

        return Inertia::render('Admin/PaymentGateways/Index', [
            'gateways' => $gateways,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/PaymentGateways/Create', [
            'presets' => $this->presets(),
        ]);
    }

    public function show(PaymentGateway $paymentGateway): Response
    {
        return Inertia::render('Admin/PaymentGateways/Show', [
            'gateway' => $paymentGateway,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gateway_key' => ['required', 'string', 'unique:payment_gateways,gateway_key'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_sandbox' => ['boolean'],
            'credentials' => ['nullable', 'array'],
            'fee_fixed' => ['nullable', 'numeric', 'min:0'],
            'fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $validated['credentials'] = $this->sanitizeCredentials(
            $validated['gateway_key'],
            $request->input('credentials', []),
        );
        $validated['fee_fixed'] = $validated['fee_fixed'] ?? 0;
        $validated['fee_percentage'] = $validated['fee_percentage'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PaymentGateway::create($validated);

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Gateway de pagamento criado com sucesso.');
    }

    public function edit(PaymentGateway $paymentGateway): Response
    {
        return Inertia::render('Admin/PaymentGateways/Edit', [
            'gateway' => $paymentGateway,
            'presets' => $this->presets(),
            // Only expose which credential keys are already filled, never the
            // decrypted secret values themselves.
            'configuredKeys' => array_keys($paymentGateway->credentials ?? []),
        ]);
    }

    public function update(Request $request, PaymentGateway $paymentGateway): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_sandbox' => ['boolean'],
            'credentials' => ['nullable', 'array'],
            'fee_fixed' => ['nullable', 'numeric', 'min:0'],
            'fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // Merge so that blank credential fields keep their stored value
        // instead of overwriting existing secrets with empty strings.
        $validated['credentials'] = $this->mergeCredentials(
            $paymentGateway,
            $request->input('credentials', []),
        );

        $paymentGateway->update($validated);

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Gateway atualizado com sucesso.');
    }

    public function destroy(PaymentGateway $paymentGateway): RedirectResponse
    {
        if ($paymentGateway->payments()->exists()) {
            return back()->with('error', 'Não é possível deletar um gateway com pagamentos associados.');
        }

        $paymentGateway->delete();

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Gateway deletado com sucesso.');
    }

    /**
     * Available gateway presets and their credential field definitions.
     *
     * @return array<string, mixed>
     */
    protected function presets(): array
    {
        return config('payment_gateways.presets', []);
    }

    /**
     * Keep only credential keys that belong to the gateway preset and drop
     * empty values so we don't store blank secrets.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    protected function sanitizeCredentials(string $gatewayKey, array $input): array
    {
        $allowed = collect($this->presets()[$gatewayKey]['fields'] ?? [])
            ->pluck('key')
            ->all();

        $credentials = [];

        foreach ($input as $key => $value) {
            if ($allowed && ! in_array($key, $allowed, true)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $credentials[$key] = (string) $value;
        }

        return $credentials;
    }

    /**
     * Merge submitted credentials with the stored ones, preserving existing
     * secrets when the field is left blank.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    protected function mergeCredentials(PaymentGateway $gateway, array $input): array
    {
        $existing = $gateway->credentials ?? [];
        $incoming = $this->sanitizeCredentials($gateway->gateway_key, $input);

        return array_merge($existing, $incoming);
    }
}
