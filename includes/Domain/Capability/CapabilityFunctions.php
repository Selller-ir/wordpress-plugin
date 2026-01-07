<?php

use Pfs\Domain\Capability\CapabilityService;
function pfs_add_cap(int $device_id,int $category_id,string $cap): int|WP_Error 
{
    $service = new CapabilityService();
    return $service->grant($device_id,$category_id,$cap);
}

function pfs_remove_cap(int $device_id,int $category_id,string $cap): int|WP_Error 
{
    $service = new CapabilityService();
    return $service->revoke($device_id,$category_id,$cap);
}
function pfs_get_caps_by_device(int $device_id): array 
{
    $service = new CapabilityService();
    return $service->listCapByDevice($device_id,);
}
function pfs_has_cap(int $device_id,int $category_id,string $cap): int 
{
    $service = new CapabilityService();
    return $service->check($device_id,$category_id,$cap);
}