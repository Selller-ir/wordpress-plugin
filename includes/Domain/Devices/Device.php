<?php
namespace Pfs\Domain\Devices;

class Device
{
    public ?int $device_id = null;

    public string $name;
    public string $status;
    public string $token;

    public function __construct(
        string $name,
        string $status,
        string $token
    ) {
        $this->name   = $name;
        $this->status = $status;
        $this->token  = $token;
    }

    public static function fromDb(array $row): self
    {
        $device = new self(
            $row['name'],
            $row['status'],
            $row['token']
        );

        $device->device_id = (int) $row['device_id'];

        return $device;
    }
}
