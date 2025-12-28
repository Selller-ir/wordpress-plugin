<?php
namespace Pfs\Domain\Orders;

class OrderRepository {

    private string $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'pfs_orders';
    }

    /**
     * Insert Order
     */
public function create(Order $order): bool {
    global $wpdb;

    return (bool) $wpdb->insert(
        $this->table,
        [
            'device_id'   => $order->device_id,
            'user_id'     => $order->user_id,
            'total_price' => $order->total_price,
            'paid_price'  => $order->paid_price,
            'status'      => $order->status,
            'created_at'  => $order->created_at,
        ],
        ['%d','%d','%d','%d','%s','%s']
    );
}


    /**
     * Find by Order Number (حسابداری)
     */
    public function findByOrderId(string $orderId): ?array {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %s LIMIT 1",
            $orderId
        );

        return $wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Paginated list (100k+/month safe)
     */
    public function paginate(int $page, int $perPage, array $filters = []): array {
        global $wpdb;

        $offset = ($page - 1) * $perPage;
        $where  = '1=1';
        $args   = [];

        if (!empty($filters['status'])) {
            $where .= ' AND status = %s';
            $args[] = $filters['status'];
        }

        if (!empty($filters['from'])) {
            $where .= ' AND created_at >= %s';
            $args[] = $filters['from'];
        }

        if (!empty($filters['to'])) {
            $where .= ' AND created_at <= %s';
            $args[] = $filters['to'];
        }

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table}
             WHERE $where
             ORDER BY created_at DESC
             LIMIT %d OFFSET %d",
            array_merge($args, [$perPage, $offset])
        );

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
