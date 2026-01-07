<?php

use Pfs\Domain\Orders\Order;
use Pfs\Domain\Orders\OrderService;

function pfs_create_order(array $data): int|WP_Error
{
    $service = new OrderService();
    $order   = new Order($data);
    return $service->create($order);
}

function pfs_create_order_with_items(array $data, array $items): int|WP_Error
{
    $service = new OrderService();
    $order   = new Order($data);
    return $service->createWithItems($order, $items);
}

function pfs_get_order(int $order_id, int $device_id)
{
    $service = new OrderService();
    return $service->getById($order_id, $device_id);
}

function pfs_list_orders(array $args): array
{
    $service = new OrderService();
    return $service->list($args);
}