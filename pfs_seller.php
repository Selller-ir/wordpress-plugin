<?php
/**
 * Plugin Name: My E-Commerce Plugin
 * Version: 1.0.0
 */

// defined('ABSPATH') || exit;

// define('PFS_VERSION', '1.0.0');
// define('PFS_PATH', plugin_dir_path(__FILE__));
// define('PFS_URL', plugin_dir_url(__FILE__));

// require_once PFS_PATH . 'includes/Autoloader.php';

// Pfs\Autoloader::register();

// add_action('plugins_loaded', function () {
//     Pfs\Core\Plugin::init();
// });


class PFS_Seller {
    public function __construct() {
        $this->define_constans();
        $this->init();
    }
    public function define_constans() 
    {
        define('PFS_PATH', plugin_dir_path(__FILE__));
        define('PFS_URL',plugin_dir_url(__FILE__));
    }
    public function init() {
        register_activation_hook(__FILE__, [$this,'activation']);
        register_deactivation_hook(__FILE__, [$this,'deactivation']);
        $this->initAutoloader();
        $this->startPlugin();
    }
    public function activation() {
        
    }
    public function deactivation() {

    }
    public function initAutoloader() {
        require_once PFS_PATH . 'includes/Autoloader.php';
        Pfs\Autoloader::register();
    }
    public function startPlugin() {
        add_action('plugins_loaded', function () {
            Pfs\Core\Plugin::init();
        });
        // Pfs\Core\Plugin::init();
    }

}

new PFS_Seller();
