<?php
namespace Pfs\Domain\OrderItems;

class OrderItem
{
    public ?int $id = null;
    public int $order_id;
    public int $product_id;
    public int $price;
    public float $quantity;
    public string $created_at;

    public function __construct(
        int $order_id,
        int $product_id,
        int $price,
        float $quantity
    ) {
        $this->order_id   = $order_id;
        $this->product_id = $product_id;
        $this->price      = $price;
        $this->quantity   = $quantity;
        $this->created_at = current_time('mysql');
    }

    public static function fromDb(array $row): self
    {
        $item = new self(
            (int) $row['order_id'],
            (int) $row['product_id'],
            (int) $row['price'],
            (float) $row['quantity']
        );
        $item->id = (int) $row['id'];
        $item->created_at = $row['created_at'];
        return $item;
    }
}