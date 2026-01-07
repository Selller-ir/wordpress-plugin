<?php
namespace Pfs\Domain\Categories;

use wpdb;
use WP_Error;

class CategoryRepository
{
    private wpdb $db;
    private string $table;
    private string $pivotTable;

    public function __construct()
    {
        global $wpdb;
        $this->db        = $wpdb;
        $this->table     = $wpdb->prefix . 'pfs_categories';
        $this->pivotTable = $wpdb->prefix . 'pfs_product_categories';
    }

    public function insert(Category $category): int|WP_Error
    {
        $result = $this->db->insert(
            $this->table,
            ['name' => $category->name],
            ['%s']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to insert category');
        }

        return (int) $this->db->insert_id;
    }

    public function update(Category $category): bool|WP_Error
    {
        if (!$category->category_id) {
            return new WP_Error('invalid_id', 'شناسه دسته‌بندی نامعتبر است', ['status' => 422]);
        }

        $result = $this->db->update(
            $this->table,
            ['name' => $category->name],
            ['category_id' => $category->category_id],
            ['%s'],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to update category');
        }

        return true;
    }

    public function delete(int $id): bool|WP_Error
    {
        $result = $this->db->delete(
            $this->table,
            ['category_id' => $id],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to delete category');
        }

        return (bool) $result;
    }

    public function findById(int $id): ?array
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE category_id = %d",
                $id
            ),
            ARRAY_A
        ) ?: null;
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->table} ORDER BY category_id DESC LIMIT %d OFFSET %d",
                $limit,
                $offset
            ),
            ARRAY_A
        );
    }

    public function exists(int $id): bool
    {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE category_id = %d LIMIT 1",
                $id
            )
        );
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $sql = $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE name = %s AND category_id != %d LIMIT 1",
                $name,
                $excludeId
            );
        } else {
            $sql = $this->db->prepare(
                "SELECT 1 FROM {$this->table} WHERE name = %s LIMIT 1",
                $name
            );
        }

        return (bool) $this->db->get_var($sql);
    }

    public function assignProduct(int $product_id, int $category_id): int|WP_Error
    {
        $result = $this->db->insert(
            $this->pivotTable,
            [
                'product_id'  => $product_id,
                'category_id' => $category_id,
            ],
            ['%d', '%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to assign product');
        }

        return (int) $this->db->insert_id;
    }

    public function removeProduct(int $product_id, int $category_id): bool|WP_Error
    {
        $result = $this->db->delete(
            $this->pivotTable,
            [
                'product_id'  => $product_id,
                'category_id' => $category_id,
            ],
            ['%d', '%d']
        );

        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error ?: 'Failed to remove product');
        }

        return (bool) $result;
    }

    public function getCategoriesByProduct(int $product_id): array
    {
        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT c.* FROM {$this->table} c JOIN {$this->pivotTable} pc ON pc.category_id = c.category_id WHERE pc.product_id = %d",
                $product_id
            ),
            ARRAY_A
        );

        return $rows;
    }
}