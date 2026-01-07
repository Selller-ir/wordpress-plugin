<?php
echo '<div class="d-flex align-items-center mb-3">';
echo '<form method="post" action="'.esc_url(admin_url('admin-post.php?action=pfs_device_create')).'" class="d-flex gap-2">';
echo '<input type="text" name="name" class="form-control" placeholder="نام دستگاه" required style="max-width:300px">';
echo '<button type="submit" class="btn btn-primary">افزودن دستگاه</button>';
echo '</form>';
echo '</div>';

echo '<div class="table-responsive">';
echo '<table class="table table-striped table-bordered align-middle">';
echo '<thead class="table-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Status</th>
            <th scope="col">Token</th>
            <th scope="col">Actions</th>
        </tr>
      </thead>';
echo '<tbody>';
if (empty($devices)) {
    echo '<tr><td colspan="5" class="text-center">No devices found.</td></tr>';
}
foreach ($devices as $device) {
    $statusClass = ($device->status === 'active') ? 'bg-success' : 'bg-secondary';
    echo '<tr>';
    echo '<td>' . esc_html($device->device_id) . '</td>';
    echo '<td>' . esc_html($device->name) . '</td>';
    echo '<td><span class="badge '.$statusClass.'">' . esc_html(ucfirst($device->status)) . '</span></td>';
    echo '<td><code>' . esc_html($device->token) . '</code></td>';
    echo '<td>
            <a class="btn btn-sm btn-outline-secondary"
               href="' . esc_url(admin_url('admin.php?page=pfs-devices&action=detail&id=' . $device->device_id)) . '">
                جزئیات
            </a>
          </td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
