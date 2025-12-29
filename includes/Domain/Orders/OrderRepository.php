<?php
namespace Pfs\Domain\Orders;

use RuntimeException;

class OrderRepository {

    private string $ordersTable;
    private string $itemsTable;

    public function __construct() {
        global $wpdb;
        $this->ordersTable = $wpdb->prefix . 'pfs_orders';
        $this->itemsTable  = $wpdb->prefix . 'pfs_order_items';
    }

    /**
     * ایجاد سفارش ساده (بدون آیتم)
     */
    public function create(Order $order): int {
        global $wpdb;

        $result = $wpdb->insert(
            $this->ordersTable,
            [
                'device_id'   => $order->device_id,
                'user_id'     => $order->user_id,
                'total_price' => $order->total_price,
                'paid_price'  => $order->paid_price,
                'status'      => $order->status,
                'created_at'  => current_time('mysql'),
            ],
            [
                '%d', '%d', '%d', '%d', '%s', '%s'
            ]
        );

        if ($result === false) {
            throw new RuntimeException('Failed to create order');
        }

        return (int) $wpdb->insert_id;
    }

    /**
     * ایجاد سفارش به همراه آیتم‌ها (اتمیک)
     */
    public function createWithItems(Order $order, array $items): int {
        global $wpdb;

        $wpdb->query('START TRANSACTION');

        try {
            $orderId = $this->create($order);

            foreach ($items as $item) {

                $inserted = $wpdb->insert(
                    $this->itemsTable,
                    [
                        'order_id'   => $orderId,
                        'product_id' => (int) $item['product_id'],
                        'price'      => (int) $item['price'],
                        'quantity'   => (int) $item['quantity'],
                        'created_at' => current_time('mysql'),
                    ],
                    [
                        '%d', '%d', '%d', '%d', '%s'
                    ]
                );

                if ($inserted === false) {
                    throw new RuntimeException('Failed to insert order item');
                }
            }

            $wpdb->query('COMMIT');
            return $orderId;

        } catch (\Throwable $e) {
            $wpdb->query('ROLLBACK');
            throw $e;
        }
    }

    /**
     * دریافت لیست سفارش‌ها برای sync حسابداری
     */
    public function paginate(array $args): array {
        global $wpdb;

        $deviceId = (int) $args['device_id'];
        $limit    = (int) ($args['limit'] ?? 50);
        $offset   = (int) ($args['offset'] ?? 0);
        $fromId   = (int) ($args['from_id'] ?? 0);

        $where = 'WHERE device_id = %d';
        $params = [$deviceId];

        if ($fromId > 0) {
            $where   .= ' AND order_id > %d';
            $params[] = $fromId;
        }

        $params[] = $limit;
        $params[] = $offset;

        $sql = $wpdb->prepare(
            "
            SELECT *
            FROM {$this->ordersTable}
            $where
            ORDER BY order_id ASC
            LIMIT %d OFFSET %d
            ",
            $params
        );

        $rows = $wpdb->get_results($sql, ARRAY_A);

        return array_map(fn($row) => $this->map($row), $rows);
    }

    /**
     * دریافت یک سفارش خاص متعلق به device
     */
    public function findById(int $orderId, int $deviceId): ?array {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$this->ordersTable}
                WHERE order_id = %d
                  AND device_id = %d
                LIMIT 1
                ",
                $orderId,
                $deviceId
            ),
            ARRAY_A
        );

        if (! $row) {
            return null;
        }

        $row['items'] = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT product_id, price, quantity
                FROM {$this->itemsTable}
                WHERE order_id = %d
                ",
                $orderId
            ),
            ARRAY_A
        );

        return $this->map($row);
    }

    /**
     * Mapper دیتابیس → خروجی API
     */
    private function map(array $row): array {
        return [
            'order_id'    => (int) $row['order_id'],
            'device_id'   => (int) $row['device_id'],
            'user_id'     => (int) $row['user_id'],
            'total_price' => (int) $row['total_price'],
            'paid_price'  => (int) $row['paid_price'],
            'status'      => $row['status'],
            'created_at'  => $row['created_at'],
            'items'       => $row['items'] ?? [],
        ];
    }
}
