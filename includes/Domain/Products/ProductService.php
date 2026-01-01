<?php

namespace Pfs\Domain\Products;

use WP_Error;

class ProductService
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    /* =========================
     * CREATE
     * ========================= */

    public function create(Product $product): int|WP_Error
    {
        // SKU unique
        if ($this->repository->existsBySku($product->product_sku)) {
            return new WP_Error(
                'duplicate_sku',
                'این SKU قبلاً ثبت شده است',
                ['status' => 409]
            );
        }

        $error = $this->validate($product);
        if ($error) {
            return $error;
        }

        return $this->repository->insert($product);
    }

    /* =========================
     * UPDATE
     * ========================= */

    public function update(Product $product): bool|WP_Error
    {
        if (!$product->product_id) {
            return new WP_Error(
                'invalid_id',
                'شناسه محصول نامعتبر است',
                ['status' => 422]
            );
        }

        if (!$this->repository->exists($product->product_id)) {
            return new WP_Error(
                'not_found',
                'محصول مورد نظر یافت نشد',
                ['status' => 404]
            );
        }

        if (
            $this->repository->existsBySku(
                $product->product_sku,
                $product->product_id
            )
        ) {
            return new WP_Error(
                'duplicate_sku',
                'این SKU قبلاً ثبت شده است',
                ['status' => 409]
            );
        }

        $error = $this->validate($product);
        if ($error) {
            return $error;
        }

        return $this->repository->update($product);
    }

    /* =========================
     * DELETE
     * ========================= */

    public function delete(int $id): bool|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error(
                'invalid_id',
                'شناسه محصول نامعتبر است',
                ['status' => 422]
            );
        }

        if (!$this->repository->exists($id)) {
            return new WP_Error(
                'not_found',
                'محصول مورد نظر یافت نشد',
                ['status' => 404]
            );
        }

        return $this->repository->delete($id);
    }

    /* =========================
     * GET ONE
     * ========================= */

    public function getById(int $id): Product|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error(
                'invalid_id',
                'شناسه محصول نامعتبر است',
                ['status' => 422]
            );
        }

        $row = $this->repository->findById($id);

        if (!$row) {
            return new WP_Error(
                'not_found',
                'محصول مورد نظر یافت نشد',
                ['status' => 404]
            );
        }

        return Product::fromDb($row);
    }

    public function getBySku(string $sku): Product|WP_Error
    {
        $row = $this->repository->findBySku($sku);

        if (!$row) {
            return new WP_Error(
                'not_found',
                'محصول مورد نظر یافت نشد',
                ['status' => 404]
            );
        }

        return Product::fromDb($row);
    }

    /* =========================
     * LIST
     * ========================= */

    public function list(int $limit = 20, int $offset = 0): array
    {
        $rows = $this->repository->findAll($limit, $offset);

        return array_map(
            fn(array $row) => Product::fromDb($row),
            $rows
        );
    }

    /* =========================
     * VALIDATION
     * ========================= */

    private function validate(Product $product): ?WP_Error
    {
        if ($product->sale_price <= 0) {
            return new WP_Error(
                'invalid_sale_price',
                'قیمت فروش باید بزرگتر از صفر باشد',
                ['status' => 422]
            );
        }

        if ($product->step_sale_quantity <= 0) {
            return new WP_Error(
                'invalid_step_quantity',
                'step_sale_quantity باید بزرگتر از صفر باشد',
                ['status' => 422]
            );
        }

        if (round($product->step_sale_quantity, 3) !== $product->step_sale_quantity) {
            return new WP_Error(
                'invalid_precision',
                'حداکثر ۳ رقم اعشار مجاز است',
                ['status' => 422]
            );
        }

        if (empty($product->product_sku)) {
            return new WP_Error(
                'invalid_sku',
                'SKU نمی‌تواند خالی باشد',
                ['status' => 422]
            );
        }

        if (empty($product->name)) {
            return new WP_Error(
                'invalid_name',
                'نام محصول الزامی است',
                ['status' => 422]
            );
        }

        return null;
    }
}
