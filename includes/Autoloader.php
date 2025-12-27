<?php
namespace Pfs;

class Autoloader {

    public static function register() {
        spl_autoload_register(function ($class) {

            if (strpos($class, 'Pfs\\') !== 0) {
                return;
            }

            $path = PFS_PATH . 'includes/' .
                str_replace(['Pfs\\', '\\'], ['', '/'], $class) .
                '.php';

            if (file_exists($path)) {
                require_once $path;
            }
        });
    }
}
