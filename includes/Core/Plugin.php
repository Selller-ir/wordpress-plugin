<?php
namespace Pfs\Core;

use Pfs\Database\Migrator;
use Pfs\Domain\GlobalFunctions;
use Pfs\ManualTests\RunTests;
use Pfs\Api\Api;
use Pfs\Admin\Menu\SellerMenu;

class Plugin {

    public static function init() {
        self::bootstrap();
        Migrator::run();
        GlobalFunctions::registerFunctions();
        // RunTests::run();
        (new Api)->register();
        (new \Pfs\Admin\AdminAssets())->register();
        (new SellerMenu())->register();
    }
    private static function bootstrap() {


    }
}
