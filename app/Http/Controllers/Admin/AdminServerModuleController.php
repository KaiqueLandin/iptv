<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServerModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminServerModuleController extends Controller
{
    public function index(): Response
    {
        $modules = ServerModule::latest()->get();

        return Inertia::render('Admin/ServerModules/Index', [
            'modules' => $modules,
        ]);
    }

    public function create(): Response
    {
        $availableModules = [
            'cpanel' => 'cPanel/WHM',
            'plesk' => 'Plesk',
            'directadmin' => 'DirectAdmin',
            'virtualizor' => 'Virtualizor',
            'proxmox' => 'Proxmox VE',
            'solusvm' => 'SolusVM',
            'custom' => 'Custom Script',
        ];

        return Inertia::render('Admin/ServerModules/Create', [
            'availableModules' => $availableModules,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'module_key' => ['required', 'string', 'unique:server_modules,module_key'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'server_hostname' => ['nullable', 'string'],
            'server_port' => ['nullable', 'integer'],
            'use_ssl' => ['boolean'],
            'credentials' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'provision_script' => ['nullable', 'string'],
        ]);

        ServerModule::create($validated);

        return redirect()->route('admin.server-modules.index')
            ->with('success', 'Módulo de servidor criado com sucesso.');
    }

    public function edit(ServerModule $serverModule): Response
    {
        return Inertia::render('Admin/ServerModules/Edit', [
            'module' => $serverModule,
        ]);
    }

    public function update(Request $request, ServerModule $serverModule): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'server_hostname' => ['nullable', 'string'],
            'server_port' => ['nullable', 'integer'],
            'use_ssl' => ['boolean'],
            'credentials' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'provision_script' => ['nullable', 'string'],
        ]);

        $serverModule->update($validated);

        return redirect()->route('admin.server-modules.index')
            ->with('success', 'Módulo atualizado com sucesso.');
    }

    public function testConnection(ServerModule $serverModule): RedirectResponse
    {
        if (! $serverModule->server_hostname) {
            return back()->with('error', 'Nenhum hostname configurado para este módulo.');
        }

        $url = $serverModule->getApiUrl();
        $timeout = 5;

        try {
            $ctx = stream_context_create([
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);

            $address = ($serverModule->use_ssl ? 'ssl://' : 'tcp://')
                .$serverModule->server_hostname.':'
                .($serverModule->server_port ?: ($serverModule->use_ssl ? 443 : 80));

            $fp = @stream_socket_client(
                $address,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $ctx,
            );

            if ($fp) {
                fclose($fp);

                return back()->with('success', "Conexão com {$url} estabelecida com sucesso.");
            }

            return back()->with('error', "Falha ao conectar em {$url}: {$errstr} (#{$errno})");
        } catch (\Throwable $e) {
            return back()->with('error', "Erro ao testar conexão: {$e->getMessage()}");
        }
    }

    public function destroy(ServerModule $serverModule): RedirectResponse
    {
        $serverModule->delete();

        return redirect()->route('admin.server-modules.index')
            ->with('success', 'Módulo deletado com sucesso.');
    }
}
