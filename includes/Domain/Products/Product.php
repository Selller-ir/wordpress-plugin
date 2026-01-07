<?php
namespace Pfs\Domain\Products;

class Product
{
    public ?int $product_id = null;

    public string $product_sku;
    public string $name;
    public ?string $image_id;
    public string $status;

    public ?float $purchase_price;
    public ?float $consumer_price;
    public float $sale_price;

    public float $step_sale_quantity;
    public ?string $unit;

    public string $created_at;
    public string $updated_at;

    public function __construct(
        string $product_sku,
        string $name,
        string $status,
        float $sale_price,
        float $step_sale_quantity,
        ?float $purchase_price = null,
        ?float $consumer_price = null,
        ?string $image_id = null,
        ?string $unit = null
    ) {
        $this->product_sku        = $product_sku;
        $this->name               = $name;
        $this->status             = $status;
        $this->sale_price         = $sale_price;
        $this->step_sale_quantity = $step_sale_quantity;

        $this->purchase_price     = $purchase_price;
        $this->consumer_price     = $consumer_price;
        $this->image_id           = $image_id;
        $this->unit               = $unit;

        $this->created_at = current_time('mysql');
        $this->updated_at = current_time('mysql');
    }

    public static function fromDb(array $row): self
    {
        $product = new self(
            $row['product_sku'],
            $row['name'],
            $row['status'],
            (float) $row['sale_price'],
            (float) $row['step_sale_quantity'],
            isset($row['purchase_price']) ? (float) $row['purchase_price'] : null,
            isset($row['consumer_price']) ? (float) $row['consumer_price'] : null,
            $row['image_id'] ?? null,
            $row['unit'] ?? null
        );

        $product->product_id = (int) $row['product_id'];
        $product->created_at = $row['created_at'];
        $product->updated_at = $row['updated_at'];

        return $product;
    }
}
