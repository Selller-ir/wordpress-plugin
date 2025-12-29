<?php
namespace Pfs\Rest;

class Api {

    public function register() {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes() {
        // new \Pfs\Rest\V1\OrdersController();
        new \Pfs\Rest\V1\ProductsController();
        // new \Pfs\Rest\V1\PaymentsController();
    }
}
