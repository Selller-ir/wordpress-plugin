<?php
namespace Pfs\Domain\Categories;

use Pfs\Domain\Categories\Category;
use WP_Error;

class CategoryService
{
    private CategoryRepository $repository;

    public function __construct()
    {
        $this->repository = new CategoryRepository();
    }

    public function create(string $name): int|WP_Error
    {
        $name = trim($name);
        if ($name === '') {
            return new WP_Error('invalid_name', 'نام دسته‌بندی الزامی است', ['status' => 422]);
        }

        if ($this->repository->existsByName($name)) {
            return new WP_Error('duplicate_name', 'این نام قبلاً ثبت شده است', ['status' => 409]);
        }

        $category = new Category($name);
        return $this->repository->insert($category);
    }

    public function update(int $id, string $name): bool|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error('invalid_id', 'شناسه دسته‌بندی نامعتبر است', ['status' => 422]);
        }

        $name = trim($name);
        if ($name === '') {
            return new WP_Error('invalid_name', 'نام دسته‌بندی الزامی است', ['status' => 422]);
        }

        if ($this->repository->existsByName($name, $id)) {
            return new WP_Error('duplicate_name', 'این نام قبلاً ثبت شده است', ['status' => 409]);
        }

        $category = new Category($name);
        $category->category_id = $id;
        return $this->repository->update($category);
    }

    public function delete(int $id): bool|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error('invalid_id', 'شناسه دسته‌بندی نامعتبر است', ['status' => 422]);
        }

        return $this->repository->delete($id);
    }

    public function getById(int $id): Category|WP_Error
    {
        if ($id <= 0) {
            return new WP_Error('invalid_id', 'شناسه دسته‌بندی نامعتبر است', ['status' => 422]);
        }

        $row = $this->repository->findById($id);
        if (!$row) {
            return new WP_Error('not_found', 'دسته‌بندی یافت نشد', ['status' => 404]);
        }

        return Category::fromDb($row);
    }

    public function list(int $limit = 50, int $offset = 0): array
    {
        $rows = $this->repository->findAll($limit, $offset);
        return array_map(fn(array $row) => Category::fromDb($row), $rows);
    }

    public function assignProduct(int $category_id, int $product_id): int|WP_Error
    {
        if ($category_id <= 0 || $product_id <= 0) {
            return new WP_Error('invalid_params', 'پارامترهای نامعتبر', ['status' => 422]);
        }

        return $this->repository->assignProduct($product_id, $category_id);
    }

    public function removeProduct(int $category_id, int $product_id): bool|WP_Error
    {
        if ($category_id <= 0 || $product_id <= 0) {
            return new WP_Error('invalid_params', 'پارامترهای نامعتبر', ['status' => 422]);
        }

        return $this->repository->removeProduct($product_id, $category_id);
    }

    public function getCategoriesByProduct(int $product_id): array
    {
        return array_map(
            fn(array $row) => Category::fromDb($row),
            $this->repository->getCategoriesByProduct($product_id)
        );
    }
}