<?php
namespace Pfs\Database\Migrations;

class Migration_1_0_0 {

    public function up() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        /*
         * DEVICES
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_devices (
                device_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                status VARCHAR(20) NOT NULL,
                token VARCHAR(100) NOT NULL,
                PRIMARY KEY (device_id),
                UNIQUE KEY token (token)
            ) $charset;
        ");

        /*
         * USERS
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_users (
                user_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255),
                phone_number VARCHAR(20),
                status VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY (user_id)
            ) $charset;
        ");

        /*
         * PRODUCTS
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_products (
                product_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_sku VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                image_path VARCHAR(255),
                status VARCHAR(20) NOT NULL,
                purchase_price BIGINT NOT NULL,
                consumer_price BIGINT NOT NULL,
                sale_price BIGINT NOT NULL,
                step_sale_quantity INT NOT NULL DEFAULT 1,
                unit VARCHAR(20),
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (product_id),
                UNIQUE KEY product_sku (product_sku)
            ) $charset;
        ");

        /*
         * CATEGORIES
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_categories (
                category_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY (category_id)
            ) $charset;
        ");

        /*
         * PRODUCT_CATEGORIES
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_product_categories (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id BIGINT UNSIGNED NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                KEY product_id (product_id),
                KEY category_id (category_id)
            ) $charset;
        ");

        /*
         * CAPABILITY
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_capability (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                device_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                cap VARCHAR(50) NOT NULL,
                PRIMARY KEY (id),
                KEY device_id (device_id),
                KEY product_id (product_id)
            ) $charset;
        ");

        /*
         * ORDERS
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_orders (
                order_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                device_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                total_price BIGINT NOT NULL,
                paid_price BIGINT NOT NULL,
                status VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY (order_id),
                KEY device_id (device_id),
                KEY user_id (user_id),
                KEY created_at (created_at)
            ) $charset;
        ");

        /*
         * ORDER_ITEMS
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_order_items (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                price BIGINT NOT NULL,
                quantity INT NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                KEY order_id (order_id),
                KEY product_id (product_id)
            ) $charset;
        ");

        /*
         * PAYMENTS
         */
        dbDelta("
            CREATE TABLE {$wpdb->prefix}pfs_payments (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id BIGINT UNSIGNED NOT NULL,
                device_id BIGINT UNSIGNED NOT NULL,
                amount BIGINT NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                status VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                KEY order_id (order_id),
                KEY device_id (device_id),
                KEY created_at (created_at)
            ) $charset;
        ");
    }
}
