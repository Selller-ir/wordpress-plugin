<?php
namespace Pfs\Domain\Orders;

class Order {

    public ?int $order_id = null;
    public int $device_id;
    public ?int $user_id = null;
    public int $total_price;
    public int $paid_price;
    public string $status;
    public string $created_at;

    public function __construct(array $data) {
        $this->device_id   = (int) $data['device_id'];
        $this->user_id     = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->total_price = (int) $data['total_price'];
        $this->paid_price  = (int) $data['paid_price'];
        $this->status      = $data['status'];
        $this->created_at  = $data['created_at'] ?? current_time('mysql');
    }

    public static function fromDb(array $row): self
    {
        $order = new self([
            'device_id'   => (int) ($row['device_id'] ?? 0),
            'user_id'     => isset($row['user_id']) ? (int) $row['user_id'] : null,
            'total_price' => (int) $row['total_price'],
            'paid_price'  => (int) $row['paid_price'],
            'status'      => $row['status'],
            'created_at'  => $row['created_at'],
        ]);

        if (isset($row['order_id'])) {
            $order->order_id = (int) $row['order_id'];
        }
        return $order;
    }
}

