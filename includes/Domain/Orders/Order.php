<?php
namespace MyEC\Domain\Orders;

class Order {

    public string $order_number;
    public int $user_id;
    public string $currency;
    public int $total_amount;
    public int $paid_amount;
    public string $status;
    public string $created_at;

    public function __construct(array $data) {
        $this->order_number = $data['order_number'];
        $this->user_id      = (int) $data['user_id'];
        $this->currency     = $data['currency'];
        $this->total_amount = (int) $data['total_amount'];
        $this->paid_amount  = (int) $data['paid_amount'];
        $this->status       = $data['status'];
        $this->created_at   = $data['created_at'] ?? current_time('mysql');
    }
}
