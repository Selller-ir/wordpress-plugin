<?php
namespace Pfs\Domain\Capability;

class CapabilityRepository {

    public function has(
        int $deviceId,
        int $productId,
        string $cap
    ): bool {
        global $wpdb;

        return (bool) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1 FROM {$wpdb->prefix}pfs_capability
                 WHERE device_id = %d
                   AND product_id = %d
                   AND cap = %s
                 LIMIT 1",
                $deviceId,
                $productId,
                $cap
            )
        );
    }
}
