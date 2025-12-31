<?php
namespace Pfs\Api\V1;

use WP_REST_Request;
use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductService;

class ProductsController extends BaseController
{
    public function register_routes(): void
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
    }

    public function store(WP_REST_Request $request)
    {
        $device = $this->auth($request);
        if(is_wp_error($device)) {
            return $this->error($device);
        }

        $required = ['product_sku','name','status','sale_price','step_sale_quantity'];

        foreach ($required as $field) {
            if ($request->get_param($field) === null) {
                return $this->error(new \WP_Error(
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
        $create = $service->create($product);
        if (is_wp_error($create)) {
            return $this->error($create);
        }
        return $this->success($service->show($create));
    }

    public function index(WP_REST_Request $request)
    {
        $device = $this->auth($request);
        if(is_wp_error($device)) {
            return $this->error($device);
        }
        $service = new ProductService();
        return $this->success($service->list());
    }
}
