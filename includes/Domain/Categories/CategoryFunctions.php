<?php

use Pfs\Domain\Categories\CategoryService;

function pfs_create_category(string $name): int|WP_Error
{
    $service = new CategoryService();
    return $service->create($name);
}

function pfs_update_category(int $id, string $name): bool|WP_Error
{
    $service = new CategoryService();
    return $service->update($id, $name);
}

function pfs_delete_category(int $id): bool|WP_Error
{
    $service = new CategoryService();
    return $service->delete($id);
}

function pfs_get_category(int $id): \Pfs\Domain\Categories\Category|WP_Error
{
    $service = new CategoryService();
    return $service->getById($id);
}

function pfs_list_categories(int $limit = 50, int $offset = 0): array
{
    $service = new CategoryService();
    return $service->list($limit, $offset);
}

function pfs_assign_product_to_category(int $category_id, int $product_id): int|WP_Error
{
    $service = new CategoryService();
    return $service->assignProduct($category_id, $product_id);
}

function pfs_remove_product_from_category(int $category_id, int $product_id): bool|WP_Error
{
    $service = new CategoryService();
    return $service->removeProduct($category_id, $product_id);
}