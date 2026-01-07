<?php
echo '<form method="post" action="'.esc_url(admin_url('admin-post.php?action=pfs_product_update')).'" class="mt-3">';
wp_nonce_field('pfs_update_product', 'pfs_nonce');
echo '<input type="hidden" name="product_id" value="'.esc_attr($product->product_id).'">';
echo '<div class="card"><div class="card-body">';
echo '<div class="row g-3">';
echo '<div class="col-md-6">
        <label for="name" class="form-label">Title</label>
        <input type="text" name="name" id="name" class="form-control" required value="'.esc_attr($form['name'] ?? $product->name).'">
      </div>';
echo '<div class="col-md-6">
        <label for="product_sku" class="form-label">SKU</label>
        <input type="text" name="product_sku" id="product_sku" class="form-control" required value="'.esc_attr($form['product_sku'] ?? $product->product_sku).'">
      </div>';
echo '<div class="col-md-6">
        <label class="form-label">Product Image</label>
        <div class="d-flex align-items-center gap-2">
            <input type="hidden" name="image_id" id="pfs_image_id" value="'.esc_attr($form['image_id'] ?? ($product->image_id ?? '')).'">
            <button type="button" class="btn btn-outline-secondary" id="pfs_upload_image">Select Image</button>
        </div>
        <div id="pfs_image_preview" class="mt-2">';
if (!empty($form['image_id'] ?? ($product->image_id ?? ''))) {
    $imgId = (int) ($form['image_id'] ?? $product->image_id);
    echo wp_get_attachment_image($imgId, 'thumbnail', false, ['style' => 'max-width:150px;height:auto;']);
}
echo     '</div>
      </div>';
echo '<div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select">
            <option value="active" '.selected(($form['status'] ?? $product->status), 'active', false).'>Active</option>
            <option value="inactive" '.selected(($form['status'] ?? $product->status), 'inactive', false).'>InActive</option>
        </select>
      </div>';
echo '<div class="col-md-6">
        <label for="step_sale_quantity" class="form-label">Step Sale Quantity</label>
        <input type="number" name="step_sale_quantity" id="step_sale_quantity" class="form-control" min="1" step="0.001" value="'.esc_attr($form['step_sale_quantity'] ?? $product->step_sale_quantity).'">
      </div>';
echo '<div class="col-md-6">
        <label for="unit" class="form-label">Unit</label>
        <input type="text" name="unit" id="unit" class="form-control" value="'.esc_attr($form['unit'] ?? ($product->unit ?? '')).'" placeholder="kg / pcs">
      </div>';
echo '<div class="col-md-12">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" id="categories" class="form-select" multiple>';
$selected = isset($form['categories']) && is_array($form['categories'])
    ? array_map('intval', $form['categories'])
    : (array) $selectedCats;
foreach ($categories as $category) {
    $sel = in_array($category->category_id, $selected, true) ? 'selected' : '';
    echo '<option value="'.esc_attr($category->category_id).'" '.$sel.'>'.esc_html($category->name).'</option>';
}
echo    '</select>
      </div>';
echo '<div class="col-md-4">
        <label for="purchase_price" class="form-label">Purchase Price</label>
        <input type="number" name="purchase_price" id="purchase_price" step="0.01" class="form-control" value="'.esc_attr($form['purchase_price'] ?? ($product->purchase_price ?? '')).'" required>
      </div>';
echo '<div class="col-md-4">
        <label for="consumer_price" class="form-label">Consumer Price</label>
        <input type="number" name="consumer_price" id="consumer_price" step="0.01" class="form-control" value="'.esc_attr($form['consumer_price'] ?? ($product->consumer_price ?? '')).'" required>
      </div>';
echo '<div class="col-md-4">
        <label for="sale_price" class="form-label">Sale Price</label>
        <input type="number" name="sale_price" id="sale_price" step="0.01" class="form-control" value="'.esc_attr($form['sale_price'] ?? $product->sale_price).'" required>
      </div>';
echo '</div>';
echo '<div class="mt-3 d-flex gap-2">';
echo '<button type="submit" class="btn btn-primary">ذخیره</button>';
echo '<a href="'.esc_url(admin_url('admin.php?page=pfs-products')).'" class="btn btn-secondary">بازگشت</a>';
echo '</div>';
echo '</div></div>';
echo '</form>';
