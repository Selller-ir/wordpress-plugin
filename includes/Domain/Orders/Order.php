<?php
namespace Pfs\Domain\Orders;

class Order {

    public int $device_id;
    public int $user_id;
    public int $total_price;
    public int $paid_price;
    public string $status;
    public string $created_at;

    public function __construct(array $data) {
        $this->device_id   = (int) $data['device_id'];
        $this->user_id     = (int) $data['user_id'] ?? null;
        $this->total_price = (int) $data['total_price'];
        $this->paid_price  = (int) $data['paid_price'];
        $this->status      = $data['status'];
        $this->created_at  = $data['created_at'] ?? current_time('mysql');
    }
}

