<?php
namespace Pfs\Api;

class Api {

    public function register() {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes() {
        // new \Pfs\Rest\V1\OrdersController();
        new \Pfs\Api\V1\ProductsController();
        new \Pfs\Api\V1\ProductsController2();
        // new \Pfs\Rest\V1\PaymentsController();
    }
}
