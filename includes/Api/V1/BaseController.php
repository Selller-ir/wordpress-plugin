<?php
namespace Pfs\Api\V1;

use WP_Error;
use Pfs\Api\V1\Auth\DeviceAuth;
use WP_REST_Request;

abstract class BaseController {
    private $device;

    public function __construct() {
        $this->register_routes();
    }

    abstract protected function register_routes();

    /**
     * احراز هویت device
     */
    protected function auth(WP_REST_Request $request) {
        $device = DeviceAuth::check($request);
        if (is_wp_error($device)) {
            return $device;
        }
        $this->device = $device;
    }

    /**
     * پاسخ موفق
     */
    protected function success($data, array $meta = []) {
        return rest_ensure_response([
            'success' => true,
            'data'    => $data,
            'meta'    => array_merge([
                'timestamp' => time(),
            ], $meta),
        ]);
    }

    /**
     * پاسخ خطا
     */
    protected function error(WP_Error $error) {
        $status = 400;

        $data = $error->get_error_data();
        if (is_array($data) && isset($data['status'])) {
            $status = (int) $data['status'];
        }
    $response = rest_ensure_response([
        'success' => false,
        'error'   => [
            'code'    => $error->get_error_code(),
            'message' => $error->get_error_message(),
            'details' => $data,
        ],
    ]);

    if ($response instanceof \WP_REST_Response) {
        $response->set_status($status);
    }

    return $response;
    }
}

