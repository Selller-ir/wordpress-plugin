<?php

use Pfs\Domain\OrderItems\OrderItem;
use Pfs\Domain\OrderItems\OrderItemService;

function pfs_add_order_item(int $order_id, int $product_id, int $price, float $quantity): int|WP_Error
{
    $service = new OrderItemService();
    $item = new OrderItem($order_id, $product_id, $price, $quantity);
    return $service->addItem($item);
}

function pfs_update_order_item(int $id, int $price, float $quantity): bool|WP_Error
{
    $service = new OrderItemService();
    $existing = $service->getItem($id);
    if (is_wp_error($existing)) {
        return $existing;
    }
    $existing->price = $price;
    $existing->quantity = $quantity;
    return $service->updateItem($existing);
}

function pfs_delete_order_item(int $id): bool|WP_Error
{
    $service = new OrderItemService();
    return $service->removeItem($id);
}

function pfs_get_order_item(int $id)
{
    $service = new OrderItemService();
    return $service->getItem($id);
}

function pfs_get_order_items(int $order_id): array
{
    $service = new OrderItemService();
    return $service->listByOrder($order_id);
}

function pfs_delete_order_items_by_order(int $order_id): bool|WP_Error
{
    $service = new OrderItemService();
    return $service->removeItemsByOrder($order_id);
}