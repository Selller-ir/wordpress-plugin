<?php
namespace Pfs\Admin\Menu;

use Pfs\Admin\Controllers\ProductsController;
use Pfs\Admin\Controllers\CategoriesController;
use Pfs\Admin\Controllers\DevicesController;

class SellerMenu
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenus']);
        add_action('admin_init', [new ProductsController(), 'registerActions']);
        add_action('admin_init', [new CategoriesController(), 'registerActions']);
        add_action('admin_init', [new DevicesController(), 'registerActions']);
    }

    public function addMenus(): void
    {
        add_menu_page(
            'فروشگاه',
            'فروشگاه',
            'read',
            'pfs-admin',
            [$this, 'renderDashboard'],
            PFS_ASSETS . 'image/seller_icon.svg',
            0
        );

        add_submenu_page(
            'pfs-admin',
            'محصولات',
            'محصولات',
            'read',
            'pfs-products',
            [$this, 'renderProducts']
        );

        add_submenu_page(
            'pfs-admin',
            'دسته‌بندی‌ها',
            'دسته‌بندی‌ها',
            'read',
            'pfs-categories',
            [$this, 'renderCategories']
        );

        add_submenu_page(
            'pfs-admin',
            'دستگاه‌ها',
            'دستگاه‌ها',
            'read',
            'pfs-devices',
            [$this, 'renderDevices']
        );
    }

    public function renderDashboard(): void
    {
        echo '<div class="wrap"><h1>داشبورد فروشگاه</h1></div>';
    }
    public function renderProducts(): void
    {
        (new ProductsController())->render();
    }
    public function renderCategories(): void
    {
        (new CategoriesController())->render();
    }
    public function renderDevices(): void
    {
        (new DevicesController())->render();
    }
}
