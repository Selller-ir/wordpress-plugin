<?php
namespace Pfs\Domain\Capability;

class Capability {

    public int $device_id;
    public int $product_id;
    public string $cap;

    public function __construct(array $data) {
        $this->device_id  = (int) $data['device_id'];
        $this->product_id = (int) $data['product_id'];
        $this->cap        = $data['cap'];
    }
}
