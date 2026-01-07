<?php

use Pfs\Domain\Products\ProductService;

function pfs_get_products_for_order(
    int $device_id,
    ?int $category_id = null,
    int $limit = 20,
    int $offset = 0
): array {
    $service = new ProductService();
    return $service->listForOrder($device_id, $category_id, $limit, $offset);
}