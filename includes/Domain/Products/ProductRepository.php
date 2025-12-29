<?php
namespace Pfs\Domain\Products;

use wpdb;

class ProductRepository {

    private wpdb $db;
    private string $table;

    public function __construct() {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pfs_products';
    }

    /**
     * ایجاد محصول
     */
    public function create(Product $product): int {

        $this->db->insert(
            $this->table,
            [
                'product_sku'        => $product->product_sku,
                'name'               => $product->name,
                'image_path'         => $product->image_path,
                'status'             => $product->status,
                'purchase_price'     => $product->purchase_price,
                'consumer_price'     => $product->consumer_price,
                'sale_price'         => $product->sale_price,
                'step_sale_quantity' => $product->step_sale_quantity,
                'unit'               => $product->unit,
                'created_at'         => current_time('mysql'),
                'updated_at'         => current_time('mysql'),
            ],
            [
                '%s','%s','%s','%s',
                '%f','%f','%f',
                '%d','%s',
                '%s','%s',
            ]
        );

        return (int) $this->db->insert_id;
    }

    /**
     * دریافت محصول با ID
     */
    public function findById(int $productId): ?array {

        $sql = $this->db->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE product_id = %d
             LIMIT 1",
            $productId
        );

        $row = $this->db->get_row($sql, ARRAY_A);

        return $row ?: null;
    }

    /**
     * لیست محصولات
     */
    public function paginate(int $limit, int $offset): array {

        $sql = $this->db->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE status != 'inactive'
             ORDER BY product_id DESC
             LIMIT %d OFFSET %d",
            $limit,
            $offset
        );

        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    /**
     * بروزرسانی محصول
     */
    public function update(int $productId, array $data): bool {

        if (empty($data)) {
            return false;
        }

        $allowed = [
            'product_sku',
            'name',
            'image_path',
            'status',
            'purchase_price',
            'consumer_price',
            'sale_price',
            'step_sale_quantity',
            'unit',
        ];

        $update = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        if (empty($update)) {
            return false;
        }

        $update['updated_at'] = current_time('mysql');

        return (bool) $this->db->update(
            $this->table,
            $update,
            ['product_id' => $productId]
        );
    }

    /**
     * حذف منطقی محصول
     */
    public function disable(int $productId): bool {

        return (bool) $this->db->update(
            $this->table,
            [
                'status'     => 'inactive',
                'updated_at' => current_time('mysql'),
            ],
            ['product_id' => $productId]
        );
    }
}
