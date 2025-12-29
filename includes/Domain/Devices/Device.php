<?php
namespace Pfs\Domain\Devices;

class Device {

    public int $device_id;
    public string $name;
    public string $status;
    public string $token;

    public function __construct(array $data) {
        $this->device_id = (int) $data['device_id'];
        $this->name      = $data['name'] ?? '';
        $this->status    = $data['status'];
        $this->token     = $data['token'];
    }

    public function isActive(): bool {
        return $this->status === 'active';
    }
}
