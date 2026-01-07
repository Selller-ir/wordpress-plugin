<?php
namespace Pfs\Domain\Capability;

use wpdb;
use WP_Error;
use Pfs\Domain\Capability\Capability;

class CapabilityRepository
{
    private wpdb $db;
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pfs_capability';
    }

    public function exists(
        int $device_id,
        int $category_id,
        string $cap
    ): bool {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table}
                 WHERE device_id = %d
                   AND category_id = %d
                   AND cap = %s
                 LIMIT 1",
                $device_id,
                $category_id,
                $cap
            )
        );
    }

    public function insert(Capability $capability): int|WP_Error
    {
        $result = $this->db->insert(
            $this->table,
            [
                'device_id'  => $capability->device_id,
                'category_id' => $capability->category_id,
                'cap'        => $capability->cap,
            ],
            [
                '%d',
                '%d',
                '%s',
            ]
        );

        if ($result === false) {
            return new WP_Error(
                'db_error',
                $this->db->last_error ?: 'Failed to insert capability'
            );
        }

        return (int) $this->db->insert_id;
    }

    public function delete(
        int $device_id,
        int $category_id,
        string $cap
    ): bool|WP_Error {
        $result = $this->db->delete(
            $this->table,
            [
                'device_id'  => $device_id,
                'category_id' => $category_id,
                'cap'        => $cap,
            ],
            [
                '%d',
                '%d',
                '%s',
            ]
        );

        if ($result === false) {
            return new WP_Error(
                'db_error',
                $this->db->last_error
            );
        }

        return (bool) $result;
    }

    public function findByDevice(int $device_id): array
    {
        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE device_id = %d",
                $device_id
            ),
            ARRAY_A
        );

        return array_map(
            fn ($row) => Capability::fromDb($row),
            $rows
        );
    }
}
