<?php
echo '<a class="button button-small" 
href="'.admin_url('admin.php?page=pfs-seller-products&action=store').'">افزودن محصول</a>';
        echo '<table class="widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th width="20">ID</th>
                    <th width="120">Title</th>
                    <th width="100">SKU</th>
                    <th width="100">Statuse</th>
                    <th width="60">Step Sale Quantity</th>
                    <th width="60">Unit</th>
                    <th width="100">Created At</th>
                    <th width="80">Purchase Price</th>
                    <th width="80">Consumer Price</th>
                    <th width="80">Sale Price</th>
                    <th width="80">Image</th>
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

            echo '<td>';
            if (!empty($product->image_path)) {
                echo wp_get_attachment_image(
                    $product->image_path,
                    'thumbnail',
                    false,
                    ['style' => 'max-width:60px;height:auto;']
                );
            } else {
                echo '-';
            }
            echo '</td>';

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