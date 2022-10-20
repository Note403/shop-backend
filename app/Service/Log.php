<?php

namespace App\Service;

use App\Models\OrderItemLog;
use App\Models\OrderLog;
use Illuminate\Database\Eloquent\Collection;

class Log
{
    /** @var int => Amount of days the logs should be saved */
    private static int $save_days = 30;

    public static function createOrderLog(): bool
    {
        return true;
    }

    public static function getOrderLog(string $order_id, int $order_item_id = null): null|Collection
    {
        $order_log = OrderLog::query()
            ->where(OrderLog::ORDER, $order_id)
            ->get();

        if ($order_item_id == null) {
            $order_item_log = OrderItemLog::query()
                ->where(OrderItemLog::ORDER, $order_id)
                ->get();
        } else {
            $order_item_log = OrderItemLog::query()
                ->where(OrderItemLog::ORDER, $order_id)
                ->where(OrderItemLog::ORDER_ITEM, $order_item_id)
                ->get();
        }

        return new Collection();
    }

    public static function deleteOldLogs(): bool
    {
        return true;
    }
}
