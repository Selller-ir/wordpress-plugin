<?php

namespace Pfs\Domain\Products;

use wpdb;

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

    /* =========================
     * INSERT
     * ========================= */

    public function insert(Product $product): int
    {
        $this->db->insert(
            $this->table,
            [
                'product_sku'        => $product->product_sku,
                'name'               => $product->name,
                'status'             => $product->status,
                'purchase_price'     => $product->purchase_price,
                'consumer_price'     => $product->consumer_price,
                'sale_price'         => $product->sale_price,
                'step_sale_quantity' => $product->step_sale_quantity,
                'unit'               => $product->unit,
                'image_path'         => $product->image_path,
                'created_at'         => current_time('mysql'),
                'updated_at'         => current_time('mysql'),
            ],
            [
                '%s', // product_sku
                '%s', // name
                '%s', // status
                '%d', // purchase_price
                '%d', // consumer_price
                '%d', // sale_price
                '%f', // step_sale_quantity
                '%s', // unit
                '%s', // image_path
                '%s', // created_at
                '%s', // updated_at
            ]
        );

        return (int) $this->db->insert_id;
    }

    /* =========================
     * UPDATE
     * ========================= */

    public function update(Product $product): bool
    {
        if (!$product->product_id) {
            return false;
        }

        return (bool) $this->db->update(
            $this->table,
            [
                'product_sku'        => $product->product_sku,
                'name'               => $product->name,
                'status'             => $product->status,
                'purchase_price'     => $product->purchase_price,
                'consumer_price'     => $product->consumer_price,
                'sale_price'         => $product->sale_price,
                'step_sale_quantity' => $product->step_sale_quantity,
                'unit'               => $product->unit,
                'image_path'         => $product->image_path,
                'updated_at'         => current_time('mysql'),
            ],
            [
                'product_id' => $product->product_id,
            ],
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%f',
                '%s',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );
    }

    /* =========================
     * DELETE
     * ========================= */

    public function delete(int $id): bool
    {
        return (bool) $this->db->delete(
            $this->table,
            ['product_id' => $id],
            ['%d']
        );
    }

    /* =========================
     * FIND
     * ========================= */

    public function findById(int $id): ?array
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE product_id = %d",
                $id
            ),
            ARRAY_A
        ) ?: null;
    }

    public function findBySku(string $sku): ?array
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE product_sku = %s",
                $sku
            ),
            ARRAY_A
        ) ?: null;
    }

    public function findAll(int $limit = 20, int $offset = 0): array
    {
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->table}
                 ORDER BY product_id DESC
                 LIMIT %d OFFSET %d",
                $limit,
                $offset
            ),
            ARRAY_A
        );
    }

    /* =========================
     * EXISTS
     * ========================= */

    public function exists(int $id): bool
    {
        $count = $this->db->get_var(
            $this->db->prepare(
                "SELECT COUNT(1) FROM {$this->table} WHERE product_id = %d",
                $id
            )
        );

        return ((int) $count) > 0;
    }

    public function existsBySku(string $sku, ?int $excludeProductId = null): bool
    {
        if ($excludeProductId !== null) {
            $count = $this->db->get_var(
                $this->db->prepare(
                    "SELECT COUNT(1)
                     FROM {$this->table}
                     WHERE product_sku = %s
                       AND product_id != %d",
                    $sku,
                    $excludeProductId
                )
            );
        } else {
            $count = $this->db->get_var(
                $this->db->prepare(
                    "SELECT COUNT(1)
                     FROM {$this->table}
                     WHERE product_sku = %s",
                    $sku
                )
            );
        }

        return ((int) $count) > 0;
    }
}
