<?php
namespace Pfs\Domain\Devices;

class DeviceRepository {

    public function findByToken(string $token): ?Device {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}pfs_devices WHERE token = %s",
                $token
            ),
            ARRAY_A
        );

        if (! $row) {
            return null;
        }

        return new Device($row);
    }
}
