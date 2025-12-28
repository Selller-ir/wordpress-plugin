<?php
namespace Pfs\ManualTests\Domain\Orders;
class OrdersTest {
    public static function OrderRunTest()
    {
            $order = new \Pfs\Domain\Orders\Order([
                'device_id'   => 1,
                'user_id'     => get_current_user_id(),
                'total_price' => random_int(10,2000),
                'paid_price'  => 0,
                'status'      => 'issued'
            ]);
            $repo = new \Pfs\Domain\Orders\OrderRepository();
            $repo->create($order);

    }
}