<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderRequest;
use App\Models\Article;
use App\Models\Order;
use App\Models\OrderItem;
use App\Service\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function all(): JsonResponse
    {
        return Response::success(Order::all());
    }

    public function create(OrderRequest $request): JsonResponse
    {
        if (!$request->authorize())
            Return Response::error();

        $data = $request->validated();

        try {
            $order = Order::query()->create([
                Order::ID => Str::uuid(),
                Order::PRICE => $data['price'],
                Order::STATUS => 'OPEN',
                Order::PAYED => false
            ]);
        } catch (Exception $e) {
            report($e);
            return Response::error('order_create');
        }

        try {
            foreach ($data['items'] as $item) {
                if (Article::find($item['article']) == null) {
                    if ($this->rollbackOrder($order->id)){
                        return Response::error('order_create');
                    } else {
                        return Response::error();
                    }
                }

                OrderItem::query()->create([
                    OrderItem::ORDER => $order->id,
                    OrderItem::ARTICLE => $item['article'],
                    OrderItem::AMOUNT => $item['amount'],
                ]);
            }
        } catch (Exception $e) {
            report($e);
            return Response::error('order_create');
        }

        return Response::success();
    }

    public function patch(OrderRequest $request, string $order_id): JsonResponse
    {
        $data = $request->validated();

        $old_order_data = Order::query()
            ->where(Order::ID, $order_id)
            ->get()->first();

        $old_items_data = OrderItem::query()
            ->where(OrderItem::ORDER, $order_id)
            ->get();

        unset($old_order_data['items']);

        return Response::success();
    }

    public function getByUser(): JsonResponse
    {
        return Response::success();
    }

    public function getById(): JsonResponse
    {

    }

    private function rollbackOrder(string $order_id): bool
    {
        return Order::query()
            ->where(Order::ID, $order_id)
            ->limit(1)->delete();
    }
}
