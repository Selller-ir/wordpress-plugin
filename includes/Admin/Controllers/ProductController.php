<?php

namespace Pfs\Admin\Controllers;

use Pfs\Domain\Products\ProductService;
use Pfs\Domain\Products\Product;

defined('ABSPATH') || exit;

class ProductController extends BaseController
{
    private ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->register_route();
    }
    protected function register_route() {
        $action = $_GET['action'] ?? 'index';
        if (method_exists($this, $action)) {
            $this->{$action}();
        } else {
            wp_die('دسترسی ندارید');
        }

    }




    /**
     * List products
     */
    public function index()
    {
        $products = $this->service->list();
        $this->loadViews('Products/Products.php', 'محصولات', [
            'products' => $products
        ]);
    }


    // public function show(int $id): Product
    // {
    //     return $this->service->show($id);
    // }

    /**
     * Store product
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->loadViews('Products/AddProduct.php','افزودن محصول جدید');
            return;
        }
        if (
            !isset($_POST['pfs_nonce']) ||
            !wp_verify_nonce($_POST['pfs_nonce'], 'pfs_create_product')
        ) {
            wp_die('درخواست نامعتبر');
        }

        $required = [
            'product_sku',
            'name',
            'status',
            'sale_price',
            'step_sale_quantity',
        ];

        foreach ($required as $field) {
            if (!isset($_POST[$field]) || $_POST[$field] === '') {
                $this->addError(0,"فیلد {$field} الزامی است");
            }
        }
        if (!$this->hasError()) {
            $product = new Product(
                sanitize_text_field($_POST['product_sku']),
                sanitize_text_field($_POST['name']),
                sanitize_key($_POST['status']),
                (int) $_POST['sale_price'],
                (float) $_POST['step_sale_quantity'],
                isset($_POST['purchase_price']) ? (int) $_POST['purchase_price'] : null,
                isset($_POST['consumer_price']) ? (int) $_POST['consumer_price'] : null,
                isset($_POST['image_id']) ? (int) $_POST['image_id'] : null,
                sanitize_text_field($_POST['unit'] ?? '')
            );

            $service = new ProductService();
            $result  = $service->create($product);
        }

        // var_dump($result);

        if (isset($result) && is_wp_error($result)) {
                $this->addError(
                    $result->get_error_code(),
                    $result->get_error_message()
            );
        }
        if ($this->hasError()) {
            $this->loadViews('Products/AddProduct.php','افزودن محصول جدید');
        }
        else {
            $this->addSucces('1','محصول با موفقیت اضافه شد');
            $this->index();
        }

    }

    /**
     * Update product
     */
    public function update(int $id, array $input): array
    {
        // if ($id <= 0) {
        //     return $this->error('Invalid product id');
        // }

        // try {
        //     $this->service->update($id, [
        //         'name'       => sanitize_text_field($input['name'] ?? ''),
        //         'sale_price' => (int) ($input['sale_price'] ?? 0),
        //         'status'     => sanitize_text_field($input['status'] ?? 'inactive'),
        //     ]);

        //     return $this->success();
        // } catch (\Throwable $e) {
        //     return $this->error($e->getMessage());
        // }
    }

    /**
     * Delete product
     */
    public function delete(int $id): array
    {
        // if ($id <= 0) {
        //     return $this->error('Invalid product id');
        // }

        // try {
        //     $this->service->delete($id);
        //     return $this->success();
        // } catch (\Throwable $e) {
        //     return $this->error($e->getMessage());
        // }
    }
}
