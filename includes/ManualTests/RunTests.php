<?php
namespace Pfs\ManualTests;

use Pfs\ManualTests\Domain\Orders\OrdersTest;
class RunTests{
    public static function run() {
        if (isset($_GET['test']) && $_GET['test'] === 'true') {
        OrdersTest::OrderRunTest();
        wp_die('finish run tests');
        }

    }
    
}
