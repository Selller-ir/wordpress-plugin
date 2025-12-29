<?php
namespace Pfs\Rest\Auth;

use WP_Error;
use Pfs\Domain\Devices\DeviceRepository;
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

        $repo   = new DeviceRepository();
        $device = $repo->findByToken($token);

        if (! $device || ! $device->isActive()) {
            return new WP_Error(
                'invalid_device',
                'Invalid or inactive device',
                ['status' => 403]
            );
        }

        return $device;
    }
}
