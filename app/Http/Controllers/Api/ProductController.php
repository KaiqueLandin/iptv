<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Whmcs\ProductCatalogService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductCatalogService $catalogService
    ) {}

    /**
     * Get product catalog
     */
    public function index(): JsonResponse
    {
        try {
            $catalog = $this->catalogService->getCatalog();
            
            return response()->json([
                'success' => true,
                'data' => $catalog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar catálogo de produtos',
            ], 500);
        }
    }

    /**
     * Refresh catalog cache
     */
    public function refresh(): JsonResponse
    {
        try {
            $catalog = $this->catalogService->getCatalog(forceRefresh: true);
            
            return response()->json([
                'success' => true,
                'message' => 'Catálogo atualizado com sucesso',
                'data' => $catalog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar catálogo',
            ], 500);
        }
    }
}
