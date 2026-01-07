<?php

namespace Pfs\Api\V1;

use WP_REST_Request;
use WP_Error;
use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductService;

class ProductsController2 extends BaseController
{
    protected function register_routes(): void
    {
        register_rest_route('pfs/v1', '/products2', [
            [
                'methods'  => 'POST',
                'callback' => [$this, 'store'],
            ],
            [
                'methods'  => 'GET',
                'callback' => [$this, 'index'],
            ],
        ]);

        register_rest_route('pfs/v1', '/products2/page/(?P<page>\d+)', [
            [
                'methods'  => 'GET',
                'callback' => [$this, 'indexPage'],
            ],
        ]);

        register_rest_route('pfs/v1', '/products2/(?P<id>\d+)', [
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

        register_rest_route('pfs/v1', '/products2/by-sku/(?P<sku>[^/]+)', [
            [
                'methods'  => 'GET',
                'callback' => [$this, 'showBySku'],
            ],
        ]);

        register_rest_route('pfs/v1', '/products2/for-order', [
            [
                'methods'  => 'GET',
                'callback' => [$this, 'indexForOrder'],
            ],
        ]);
    }

    private function getDevice(WP_REST_Request $request)
    {
        return \Pfs\Api\V1\Auth\DeviceAuth::check($request);
    }

    public function store(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        foreach ([
            'product_sku',
            'name',
            'status',
            'sale_price',
            'step_sale_quantity',
        ] as $field) {
            if ($request->get_param($field) === null) {
                return $this->error(new WP_Error('validation_error', "فیلد {$field} الزامی است", ['status' => 422]));
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

        return $this->success($service->getById($result));
    }

    public function index(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $limit  = (int) ($request->get_param('limit') ?? 20);
        $offset = (int) ($request->get_param('offset') ?? 0);
        $category_id = $request->get_param('category_id') !== null ? (int) $request->get_param('category_id') : null;

        $device = $this->getDevice($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $service = new ProductService();
        $result = $service->listForOrder($device->device_id, $category_id, $limit, $offset);
        return $this->success($result, [
            'limit'  => $limit,
            'offset' => $offset,
            'count'  => count($result),
        ]);
    }

    public function indexPage(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $page  = max(1, (int) $request->get_param('page'));
        $limit = (int) ($request->get_param('limit') ?? 20);
        $offset = ($page - 1) * $limit;
        $category_id = $request->get_param('category_id') !== null ? (int) $request->get_param('category_id') : null;

        $device = $this->getDevice($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $service = new ProductService();
        $result = $service->listForOrder($device->device_id, $category_id, $limit, $offset);
        return $this->success($result, [
            'limit'  => $limit,
            'page'   => $page,
            'offset' => $offset,
            'count'  => count($result),
        ]);
    }

    public function indexForOrder(WP_REST_Request $request)
    {
        return $this->index($request);
    }

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

    public function showBySku(WP_REST_Request $request)
    {
        $auth = $this->auth($request);
        if (is_wp_error($auth)) {
            return $this->error($auth);
        }

        $sku = (string) $request->get_param('sku');
        $service = new ProductService();
        $product = $service->getBySku($sku);

        if (is_wp_error($product)) {
            return $this->error($product);
        }

        return $this->success($product);
    }

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
            'product_sku','name','status','sale_price','step_sale_quantity','purchase_price','consumer_price','image_path','unit'
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
        return $this->success($service->getById($id));
    }

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

        return $this->success(['deleted' => true, 'id' => $id]);
    }
}