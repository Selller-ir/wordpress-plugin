<?php
namespace Pfs\Database;

use Pfs\Database\Migrations\Migration_1_0_0;

class Migrator {

    public static function run() {
        $installed = get_option('pfs_db_version', '0.0.0');

        if (version_compare($installed, '1.0.0', '<')) {
            (new Migration_1_0_0())->up();
            update_option('pfs_db_version', '1.0.0');
        }

        //  ADD NEW Migration
        // if (version_compare($installed, '1.0.1', '<')) {
        //     (new Migration_1_0_1())->up();
        //     update_option('pfs_db_version', '1.0.1');
        // }
    }
}
