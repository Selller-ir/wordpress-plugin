<?php
namespace Pfs\Admin\Controllers;

use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductService;
use Pfs\Domain\Categories\CategoryService;

class ProductsController
{
    public function registerActions(): void
    {
        add_action('admin_post_pfs_product_store', [$this, 'store']);
        add_action('admin_post_pfs_product_update', [$this, 'update']);
    }

    public function render(): void
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : 'index';
        if ($action === 'add') {
            $this->viewAdd();
            return;
        }
        if ($action === 'edit') {
            $this->viewEdit();
            return;
        }
        $this->viewIndex();
    }

    public function store(): void
    {
        if (!isset($_POST['pfs_nonce']) || !wp_verify_nonce($_POST['pfs_nonce'], 'pfs_create_product')) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'invalid'], admin_url('admin.php?page=pfs-products')));
            exit;
        }

        foreach (['product_sku','name','status','sale_price','step_sale_quantity'] as $f) {
            if (!isset($_POST[$f]) || $_POST[$f] === '') {
                $key = wp_generate_uuid4();
                $data = [];
                foreach ($_POST as $k => $v) {
                    $data[$k] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
                }
                set_transient('pfs_form_'.$key, $data, 60);
                wp_safe_redirect(add_query_arg(['pfs_notice' => 'missing_'.$f, 'pfs_form' => $key], admin_url('admin.php?page=pfs-products&action=add')));
                exit;
            }
        }

        $product = new Product(
            sanitize_text_field($_POST['product_sku']),
            sanitize_text_field($_POST['name']),
            sanitize_key($_POST['status']),
            (float) $_POST['sale_price'],
            (float) $_POST['step_sale_quantity'],
            isset($_POST['purchase_price']) ? (float) $_POST['purchase_price'] : null,
            isset($_POST['consumer_price']) ? (float) $_POST['consumer_price'] : null,
            isset($_POST['image_id']) ? (int) $_POST['image_id'] : null,
            sanitize_text_field($_POST['unit'] ?? '')
        );

        $service = new ProductService();
        $result  = $service->create($product);

        if (is_wp_error($result)) {
            $key = wp_generate_uuid4();
            $data = [];
            foreach ($_POST as $k => $v) {
                $data[$k] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
            }
            set_transient('pfs_form_'.$key, $data, 60);
            $url = add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $result->get_error_message(), 'pfs_form' => $key], admin_url('admin.php?page=pfs-products&action=add'));
            wp_safe_redirect($url);
            exit;
        }

        $product_id = (int) $result;
        if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
            $catService = new CategoryService();
            foreach ($_POST['categories'] as $cid) {
                $cid = (int) $cid;
                if ($cid > 0) {
                    $catService->assignProduct($cid, $product_id);
                }
            }
        }

        wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], admin_url('admin.php?page=pfs-products')));
        exit;
    }

    public function update(): void
    {
        if (!isset($_POST['pfs_nonce']) || !wp_verify_nonce($_POST['pfs_nonce'], 'pfs_update_product')) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'invalid'], admin_url('admin.php?page=pfs-products')));
            exit;
        }

        $id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        if ($id <= 0) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'invalid'], admin_url('admin.php?page=pfs-products')));
            exit;
        }

        foreach (['product_sku','name','status','sale_price','step_sale_quantity'] as $f) {
            if (!isset($_POST[$f]) || $_POST[$f] === '') {
                $key = wp_generate_uuid4();
                $data = [];
                foreach ($_POST as $k => $v) {
                    $data[$k] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
                }
                set_transient('pfs_form_'.$key, $data, 60);
                $url = add_query_arg(['pfs_notice' => 'missing_'.$f, 'pfs_form' => $key], admin_url('admin.php?page=pfs-products&action=edit&id=' . $id));
                wp_safe_redirect($url);
                exit;
            }
        }

        $service  = new ProductService();
        $existing = $service->getById($id);
        if (is_wp_error($existing)) {
            $key = wp_generate_uuid4();
            set_transient('pfs_form_'.$key, array_map('sanitize_text_field', $_POST), 60);
            $url = add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $existing->get_error_message(), 'pfs_form' => $key], admin_url('admin.php?page=pfs-products&action=edit&id=' . $id));
            wp_safe_redirect($url);
            exit;
        }

        $existing->product_sku        = sanitize_text_field($_POST['product_sku']);
        $existing->name               = sanitize_text_field($_POST['name']);
        $existing->status             = sanitize_key($_POST['status']);
        $existing->purchase_price     = isset($_POST['purchase_price']) ? (float) $_POST['purchase_price'] : null;
        $existing->consumer_price     = isset($_POST['consumer_price']) ? (float) $_POST['consumer_price'] : null;
        $existing->sale_price         = (float) $_POST['sale_price'];
        $existing->step_sale_quantity = (float) $_POST['step_sale_quantity'];
        $existing->unit               = sanitize_text_field($_POST['unit'] ?? '');
        $existing->image_id           = isset($_POST['image_id']) ? (int) $_POST['image_id'] : null;
        $existing->product_id         = $id;

        $result = $service->update($existing);
        if (is_wp_error($result)) {
            $key = wp_generate_uuid4();
            $data = [];
            foreach ($_POST as $k => $v) {
                $data[$k] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
            }
            set_transient('pfs_form_'.$key, $data, 60);
            $url = add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $result->get_error_message(), 'pfs_form' => $key], admin_url('admin.php?page=pfs-products&action=edit&id=' . $id));
            wp_safe_redirect($url);
            exit;
        }

        $catService = new CategoryService();
        $current = array_map(
            fn($c) => $c->category_id,
            $catService->getCategoriesByProduct($id)
        );
        $newCats = [];
        if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
            foreach ($_POST['categories'] as $cid) {
                $cid = (int) $cid;
                if ($cid > 0) { $newCats[] = $cid; }
            }
        }
        $toAdd = array_diff($newCats, $current);
        $toRemove = array_diff($current, $newCats);
        foreach ($toAdd as $cid) {
            $catService->assignProduct($cid, $id);
        }
        foreach ($toRemove as $cid) {
            $catService->removeProduct($cid, $id);
        }

        wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], admin_url('admin.php?page=pfs-products')));
        exit;
    }

    private function renderNotice(): void
    {
        $notice = isset($_GET['pfs_notice']) ? (string) $_GET['pfs_notice'] : '';
        if ($notice === '') { return; }
        if ($notice === 'success') { echo '<div class="alert alert-success" role="alert"><p>عملیات موفق</p></div>'; return; }
        if ($notice === 'invalid') { echo '<div class="alert alert-danger" role="alert"><p>درخواست نامعتبر</p></div>'; return; }
        if (substr($notice, 0, 8) === 'missing_') { $f = substr($notice, 8); echo '<div class="alert alert-danger" role="alert"><p>فیلد '.esc_html($f).' الزامی است</p></div>'; return; }
        if ($notice === 'error') {
            $msg = isset($_GET['pfs_error']) ? sanitize_text_field($_GET['pfs_error']) : 'خطا در عملیات';
            echo '<div class="alert alert-danger" role="alert"><p>' . esc_html($msg) . '</p></div>';
            return;
        }
    }

    private function viewIndex(): void
    {
        $service = new ProductService();
        $products = $service->list();
        $catService = new CategoryService();
        $categoriesByProduct = [];
        foreach ($products as $p) {
            $categoriesByProduct[$p->product_id] = $catService->getCategoriesByProduct($p->product_id);
        }
        echo '<div class="wrap"><h1>محصولات</h1>';
        $this->renderNotice();
        $data = ['products' => $products, 'categoriesByProduct' => $categoriesByProduct];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Products/List.php';
        echo '</div>';
    }

    private function viewAdd(): void
    {
        echo '<div class="wrap"><h1>افزودن محصول</h1>';
        $this->renderNotice();
        $form = [];
        if (!empty($_GET['pfs_form'])) {
            $key = sanitize_key($_GET['pfs_form']);
            $form = get_transient('pfs_form_'.$key) ?: [];
            delete_transient('pfs_form_'.$key);
        }
        $catService = new CategoryService();
        $categories = $catService->list(200, 0);
        $data = ['form' => $form, 'categories' => $categories];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Products/Add.php';
        echo '</div>';
    }

    private function viewEdit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $service = new ProductService();
        $product = $id > 0 ? $service->getById($id) : null;
        echo '<div class="wrap"><h1>ویرایش محصول</h1>';
        $this->renderNotice();
        if ($product === null || is_wp_error($product)) {
            $msg = is_wp_error($product) ? $product->get_error_message() : 'شناسه محصول نامعتبر است';
            echo '<div class="alert alert-danger" role="alert"><p>'.esc_html($msg).'</p></div>';
            echo '<a href="'.esc_url(admin_url('admin.php?page=pfs-products')).'" class="btn btn-secondary">بازگشت</a>';
            echo '</div>';
            return;
        }
        $form = [];
        if (!empty($_GET['pfs_form'])) {
            $key = sanitize_key($_GET['pfs_form']);
            $form = get_transient('pfs_form_'.$key) ?: [];
            delete_transient('pfs_form_'.$key);
        }
        $catService = new CategoryService();
        $categories = $catService->list(200, 0);
        $selectedCats = array_map(fn($c) => $c->category_id, $catService->getCategoriesByProduct($product->product_id));
        $data = ['product' => $product, 'categories' => $categories, 'selectedCats' => $selectedCats, 'form' => $form];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Products/Edit.php';
        echo '</div>';
    }
}
