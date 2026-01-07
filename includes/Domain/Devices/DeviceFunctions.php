<?php
use Pfs\Domain\Devices\DeviceService;

function pfs_add_device(string $name): int|WP_Error {
    $service = new DeviceService();
    return $service->create($name);
}

function pfs_authenticate_device(string $token): Device|WP_Error {
    $service = new DeviceService();
    return $service->authenticate($token);
}

function pfs_disable_device(int $device_id): bool|WP_Error {
    $service = new DeviceService();
    return $service->disable($device_id);
}

function pfs_get_devices(): array {
    $service = new DeviceService();
    return $service->list();
}