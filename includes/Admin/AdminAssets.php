<?php
namespace Pfs\Admin;

class AdminAssets
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style(
            'bootstrap-5',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            [],
            '5.3.2'
        );

        wp_enqueue_style(
            'pfs-seller-admin',
            PFS_ASSETS . 'css/admin-menu.css',
            [],
            '1.0.0'
        );

        wp_enqueue_media();

        wp_enqueue_script(
            'bootstrap-5',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.2',
            true
        );
        wp_enqueue_script(
            'pfs-media',
            PFS_ASSETS . 'js/media.js',
            ['jquery'],
            false,
            true
        );
    }
}
