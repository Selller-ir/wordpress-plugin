<?php
namespace Pfs\Domain\Capability;

use WP_Error;
use Pfs\Domain\Capability\Capability;
use Pfs\Domain\Capability\CapabilityRepository;

class CapabilityService
{
    private CapabilityRepository $repository;

    public function __construct()
    {
        $this->repository = new CapabilityRepository();
    }

    public function grant(
        int $device_id,
        int $product_id,
        string $cap
    ): int|WP_Error {

        $cap = trim(strtolower($cap));

        if ($cap === '') {
            return new WP_Error(
                'invalid_cap',
                'Capability نامعتبر است',
                ['status' => 422]
            );
        }

        if ($this->repository->exists($device_id, $product_id, $cap)) {
            return new WP_Error(
                'duplicate_capability',
                'این capability قبلاً ثبت شده است',
                ['status' => 409]
            );
        }

        $capability = new Capability(
            $device_id,
            $product_id,
            $cap
        );

        return $this->repository->insert($capability);
    }

    public function revoke(
        int $device_id,
        int $product_id,
        string $cap
    ): bool|WP_Error {

        return $this->repository->delete(
            $device_id,
            $product_id,
            $cap
        );
    }

    public function listByDevice(int $device_id): array
    {
        return $this->repository->findByDevice($device_id);
    }

    public function check(
        int $device_id,
        int $product_id,
        string $cap
    ): bool {
        return $this->repository->exists(
            $device_id,
            $product_id,
            $cap
        );
    }
}
