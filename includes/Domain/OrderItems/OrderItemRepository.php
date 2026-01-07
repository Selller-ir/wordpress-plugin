<?php
namespace Pfs\Domain\OrderItems;

use wpdb;
use WP_Error;

class OrderItemRepository
{
    private wpdb $db;
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pfs_order_items';
    }

    public function insert(OrderItem $item): int|WP_Error
    {
        $result = $this->db->insert(
            $this->table,
            [
                'order_id'   => $item->order_id,
                'product_id' => $item->product_id,
                'price'      => $item->price,
                'quantity'   => $item->quantity,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%d', '%d', '%f', '%s']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to insert order item');
        }

        return (int) $this->db->insert_id;
    }

    public function update(OrderItem $item): bool|WP_Error
    {
        if (!$item->id) {
            return new WP_Error('invalid_id', 'شناسه آیتم نامعتبر است', ['status' => 422]);
        }

        $result = $this->db->update(
            $this->table,
            [
                'price'    => $item->price,
                'quantity' => $item->quantity,
            ],
            ['id' => $item->id],
            ['%d', '%f'],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to update order item');
        }

        return true;
    }

    public function delete(int $id): bool|WP_Error
    {
        $result = $this->db->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to delete order item');
        }

        return (bool) $result;
    }

    public function deleteByOrder(int $order_id): bool|WP_Error
    {
        $result = $this->db->delete(
            $this->table,
            ['order_id' => $order_id],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to delete order items');
        }

        return (bool) $result;
    }

    public function findById(int $id): ?array
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        ) ?: null;
    }

    public function findByOrder(int $order_id): array
    {
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE order_id = %d ORDER BY id ASC",
                $order_id
            ),
            ARRAY_A
        );
    }

    public function exists(int $id): bool
    {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE id = %d LIMIT 1",
                $id
            )
        );
    }

    public function existsByProductInOrder(int $order_id, int $product_id): bool
    {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE order_id = %d AND product_id = %d LIMIT 1",
                $order_id,
                $product_id
            )
        );
    }
}