<form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=pfs_product_store')); ?>" class="mt-3">
    <?php wp_nonce_field('pfs_create_product', 'pfs_nonce'); ?>
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Title</label>
                    <input type="text" name="name" id="name" class="form-control" required value="<?php echo esc_attr($form['name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="product_sku" class="form-label">SKU</label>
                    <input type="text" name="product_sku" id="product_sku" class="form-control" required value="<?php echo esc_attr($form['product_sku'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Product Image</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" name="image_id" id="pfs_image_id" value="<?php echo esc_attr($form['image_id'] ?? ''); ?>">
                        <button type="button" class="btn btn-outline-secondary" id="pfs_upload_image">Select Image</button>
                    </div>
                    <div id="pfs_image_preview" class="mt-2">
                        <?php
                        $image_id = $form['image_id'] ?? '';
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
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" <?php selected($form['status'] ?? '', 'active'); ?>>Active</option>
                        <option value="inactive" <?php selected($form['status'] ?? '', 'inactive'); ?>>InActive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="step_sale_quantity" class="form-label">Step Sale Quantity</label>
                    <input type="number" name="step_sale_quantity" id="step_sale_quantity" class="form-control" min="1" step="0.001" value="<?php echo esc_attr($form['step_sale_quantity'] ?? 1); ?>">
                </div>
                <div class="col-md-6">
                    <label for="unit" class="form-label">Unit</label>
                    <input type="text" name="unit" id="unit" class="form-control" value="<?php echo esc_attr($form['unit'] ?? ''); ?>" placeholder="kg / pcs">
                </div>
                <div class="col-md-12">
                    <label for="categories" class="form-label">Categories</label>
                    <select name="categories[]" id="categories" class="form-select" multiple>
                        <?php
                        $selectedCats = array_map('intval', $form['categories'] ?? []);
                        foreach ($categories as $category) {
                            $sel = in_array($category->category_id, $selectedCats, true) ? 'selected' : '';
                            echo '<option value="'.esc_attr($category->category_id).'" '.$sel.'>'.esc_html($category->name).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="purchase_price" class="form-label">Purchase Price</label>
                    <input type="number" name="purchase_price" id="purchase_price" step="0.01" class="form-control" value="<?php echo esc_attr($form['purchase_price'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="consumer_price" class="form-label">Consumer Price</label>
                    <input type="number" name="consumer_price" id="consumer_price" step="0.01" class="form-control" value="<?php echo esc_attr($form['consumer_price'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="sale_price" class="form-label">Sale Price</label>
                    <input type="number" name="sale_price" id="sale_price" step="0.01" class="form-control" value="<?php echo esc_attr($form['sale_price'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Add Product</button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=pfs-products')); ?>" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</form>
