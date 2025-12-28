<?php
namespace Pfs\Core;

use Pfs\Database\Migrator;
use Pfs\ManualTests\RunTests;

class Plugin {

    public static function init() {
        Migrator::run();
        RunTests::run();
    }
}
