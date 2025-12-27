<?php
namespace Pfs\Core;

use Pfs\Database\Migrator;

class Plugin {

    public static function init() {
        Migrator::run();
    }
}
