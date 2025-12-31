<?php
namespace Pfs\Api\V1\Auth;

use WP_Error;
use Pfs\Domain\Devices\DeviceService;
use WP_REST_Request;

class DeviceAuth {

    public static function check(?WP_REST_Request $request = null) {

        if ( defined('DOING_CRON') && DOING_CRON ) {
            return new WP_Error(
                'cron_context',
                'REST API not allowed in cron',
                ['status' => 403]
            );
        }

        if ($request) {
            $token = $request->get_header('HTTP_X_PFS_DEVICE_TOKEN');
        }
        // $token = $_GET['token'] ?? null;

        if ( empty($token) ) {
            return new WP_Error(
                'missing_device_token',
                'X-PFS-DEVICE-TOKEN header required',
                ['status' => 401]
            );
        }

        $serv   = new DeviceService();
        return $serv->authenticate($token);
    }
}
