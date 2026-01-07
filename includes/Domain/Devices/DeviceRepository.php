<?php
namespace Pfs\Domain\Devices;

use wpdb;
use WP_Error;
use Pfs\Domain\Devices\Device;

class DeviceRepository
{
    private wpdb $db;
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pfs_devices';
    }

    public function findByToken(string $token): ?Device
    {
        $row = $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table}
                 WHERE token = %s
                 LIMIT 1",
                $token
            ),
            ARRAY_A
        );

        return $row ? Device::fromDb($row) : null;
    }

    public function existsByToken(string $token): bool
    {
        return (bool) $this->db->get_var(
            $this->db->prepare(
                "SELECT 1 FROM {$this->table}
                 WHERE token = %s
                 LIMIT 1",
                $token
            )
        );
    }

    public function insert(Device $device): int|WP_Error
    {
        $result = $this->db->insert(
            $this->table,
            [
                'name'   => $device->name,
                'status' => $device->status,
                'token'  => $device->token,
            ],
            [
                '%s',
                '%s',
                '%s',
            ]
        );

        if ($result === false) {
            return new WP_Error(
                'db_error',
                $this->db->last_error ?: 'Failed to insert device'
            );
        }

        return (int) $this->db->insert_id;
    }

    public function updateStatus(int $device_id, string $status): bool|WP_Error
    {
        $result = $this->db->update(
            $this->table,
            ['status' => $status],
            ['device_id' => $device_id],
            ['%s'],
            ['%d']
        );

        if ($result === false) {
            return new WP_Error(
                'db_error',
                $this->db->last_error
            );
        }

        return true;
    }

    public function updateFields(int $device_id, array $fields): bool|WP_Error
    {
        $allowed = ['name', 'status'];
        $data = [];
        $format = [];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $fields)) {
                $data[$key] = $fields[$key];
                $format[] = '%s';
            }
        }
        if (empty($data)) {
            return new WP_Error('invalid_params', 'هیچ فیلدی برای بروزرسانی وجود ندارد', ['status' => 422]);
        }

        $result = $this->db->update(
            $this->table,
            $data,
            ['device_id' => $device_id],
            $format,
            ['%d']
        );
        if ($result === false) {
            return new WP_Error('db_error', $this->db->last_error);
        }
        return true;
    }
    public function findAll(): array
    {
        $rows = $this->db->get_results(
            "SELECT * FROM {$this->table} ORDER BY device_id DESC",
            ARRAY_A
        );

        return array_map(
            fn ($row) => Device::fromDb($row),
            $rows
        );
    }
}
