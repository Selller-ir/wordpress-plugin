<?php
namespace Pfs\Admin\Controllers;

use Pfs\Domain\Categories\CategoryService;

class CategoriesController
{
    public function registerActions(): void {}

    public function render(): void
    {
        $service = new CategoryService();
        $categories = $service->list(100, 0);
        echo '<div class="wrap"><h1>دسته‌بندی‌ها</h1>';
        $data = ['categories' => $categories];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Categories/List.php';
        echo '</div>';
    }
}
