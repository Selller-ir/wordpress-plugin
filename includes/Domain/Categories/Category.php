<?php
namespace Pfs\Domain\Categories;

class Category
{
    public ?int $category_id = null;
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromDb(array $row): self
    {
        $category = new self($row['name']);
        $category->category_id = (int) $row['category_id'];
        return $category;
    }
}