<?php
namespace PFS\Admin;

class AdminAssets
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style(
            'pfs-seller-admin',
            PFS_ASSETS . 'css/admin-menu.css',
            [],
            '1.0.0'
        );
    }
}
