<?php
echo '<div class="table-responsive">';
echo '<table class="table table-striped table-bordered align-middle">';
echo '<thead class="table-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
        </tr>
      </thead>';
echo '<tbody>';
if (empty($categories)) {
    echo '<tr><td colspan="2" class="text-center">No categories found.</td></tr>';
}
foreach ($categories as $category) {
    echo '<tr>';
    echo '<td>' . esc_html($category->category_id) . '</td>';
    echo '<td>' . esc_html($category->name) . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
