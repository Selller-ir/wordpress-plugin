<?php
namespace Pfs\Domain\OrderItems;

use WP_Error;

class OrderItemService
{
    private OrderItemRepository $repository;

    public function __construct()
    {
        $this->repository = new OrderItemRepository();
    }

    public function addItem(OrderItem $item): int|WP_Error
    {
        $error = $this->validate($item);
        if ($error) {
            return $error;
        }

        return $this->repository->insert($item);
    }

    public function updateItem(OrderItem $item): bool|WP_Error
    {
        if (!$item->id) {
            return new WP_Error('invalid_id', 'شناسه آیتم نامعتبر است', ['status' => 422]);
        }

        $error = $this->validate($item);
        if ($error) {
            return $error;
        }

        return $this->repository->update($item);
    }

    public function removeItem(int $id): bool|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error('invalid_id', 'شناسه آیتم نامعتبر است', ['status' => 422]);
        }
        return $this->repository->delete($id);
    }

    public function removeItemsByOrder(int $order_id): bool|WP_Error
    {
        if ($order_id <= 0) {
            return new WP_Error('invalid_order_id', 'شناسه سفارش نامعتبر است', ['status' => 422]);
        }
        return $this->repository->deleteByOrder($order_id);
    }

    public function getItem(int $id): OrderItem|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error('invalid_id', 'شناسه آیتم نامعتبر است', ['status' => 422]);
        }
        $row = $this->repository->findById($id);
        if (!$row) {
            return new WP_Error('not_found', 'آیتم یافت نشد', ['status' => 404]);
        }
        return OrderItem::fromDb($row);
    }

    public function listByOrder(int $order_id): array
    {
        $rows = $this->repository->findByOrder($order_id);
        return array_map(fn(array $row) => OrderItem::fromDb($row), $rows);
    }

    private function validate(OrderItem $item): ?WP_Error
    {
        if ($item->order_id <= 0 || $item->product_id <= 0) {
            return new WP_Error('invalid_params', 'پارامترهای نامعتبر', ['status' => 422]);
        }
        if ($item->price < 0) {
            return new WP_Error('invalid_price', 'قیمت نامعتبر است', ['status' => 422]);
        }
        if ($item->quantity <= 0) {
            return new WP_Error('invalid_quantity', 'تعداد باید بزرگتر از صفر باشد', ['status' => 422]);
        }
        if (round($item->quantity, 3) !== $item->quantity) {
            return new WP_Error('invalid_precision', 'حداکثر ۳ رقم اعشار مجاز است', ['status' => 422]);
        }
        return null;
    }
}