<?php
namespace Pfs\Rest\V1;

use WP_REST_Request;
use WP_Error;
use Pfs\Domain\Products\Product;
use Pfs\Domain\Products\ProductRepository;

class ProductsController extends BaseController {

    protected function register_routes() {

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
                'methods'  => 'DELETE',
                'callback' => [$this, 'destroy'],
            ],
        ]);
    }

    /**
     * ایجاد محصول
     */
    public function store(WP_REST_Request $request) {

        $device = $this->auth($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $data = $request->get_json_params();

        if (empty($data['name']) || empty($data['product_sku'])) {
            return $this->error(
                new WP_Error('invalid_data', 'name و product_sku الزامی است', ['status' => 422])
            );
        }

        $product = new Product($data);

        try {
            $repo = new ProductRepository();
            $productId = $repo->create($product);
        } catch (\Throwable $e) {
            return $this->error(
                new WP_Error('product_create_failed', $e->getMessage(), ['status' => 500])
            );
        }

        return $this->success(
            $repo->findById($productId)
        );
    }

    /**
     * لیست محصولات
     */
    public function index(WP_REST_Request $request) {

        $device = $this->auth($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $limit  = (int) ($request->get_param('limit') ?? 50);
        $offset = (int) ($request->get_param('offset') ?? 0);

        $repo = new ProductRepository();
        $products = $repo->paginate($limit, $offset);

        return $this->success($products, [
            'count' => count($products),
        ]);
    }

    /**
     * مشاهده محصول
     */
    public function show(WP_REST_Request $request) {

        $device = $this->auth($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $productId = (int) $request['id'];

        $repo = new ProductRepository();
        $product = $repo->findById($productId);

        if (!$product || $product['status'] === 'inactive') {
            return $this->error(
                new WP_Error('product_not_found', 'Product not found', ['status' => 404])
            );
        }

        return $this->success($product);
    }

    /**
     * ویرایش محصول
     */
    public function update(WP_REST_Request $request) {

        $device = $this->auth($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $productId = (int) $request['id'];
        $data = $request->get_json_params();

        $repo = new ProductRepository();

        if (!$repo->findById($productId)) {
            return $this->error(
                new WP_Error('product_not_found', 'Product not found', ['status' => 404])
            );
        }

        try {
            $repo->update($productId, $data);
        } catch (\Throwable $e) {
            return $this->error(
                new WP_Error('product_update_failed', $e->getMessage(), ['status' => 500])
            );
        }

        return $this->success(
            $repo->findById($productId)
        );
    }

    /**
     * حذف (غیرفعال‌سازی) محصول
     */
    public function destroy(WP_REST_Request $request) {

        $device = $this->auth($request);
        if (is_wp_error($device)) {
            return $this->error($device);
        }

        $productId = (int) $request['id'];

        $repo = new ProductRepository();

        if (!$repo->findById($productId)) {
            return $this->error(
                new WP_Error('product_not_found', 'Product not found', ['status' => 404])
            );
        }

        $repo->disable($productId);

        return $this->success([
            'product_id' => $productId,
            'status'     => 'inactive',
        ]);
    }
}
