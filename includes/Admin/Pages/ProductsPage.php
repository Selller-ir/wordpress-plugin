<?php

namespace Pfs\Admin\Pages;

// use Pfs\Domain\Products\ProductService;
use Pfs\Admin\Controllers\ProductController;
use Pfs\Domain\Products\Product;

defined('ABSPATH') || exit;

class ProductsPage
{
    private ProductController $service;

    public function __construct()
    {
        $this->service = new ProductController();
    }

    public function render(): void
    {
        $action = $_GET['action'] ?? 'list';

        echo '<div class="wrap">';
        echo '<h1>محصولات</h1>';

        switch ($action) {
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    $this->postEdit();
                } else {
                    $this->renderEdit();
                }
                break;

            default:
                $this->renderList();
        }

        echo '</div>';
    }

    /**
     * Products list (DB driven)
     */
    private function renderList(): void
    {
        $products = $this->service->index();

        echo '<table class="widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Title</th>
                    <th>SKU</th>
                    <th width="120">Statuse</th>
                    <th width="60">Step Sale Quantity</th>
                    <th width="60">Unit</th>
                    <th width="120">Created At</th>
                    <th width="120">Purchase Price</th>
                    <th width="120">Consumer Price</th>
                    <th width="120">Sale Price</th>
                    <th width="60">Actions</th>
                </tr>
              </thead>';
        echo '<tbody>';

        if (empty($products)) {
            echo '<tr><td colspan="4">No products found.</td></tr>';
        }

        foreach ($products as $product) {
            echo '<tr>';

            echo '<td>' . esc_html($product->product_id) . '</td>';

            echo '<td>' . esc_html($product->name) . '</td>';

            echo '<td>' . esc_html($product->product_sku) . '</td>';

            echo '<td>
                    <span class="pfs-status pfs-status-' . esc_attr($product->status) . '">
                        ' . esc_html(ucfirst($product->status)) . '
                    </span>
                </td>';

            echo '<td>' . esc_html($product->step_sale_quantity) . '</td>';

            echo '<td>' . esc_html($product->unit ?? '-') . '</td>';

            echo '<td>' . esc_html(
                    wp_date(
                        'Y/m/d H:i',
                        strtotime($product->created_at)
                    )
                ) . '</td>';

            echo '<td>' . esc_html(number_format($product->purchase_price)) . '</td>';

            echo '<td>' . esc_html(number_format($product->consumer_price)) . '</td>';

            echo '<td>' . esc_html(number_format($product->sale_price)) . '</td>';

            echo '<td>
                    <a class="button button-small"
                    href="' . esc_url(
                            admin_url(
                                'admin.php?page=pfs-seller-products&action=edit&id=' . $product->product_id
                            )
                    ) . '">
                        Edit
                    </a>
                </td>';

            echo '</tr>';
        }


        echo '</tbody>';
        echo '</table>';
    }

    /**
     * Edit form (data loaded from service)
     */
private function renderEdit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            echo '<p>Invalid product.</p>';
            return;
        }

        $product = $this->service->show($id);

        if (!$product) {
            echo '<p>Product not found.</p>';
            return;
        }

        echo '<form method="post">';

        wp_nonce_field('pfs_update_product');

        echo '<input type="hidden" name="action" value="pfs_update_product">';
        echo '<input type="hidden" name="id" value="' . esc_attr($product->product_id) . '">';

        echo '<table class="form-table">';

        // Name
        echo '<tr>
                <th><label for="name">Product Name</label></th>
                <td>
                    <input type="text" id="name" name="name"
                        class="regular-text"
                        value="' . esc_attr($product->name) . '" required>
                </td>
            </tr>';

        // SKU
        echo '<tr>
                <th><label for="product_sku">SKU</label></th>
                <td>
                    <input type="text" id="product_sku" name="product_sku"
                        class="regular-text"
                        value="' . esc_attr($product->product_sku) . '" readonly>
                </td>
            </tr>';

        // Status
        echo '<tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="active" ' . selected($product->status, 'active', false) . '>Active</option>
                        <option value="inactive" ' . selected($product->status, 'inactive', false) . '>Inactive</option>
                    </select>
                </td>
            </tr>';

        // Purchase Price
        echo '<tr>
                <th><label for="purchase_price">Purchase Price</label></th>
                <td>
                    <input type="number" id="purchase_price" name="purchase_price"
                        value="' . esc_attr($product->purchase_price) . '">
                </td>
            </tr>';

        // Consumer Price
        echo '<tr>
                <th><label for="consumer_price">Consumer Price</label></th>
                <td>
                    <input type="number" id="consumer_price" name="consumer_price"
                        value="' . esc_attr($product->consumer_price) . '">
                </td>
            </tr>';

        // Sale Price
        echo '<tr>
                <th><label for="sale_price">Sale Price</label></th>
                <td>
                    <input type="number" id="sale_price" name="sale_price"
                        value="' . esc_attr($product->sale_price) . '" required>
                </td>
            </tr>';

        // Step Sale Quantity
        echo '<tr>
                <th><label for="step_sale_quantity">Step Sale Quantity</label></th>
                <td>
                    <input type="number" step="0.01" id="step_sale_quantity" name="step_sale_quantity"
                        value="' . esc_attr($product->step_sale_quantity) . '">
                </td>
            </tr>';

        // Unit
        echo '<tr>
                <th><label for="unit">Unit</label></th>
                <td>
                    <input type="text" id="unit" name="unit"
                        value="' . esc_attr($product->unit ?? '') . '">
                </td>
            </tr>';

        echo '</table>';

        submit_button(__('Update Product', 'pfs'));

        echo '</form>';
    }
    private function postEdit(): void {
        echo 'خطا در اپدیت';
        // TODO ....
    } 

}
