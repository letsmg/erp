<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Lista todas as vendas (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'client_id' => $request->input('client_id'),
        ];

        $result = $this->orderService->getAdminOrders($filters);

        if ($result['success']) {
            return $this->success($result['orders'], 'Vendas carregadas com sucesso');
        }

        return $this->error($result['message']);
    }

    /**
     * Detalhes de uma venda (Admin)
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $result = $this->orderService->getOrderDetails($id, auth()->id());

        if ($result['success']) {
            return $this->success($result['sale'], 'Detalhes da venda carregados');
        }

        return $this->error($result['message'], 404);
    }

    /**
     * Atualiza status de uma venda (Admin)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,canceled',
        ]);

        $result = $this->orderService->updateOrderStatus($id, $request->input('status'));

        if ($result['success']) {
            return $this->success(null, $result['message']);
        }

        return $this->error($result['message'], 400);
    }

    /**
     * Cancela uma venda (Admin)
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $result = $this->orderService->cancelOrder($id, auth()->id());

        if ($result['success']) {
            return $this->success(null, $result['message']);
        }

        return $this->error($result['message'], 400);
    }
}