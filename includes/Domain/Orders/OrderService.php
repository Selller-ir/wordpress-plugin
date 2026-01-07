<?php
namespace Pfs\Domain\Orders;

use WP_Error;
use Pfs\Domain\Capability\CapabilityRepository;
use Pfs\Domain\Categories\CategoryRepository;
use Pfs\Domain\OrderItems\OrderItemService;
use Pfs\Domain\OrderItems\OrderItem;

class OrderService
{
    private OrderRepository $repository;

    public function __construct()
    {
        $this->repository = new OrderRepository();
    }

    public function create(Order $order): int|WP_Error
    {
        $error = $this->validate($order);
        if ($error) {
            return $error;
        }

        try {
            return $this->repository->create($order);
        } catch (\Throwable $e) {
            return new WP_Error('db_error', $e->getMessage() ?: 'Failed to create order', ['status' => 500]);
        }
    }

    public function createWithItems(Order $order, array $items): int|WP_Error
    {
        $error = $this->validate($order);
        if ($error) {
            return $error;
        }

        foreach ($items as $i) {
            if (!isset($i['product_id'], $i['price'], $i['quantity'])) {
                return new WP_Error('invalid_item', 'آیتم نامعتبر است', ['status' => 422]);
            }
        }

        $capRepo = new CapabilityRepository();
        $catRepo = new CategoryRepository();

        foreach ($items as $i) {
            $categories = $catRepo->getCategoriesByProduct((int) $i['product_id']);
            $authorized = false;
            foreach ($categories as $c) {
                if ($capRepo->exists($order->device_id, (int) $c['category_id'], 'order')) {
                    $authorized = true;
                    break;
                }
            }
            if (!$authorized) {
                return new WP_Error(
                    'forbidden',
                    'دسترسی دستگاه به محصول مجاز نیست',
                    [
                        'status' => 403,
                        'product_id' => (int) $i['product_id']
                    ]
                );
            }
        }

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $orderId = $this->repository->create($order);

            $itemService = new OrderItemService();
            foreach ($items as $i) {
                $res = $itemService->addItem(new OrderItem(
                    $orderId,
                    (int) $i['product_id'],
                    (int) $i['price'],
                    (float) $i['quantity']
                ));

                if (is_wp_error($res)) {
                    $wpdb->query('ROLLBACK');
                    return $res;
                }
            }

            $wpdb->query('COMMIT');
            return $orderId;

        } catch (\Throwable $e) {
            $wpdb->query('ROLLBACK');
            return new WP_Error('db_error', $e->getMessage() ?: 'Failed to create order with items', ['status' => 500]);
        }
    }

    public function getById(int $orderId, int $deviceId): array|WP_Error
    {
        if ($orderId <= 0 || $deviceId <= 0) {
            return new WP_Error('invalid_params', 'پارامترهای نامعتبر', ['status' => 422]);
        }

        $row = $this->repository->findById($orderId, $deviceId);
        if (!$row) {
            return new WP_Error('not_found', 'سفارش یافت نشد', ['status' => 404]);
        }

        return $row;
    }

    public function list(array $args): array
    {
        return $this->repository->paginate($args);
    }

    private function validate(Order $order): ?WP_Error
    {
        if ($order->device_id <= 0) {
            return new WP_Error('invalid_device', 'شناسه دستگاه نامعتبر است', ['status' => 422]);
        }
        if ($order->total_price < 0 || $order->paid_price < 0) {
            return new WP_Error('invalid_price', 'مبالغ نامعتبر است', ['status' => 422]);
        }
        if (trim($order->status) === '') {
            return new WP_Error('invalid_status', 'وضعیت نامعتبر است', ['status' => 422]);
        }
        return null;
    }
}