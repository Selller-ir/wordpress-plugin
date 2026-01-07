<?php
if (!$device) {
    echo '<div class="alert alert-danger" role="alert"><p>Device یافت نشد</p></div>';
    echo '<a href="'.esc_url(admin_url('admin.php?page=pfs-devices')).'" class="btn btn-secondary">بازگشت</a>';
    return;
}
echo '<div class="row g-4">';
echo '<div class="col-lg-6">';
echo '<div class="card"><div class="card-body">';
echo '<h5 class="card-title">ویرایش دستگاه</h5>';
echo '<form method="post" action="'.esc_url(admin_url('admin-post.php?action=pfs_device_update')).'" class="mt-2">';
echo '<input type="hidden" name="device_id" value="'.esc_attr($device->device_id).'">';
echo '<div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="'.esc_attr($device->name).'" required>
      </div>';
echo '<div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" '.selected($device->status, 'active', false).'>Active</option>
            <option value="inactive" '.selected($device->status, 'inactive', false).'>Inactive</option>
        </select>
      </div>';
echo '<div class="mb-3"><label class="form-label">Token</label><div><code>'.esc_html($device->token).'</code></div></div>';
echo '<button type="submit" class="btn btn-primary">ذخیره</button>';
echo '<a href="'.esc_url(admin_url('admin.php?page=pfs-devices')).'" class="btn btn-secondary ms-2">بازگشت</a>';
echo '</form>';
echo '</div></div>';
echo '</div>';

echo '<div class="col-lg-6">';
echo '<div class="card"><div class="card-body">';
echo '<h5 class="card-title">قابلیت سفارش بر اساس دسته‌بندی</h5>';
echo '<div class="list-group">';
$hasCap = [];
foreach ($caps as $c) { $hasCap[$c->category_id . ':' . $c->cap] = true; }
foreach ($categories as $category) {
    $enabled = !empty($hasCap[$category->category_id . ':order']);
    echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
    echo '<div>'.esc_html($category->name).'</div>';
    echo '<div>';
    if ($enabled) {
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php?action=pfs_device_cap_revoke')).'" class="d-inline">';
        echo '<input type="hidden" name="device_id" value="'.esc_attr($device->device_id).'">';
        echo '<input type="hidden" name="category_id" value="'.esc_attr($category->category_id).'">';
        echo '<input type="hidden" name="cap" value="order">';
        echo '<button type="submit" class="btn btn-outline-danger btn-sm">لغو دسترسی</button>';
        echo '</form>';
    } else {
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php?action=pfs_device_cap_grant')).'" class="d-inline">';
        echo '<input type="hidden" name="device_id" value="'.esc_attr($device->device_id).'">';
        echo '<input type="hidden" name="category_id" value="'.esc_attr($category->category_id).'">';
        echo '<input type="hidden" name="cap" value="order">';
        echo '<button type="submit" class="btn btn-outline-success btn-sm">اعطای دسترسی</button>';
        echo '</form>';
    }
    echo '</div>';
    echo '</div>';
}
echo '</div>';
echo '</div></div>';
echo '</div>';

echo '<div class="col-12">';
echo '<div class="card"><div class="card-body">';
echo '<h5 class="card-title">محصولات قابل سفارش برای این دستگاه</h5>';
echo '<div class="table-responsive">';
echo '<table class="table table-striped table-bordered align-middle">';
echo '<thead class="table-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">SKU</th>
            <th scope="col">Sale Price</th>
            <th scope="col">Status</th>
        </tr>
      </thead>';
echo '<tbody>';
if (empty($products)) {
    echo '<tr><td colspan="5" class="text-center">No products found.</td></tr>';
} else {
    foreach ($products as $product) {
        $statusClass = ($product->status === 'active') ? 'bg-success' : 'bg-secondary';
        echo '<tr>';
        echo '<td>' . esc_html($product->product_id) . '</td>';
        echo '<td>' . esc_html($product->name) . '</td>';
        echo '<td>' . esc_html($product->product_sku) . '</td>';
        echo '<td>' . esc_html(number_format($product->sale_price)) . '</td>';
        echo '<td><span class="badge '.$statusClass.'">' . esc_html(ucfirst($product->status)) . '</span></td>';
        echo '</tr>';
    }
}
echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div></div>';
echo '</div>';
echo '</div>';
