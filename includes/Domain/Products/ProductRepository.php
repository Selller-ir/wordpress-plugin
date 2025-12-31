<?php
namespace Pfs\Domain\Products;

use wpdb;
use WP_Error;
use Pfs\Domain\Products\Product;

class ProductRepository
{
    private wpdb $db;
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pfs_products';
    }
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

    public function existsBySku(string $sku): bool
    {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE product_sku = %s LIMIT 1",
                $sku
            )
        );
    }

    public function insert(Product $product): int|WP_Error
    {
        $result = $this->db->insert(
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
                'created_at'         => $product->created_at,
                'updated_at'         => $product->updated_at,
            ],
            [
                '%s', // sku
                '%s', // name
                '%s', // image
                '%s', // status
                '%d', // purchase_price
                '%d', // consumer_price
                '%d', // sale_price
                '%f', // step_sale_quantity (DECIMAL)
                '%s', // unit
                '%s', // created_at
                '%s', // updated_at
            ]
        );

        if ($result === false) {
            return new WP_Error(
                'db_error',
                $this->db->last_error ?: 'Database insert failed'
            );
        }

        return (int) $this->db->insert_id;
    }

    public function findAll(): array
    {
        $rows = $this->db->get_results(
            "SELECT * FROM {$this->table} ORDER BY product_id DESC",
            ARRAY_A
        );

        return array_map(
            fn ($row) => Product::fromDb($row),
            $rows
        );
    }
}
