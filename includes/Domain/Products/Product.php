<?php
namespace Pfs\Domain\Products;

class Product {

    public string $product_sku;
    public string $name;
    public ?string $image_path;
    public string $status;
    public float $purchase_price;
    public float $consumer_price;
    public float $sale_price;
    public int $step_sale_quantity;
    public string $unit;

    public function __construct(array $data) {

        $this->product_sku        = (string) $data['product_sku'];
        $this->name               = (string) $data['name'];
        $this->image_path         = $data['image_path'] ?? null;
        $this->status             = $data['status'] ?? 'active';

        $this->purchase_price     = (float) ($data['purchase_price'] ?? 0);
        $this->consumer_price     = (float) ($data['consumer_price'] ?? 0);
        $this->sale_price         = (float) ($data['sale_price'] ?? 0);

        $this->step_sale_quantity = (int) ($data['step_sale_quantity'] ?? 1);
        $this->unit               = (string) ($data['unit'] ?? 'unit');
    }
}
