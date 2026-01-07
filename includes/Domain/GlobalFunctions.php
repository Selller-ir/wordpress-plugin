<?php 
namespace Pfs\Domain;

class GlobalFunctions
{
    public static function registerFunctions() 
    {
        require 'Capability/CapabilityFunctions.php';
        require 'Devices/DeviceFunctions.php';
        require 'Categories/CategoryFunctions.php';
        require 'Orders/OrderFunctions.php';
        require 'Products/ProductFunctions.php';
        require 'OrderItems/OrderItemFunctions.php';
    }
}

