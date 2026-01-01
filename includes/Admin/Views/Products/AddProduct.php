<form method="post" action="<?php echo esc_url(admin_url('admin.php?page=pfs-seller-products&action=store')); ?>">

    <?php wp_nonce_field('pfs_create_product', 'pfs_nonce'); ?>

    <table class="form-table">
        <tbody>

        <!-- Title -->
        <tr>
            <th scope="row"><label for="name">Title</label></th>
            <td>
                <input type="text" name="name" id="name"
                       class="regular-text" required
                       value="<?php echo esc_attr($_POST['name'] ?? ''); ?>">
            </td>
        </tr>

        <!-- SKU -->
        <tr>
            <th scope="row"><label for="product_sku">SKU</label></th>
            <td>
                <input type="text" name="product_sku" id="product_sku"
                       class="regular-text" required
                       value="<?php echo esc_attr($_POST['product_sku'] ?? ''); ?>">
            </td>
        </tr>

        <!-- Product Image -->
        <tr>
            <th scope="row">Product Image</th>
            <td>
                <!-- hidden field -->
                <input type="hidden" name="image_id" id="pfs_image_id"
                       value="<?php echo esc_attr($_POST['image_id'] ?? ''); ?>">

                <!-- Button انتخاب تصویر -->
                <button type="button" class="button" id="pfs_upload_image">
                    Select Image
                </button>

                <!-- Preview -->
                <div id="pfs_image_preview" style="margin-top:10px;">
                    <?php
                    $image_id = $_POST['image_id'] ?? '';
                    if ($image_id) {
                        echo wp_get_attachment_image(
                            (int) $image_id,
                            'thumbnail',
                            false,
                            ['style' => 'max-width:150px;height:auto;']
                        );
                    }
                    ?>
                </div>
            </td>
        </tr>

        <!-- Status -->
        <tr>
            <th scope="row"><label for="status">Status</label></th>
            <td>
                <select name="status" id="status">
                    <option value="active" <?php selected($_POST['status'] ?? '', 'active'); ?>>Active</option>
                    <option value="inactive" <?php selected($_POST['status'] ?? '', 'inactive'); ?>>InActive</option>
                </select>
            </td>
        </tr>

        <!-- Step Sale Quantity -->
        <tr>
            <th scope="row"><label for="step_sale_quantity">Step Sale Quantity</label></th>
            <td>
                <input type="number" name="step_sale_quantity"
                       id="step_sale_quantity" min="1"
                       value="<?php echo esc_attr($_POST['step_sale_quantity'] ?? 1); ?>">
            </td>
        </tr>

        <!-- Unit -->
        <tr>
            <th scope="row"><label for="unit">Unit</label></th>
            <td>
                <input type="text" name="unit" id="unit"
                       class="small-text"
                       value="<?php echo esc_attr($_POST['unit'] ?? ''); ?>"
                       placeholder="kg / pcs">
            </td>
        </tr>

        <!-- Purchase Price -->
        <tr>
            <th scope="row"><label for="purchase_price">Purchase Price</label></th>
            <td>
                <input type="number" name="purchase_price"
                       id="purchase_price" step="0.01"
                       value="<?php echo esc_attr($_POST['purchase_price'] ?? ''); ?>"
                       required>
            </td>
        </tr>

        <!-- Consumer Price -->
        <tr>
            <th scope="row"><label for="consumer_price">Consumer Price</label></th>
            <td>
                <input type="number" name="consumer_price"
                       id="consumer_price" step="0.01"
                       value="<?php echo esc_attr($_POST['consumer_price'] ?? ''); ?>"
                       required>
            </td>
        </tr>

        <!-- Sale Price -->
        <tr>
            <th scope="row"><label for="sale_price">Sale Price</label></th>
            <td>
                <input type="number" name="sale_price"
                       id="sale_price" step="0.01"
                       value="<?php echo esc_attr($_POST['sale_price'] ?? ''); ?>"
                       required>
            </td>
        </tr>

        </tbody>
    </table>

    <?php submit_button('Add Product'); ?>

</form>