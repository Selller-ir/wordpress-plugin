<?php
namespace Pfs\Domain\Products;

use WP_Error;
use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductRepository;

class ProductService
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function create(Product $product): int|WP_Error
    {
        if ($this->repository->existsBySku($product->product_sku)) {
            return new WP_Error(
                'duplicate_sku',
                'این SKU قبلاً ثبت شده است',
                ['status' => 409]
            );
        }

        if ($product->sale_price <= 0) {
            return new WP_Error(
                'invalid_price',
                'قیمت فروش نامعتبر است',
                ['status' => 422]
            );
        }

        if ($product->step_sale_quantity <= 0) {
            return new WP_Error(
                'invalid_step',
                'step_sale_quantity باید بزرگتر از صفر باشد',
                ['status' => 422]
            );
        }

        if (round($product->step_sale_quantity, 3) != $product->step_sale_quantity) {
            return new WP_Error(
                'invalid_precision',
                'حداکثر ۳ رقم اعشار مجاز است',
                ['status' => 422]
            );
        }

        return $this->repository->insert($product);
    }

    public function list(): array
    {
        return $this->repository->findAll();
    }
    //     public function show($id): array
    // {
    //     return $this->repository->findById($id);
    // }
    public function show(int $id): ?Product
{
    if ($id <= 0) {
        return null;
    }

    $row = $this->repository->findById($id);

    if (!$row) {
        return null;
    }

    return Product::fromDb($row);
}

    
}
