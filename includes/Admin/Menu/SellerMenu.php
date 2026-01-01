<?php

namespace Pfs\Admin\Menu;

use Pfs\Admin\Pages\ProductsPage;
use PFS\Admin\Pages\DevicesPage;
use Pfs\Admin\Controllers\ProductController;

defined('ABSPATH') || exit;

class SellerMenu
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenus']);
    }

    public function addMenus(): void
    {
        add_menu_page(
            'فروشنده',
            'فروشنده',
            'read',
            'pfs-seller',
            [$this, 'renderDashboard'],
            PFS_ASSETS. 'image/seller_icon.svg',
            0
        );

        add_submenu_page(
            'pfs-seller',
            'محصولات',
            'محصولات',
            'read',
            'pfs-seller-products',
            [$this, 'renderProducts']
        );

        add_submenu_page(
            'pfs-seller',
            'دستگاه ها',
            'دستگاه ها',
            'read',
            'pfs-seller-devices',
            [$this, 'renderDevices']
        );
    }

    public function renderDashboard(): void
    {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Seller Dashboard', 'pfs') . '</h1>';
        echo '</div>';
    }

    public function renderProducts(): void
    {
        new ProductController();
    }

    public function renderDevices(): void
    {
        // (new DevicesPage())->render();
    }
}
