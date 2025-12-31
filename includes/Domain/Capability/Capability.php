<?php
namespace Pfs\Domain\Capability;

class Capability
{
    public ?int $id = null;

    public int $device_id;
    public int $product_id;
    public string $cap;

    public function __construct(
        int $device_id,
        int $product_id,
        string $cap
    ) 
    {
        $this->device_id  = $device_id;
        $this->product_id = $product_id;
        $this->cap        = $cap;
    }

    public static function fromDb(array $row): self
    {
        $capability = new self(
            (int) $row['device_id'],
            (int) $row['product_id'],
            $row['cap']
        );

        $capability->id = (int) $row['id'];

        return $capability;
    }
}
