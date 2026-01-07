<?php
echo '<div class="d-flex justify-content-between align-items-center mb-3">';
echo '<a class="btn btn-success" href="'.esc_url(admin_url('admin.php?page=pfs-products&action=add')).'">افزودن محصول</a>';
echo '</div>';

echo '<div class="table-responsive">';
echo '<table class="table table-striped table-hover align-middle">';
echo '<thead class="table-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">SKU</th>
            <th scope="col">Status</th>
            <th scope="col">Step Sale Quantity</th>
            <th scope="col">Unit</th>
            <th scope="col">Created At</th>
            <th scope="col">Purchase Price</th>
            <th scope="col">Consumer Price</th>
            <th scope="col">Sale Price</th>
            <th scope="col">Categories</th>
            <th scope="col">Image</th>
            <th scope="col">Actions</th>
        </tr>
      </thead>';
echo '<tbody>';

if (empty($products)) {
    echo '<tr><td colspan="12" class="text-center">No products found.</td></tr>';
}

foreach ($products as $product) {
    echo '<tr>';
    echo '<td>' . esc_html($product->product_id) . '</td>';
    echo '<td><strong>' . esc_html($product->name) . '</strong></td>';
    echo '<td><span class="badge bg-info text-dark">' . esc_html($product->product_sku) . '</span></td>';
    $statusClass = ($product->status === 'active') ? 'bg-success' : 'bg-secondary';
    echo '<td><span class="badge '.$statusClass.'">' . esc_html(ucfirst($product->status)) . '</span></td>';
    echo '<td>' . esc_html(number_format((float)$product->step_sale_quantity, 3)) . '</td>';
    echo '<td>' . esc_html($product->unit ?? '-') . '</td>';
    echo '<td>' . esc_html(wp_date('Y/m/d H:i', strtotime($product->created_at))) . '</td>';
    echo '<td>' . (isset($product->purchase_price) ? esc_html(number_format((float)$product->purchase_price, 2)) : '-') . '</td>';
    echo '<td>' . (isset($product->consumer_price) ? esc_html(number_format((float)$product->consumer_price, 2)) : '-') . '</td>';
    echo '<td>' . esc_html(number_format((float)$product->sale_price, 2)) . '</td>';
    echo '<td>';
    $cats = $categoriesByProduct[$product->product_id] ?? [];
    if (!empty($cats)) {
        foreach ($cats as $c) {
            echo '<span class="badge bg-light text-dark border me-1">'.esc_html($c->name).'</span>';
        }
    } else {
        echo '-';
    }
    echo '</td>';
    echo '<td>';
    if (!empty($product->image_id)) {
        echo wp_get_attachment_image((int) $product->image_id, 'thumbnail', false, ['style' => 'max-width:60px;height:auto;']);
    } else {
        echo '-';
    }
    echo '</td>';
    echo '<td>
            <div class="btn-group">
                <a class="btn btn-sm btn-outline-primary"
                   href="' . esc_url(admin_url('admin.php?page=pfs-products&action=edit&id=' . $product->product_id)) . '">ویرایش</a>
            </div>
          </td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
