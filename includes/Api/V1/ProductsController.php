<?php

namespace Pfs\Api\V1;

use WP_REST_Request;
use WP_Error;
use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductService;

class ProductsController extends BaseController
{
    protected function register_routes(): void
    {
        register_rest_route('pfs/v1', '/products', [
            [
                'methods'  => 'POST',
                'callback' => [$this, 'store'],
            ],
            [
                'methods'  => 'GET',
                'callback' => [$this, 'index'],
            ],
        ]);

        register_rest_route('pfs/v1', '/products/(?P<id>\d+)', [
            [
                'methods'  => 'GET',
                'callback' => [$this, 'show'],
            ],
            [
                'methods'  => 'PUT',
                'callback' => [$this, 'update'],
            ],
            [
                'methods'  => 'PATCH',
                'callback' => [$this, 'update'],
            ],
            [
                'methods'  => 'DELETE',
                'callback' => [$this, 'destroy'],
            ],
        ]);
    }

    /* =========================
     * CREATE
     * ========================= */

    public function store(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $required = [
            'product_sku',
            'name',
            'status',
            'sale_price',
            'step_sale_quantity',
        ];

        foreach ($required as $field) {
            if ($request->get_param($field) === null) {
                return $this->error(new WP_Error(
                    'validation_error',
                    "فیلد {$field} الزامی است",
                    ['status' => 422]
                ));
            }
        }

        $product = new Product(
            (string) $request->get_param('product_sku'),
            (string) $request->get_param('name'),
            (string) $request->get_param('status'),
            (int) $request->get_param('sale_price'),
            (float) $request->get_param('step_sale_quantity'),
            $request->get_param('purchase_price') !== null ? (int) $request->get_param('purchase_price') : null,
            $request->get_param('consumer_price') !== null ? (int) $request->get_param('consumer_price') : null,
            $request->get_param('image_path'),
            $request->get_param('unit')
        );

        $service = new ProductService();
        $result  = $service->create($product);

        if (is_wp_error($result)) {
            return $this->error($result);
        }

        return $this->success(
            $service->getById($result)
        );
    }

    /* =========================
     * LIST
     * ========================= */

    public function index(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $limit  = (int) ($request->get_param('limit') ?? 20);
        $offset = (int) ($request->get_param('offset') ?? 0);

        $service = new ProductService();
        $result = $service->list($limit, $offset);
        return $this->success(
            $result,
            [
                'limit'  => $limit,
                'offset' => $offset,
                'count' => count($result),
            ]
        );
    }

    /* =========================
     * SHOW
     * ========================= */

    public function show(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $id = (int) $request->get_param('id');

        $service = new ProductService();
        $product = $service->getById($id);

        if (is_wp_error($product)) {
            return $this->error($product);
        }

        return $this->success($product);
    }

    /* =========================
     * UPDATE
     * ========================= */

    public function update(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $id = (int) $request->get_param('id');

        $service  = new ProductService();
        $existing = $service->getById($id);

        if (is_wp_error($existing)) {
            return $this->error($existing);
        }

        foreach ([
            'product_sku',
            'name',
            'status',
            'sale_price',
            'step_sale_quantity',
            'purchase_price',
            'consumer_price',
            'image_path',
            'unit',
        ] as $field) {
            if ($request->get_param($field) !== null) {
                $existing->{$field} = $request->get_param($field);
            }
        }

        $existing->product_id = $id;

        $result = $service->update($existing);

        if (is_wp_error($result)) {
            return $this->error($result);
        }

        return $this->success(
            $service->getById($id)
        );
    }

    /* =========================
     * DELETE
     * ========================= */

    public function destroy(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $id = (int) $request->get_param('id');

        $service = new ProductService();
        $result  = $service->delete($id);

        if (is_wp_error($result)) {
            return $this->error($result);
        }

        return $this->success([
            'deleted' => true,
            'id'      => $id,
        ]);
    }
}
