<?php
namespace Pfs\Admin\Controllers;

use Pfs\Domain\Devices\DeviceService;
use Pfs\Domain\Capability\CapabilityService;
use Pfs\Domain\Categories\CategoryService;
use Pfs\Domain\Products\ProductService;

class DevicesController
{
    public function registerActions(): void
    {
        add_action('admin_post_pfs_device_update', [$this, 'update']);
        add_action('admin_post_pfs_device_cap_grant', [$this, 'capGrant']);
        add_action('admin_post_pfs_device_cap_revoke', [$this, 'capRevoke']);
        add_action('admin_post_pfs_device_create', [$this, 'create']);
    }

    public function render(): void
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : 'index';
        if ($action === 'detail') {
            $this->viewDetail();
            return;
        }
        $this->viewIndex();
    }

    public function create(): void
    {
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $service = new DeviceService();
        $res = $service->create($name);
        $url = admin_url('admin.php?page=pfs-devices');
        if (is_wp_error($res)) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $res->get_error_message()], $url));
        } else {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], $url));
        }
        exit;
    }

    public function update(): void
    {
        $id = isset($_POST['device_id']) ? (int) $_POST['device_id'] : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $status = isset($_POST['status']) ? sanitize_key($_POST['status']) : '';
        $service = new DeviceService();
        $res = $service->update($id, ['name' => $name, 'status' => $status]);
        $url = admin_url('admin.php?page=pfs-devices&action=detail&id=' . $id);
        if (is_wp_error($res)) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $res->get_error_message()], $url));
        } else {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], $url));
        }
        exit;
    }

    public function capGrant(): void
    {
        $device_id = (int) ($_POST['device_id'] ?? 0);
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $cap = sanitize_key($_POST['cap'] ?? 'order');
        $service = new CapabilityService();
        $res = $service->grant($device_id, $category_id, $cap);
        $url = admin_url('admin.php?page=pfs-devices&action=detail&id=' . $device_id);
        if (is_wp_error($res)) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $res->get_error_message()], $url));
        } else {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], $url));
        }
        exit;
    }

    public function capRevoke(): void
    {
        $device_id = (int) ($_POST['device_id'] ?? 0);
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $cap = sanitize_key($_POST['cap'] ?? 'order');
        $service = new CapabilityService();
        $res = $service->revoke($device_id, $category_id, $cap);
        $url = admin_url('admin.php?page=pfs-devices&action=detail&id=' . $device_id);
        if (is_wp_error($res)) {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'error', 'pfs_error' => $res->get_error_message()], $url));
        } else {
            wp_safe_redirect(add_query_arg(['pfs_notice' => 'success'], $url));
        }
        exit;
    }

    private function renderNotice(): void
    {
        $notice = isset($_GET['pfs_notice']) ? (string) $_GET['pfs_notice'] : '';
        if ($notice === '') { return; }
        if ($notice === 'success') { echo '<div class="alert alert-success" role="alert"><p>عملیات موفق</p></div>'; return; }
        if ($notice === 'invalid') { echo '<div class="alert alert-danger" role="alert"><p>درخواست نامعتبر</p></div>'; return; }
        if ($notice === 'error') {
            $msg = isset($_GET['pfs_error']) ? sanitize_text_field($_GET['pfs_error']) : 'خطا در عملیات';
            echo '<div class="alert alert-danger" role="alert"><p>' . esc_html($msg) . '</p></div>';
            return;
        }
    }

    private function viewIndex(): void
    {
        $service = new DeviceService();
        $devices = $service->list();
        echo '<div class="wrap"><h1>دستگاه‌ها</h1>';
        $this->renderNotice();
        $data = ['devices' => $devices];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Devices/List.php';
        echo '</div>';
    }

    private function viewDetail(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $devService = new DeviceService();
        $devices = $devService->list();
        $device = null;
        foreach ($devices as $d) {
            if ($d->device_id === $id) { $device = $d; break; }
        }
        $capService = new CapabilityService();
        $caps = $capService->listCapByDevice($id);
        $catService = new CategoryService();
        $categories = $catService->list(200, 0);
        $prodService = new ProductService();
        $products = $prodService->listForOrder($id, null, 20, 0);

        echo '<div class="wrap"><h1>جزئیات دستگاه</h1>';
        $this->renderNotice();
        $data = [
            'device' => $device,
            'caps' => $caps,
            'categories' => $categories,
            'products' => $products
        ];
        extract($data);
        require PFS_ADMIN_VIEWS . 'Devices/Detail.php';
        echo '</div>';
    }
}
