<?php
namespace Pfs\Domain\Devices;

use WP_Error;
use Pfs\Domain\Devices\Device;
use Pfs\Domain\Devices\DeviceRepository;

class DeviceService
{
    private DeviceRepository $repository;

    public function __construct()
    {
        $this->repository = new DeviceRepository();
    }

    public function create(string $name): int|WP_Error
    {
        $name = trim($name);

        if ($name === '') {
            return new WP_Error(
                'invalid_name',
                'نام دستگاه الزامی است',
                ['status' => 422]
            );
        }

        $token = $this->generateToken();

        if ($this->repository->existsByToken($token)) {
            // خیلی کمیابه ولی حرفه‌ای برخورد می‌کنیم
            return new WP_Error(
                'token_collision',
                'خطا در تولید توکن',
                ['status' => 500]
            );
        }

        $device = new Device(
            $name,
            'active',
            $token
        );

        return $this->repository->insert($device);
    }

    public function authenticate(string $token): Device|WP_Error
    {
        $device = $this->repository->findByToken($token);

        if (!$device || $device->status !== 'active') {
            return new WP_Error(
                'unauthorized',
                'Device نامعتبر است',
                ['status' => 401]
            );
        }

        return $device;
    }

    public function disable(int $device_id): bool|WP_Error
    {
        return $this->repository->updateStatus(
            $device_id,
            'inactive'
        );
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 chars
    }

    public function list(): array
    {
        return $this->repository->findAll();
    }
}
