<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * List the authenticated customer's provisioned services (their IPTV
     * activation codes/credentials).
     */
    public function index(Request $request): Response
    {
        $services = $request->user()->services()
            ->with('product')
            ->latest()
            ->get()
            ->map(fn (Service $service) => $this->present($service));

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function show(Request $request, Service $service): Response
    {
        abort_unless($service->user_id === $request->user()->id, 403);

        $service->load('product');

        return Inertia::render('Services/Show', [
            'service' => $this->present($service, full: true),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(Service $service, bool $full = false): array
    {
        return [
            'id' => $service->id,
            'service_number' => $service->service_number,
            'product_name' => $service->product?->name,
            'status' => $service->status->value,
            'status_label' => $service->status->label(),
            'status_color' => $service->status->color(),
            'username' => $service->username,
            'password' => $service->password,
            'activation_code' => $service->activation_code,
            'access_url' => $service->access_url,
            'expires_at' => $service->expires_at?->toDateString(),
            'delivered_at' => $service->delivered_at?->toIso8601String(),
            'created_at' => $service->created_at?->toIso8601String(),
        ];
    }
}
