<?php

namespace Pfs\Admin\Controllers;

use Pfs\Domain\Products\ProductService;
use Pfs\Domain\Products\Product;

defined('ABSPATH') || exit;

class ProductController extends BaseController
{
    private ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    /**
     * List products
     */
    public function index(): array
    {
        return $this->service->list();
    }

    /**
     * Show single product
     */
    public function show(int $id): Product
    {
        return $this->service->show($id);
    }

    /**
     * Store product
     */
    public function store(array $input): array
    {
        try {
            $id = $this->service->create(new Product(

            ));

            return $this->success(['id' => $id]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update product
     */
    public function update(int $id, array $input): array
    {
        if ($id <= 0) {
            return $this->error('Invalid product id');
        }

        try {
            $this->service->update($id, [
                'name'       => sanitize_text_field($input['name'] ?? ''),
                'sale_price' => (int) ($input['sale_price'] ?? 0),
                'status'     => sanitize_text_field($input['status'] ?? 'inactive'),
            ]);

            return $this->success();
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function delete(int $id): array
    {
        if ($id <= 0) {
            return $this->error('Invalid product id');
        }

        try {
            $this->service->delete($id);
            return $this->success();
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
