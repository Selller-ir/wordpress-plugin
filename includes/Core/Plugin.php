<?php
namespace Pfs\Core;

use Pfs\Database\Migrator;
use Pfs\ManualTests\RunTests;
use Pfs\Rest\Api;

class Plugin {

    public static function init() {
        Migrator::run();
        // RunTests::run();
        (new Api)->register();
    }
}
