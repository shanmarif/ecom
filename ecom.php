<?php
/**
 * @package ECom
 */
/*
Plugin Name: Basic ECommerce Solution
Plugin URI: https://ule.ae/
Description: A Basic level ecommerce solution for your website
Author: Shan Arif
Author URI: - 
License: GPLv2 or later
Text Domain: ECom
*/

define('ECOM__FILE__', __FILE__);
define('ECOM_BASE', plugin_basename(ECOM__FILE__));

require_once 'functions.php';

function ecom_plugin_activate(){
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate;

    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_groups` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL,
        `description` TEXT NOT NULL,
        `slug` VARCHAR(75) NOT NULL DEFAULT '',
        `parent_id` INT(10) NOT NULL DEFAULT '0',
        `group_level` INT(10) NOT NULL DEFAULT '0',
        `image` TEXT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        INDEX `name` (`name`)
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_products` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(150) NOT NULL,
        `slug` VARCHAR(150) NOT NULL,
        `group_id` INT NOT NULL,
        `type` VARCHAR(150) NOT NULL,
        `image` TEXT NULL,
        `secondary_image` TEXT NULL,
        `description` TEXT NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
        INDEX `product-name` (`name`),
        PRIMARY KEY (`id`)
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_product_pricing` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `product_id` INT NOT NULL DEFAULT 0,
        `package_id` INT NOT NULL,
        `price` FLOAT NOT NULL DEFAULT '0',
        `additonal_class_price` FLOAT NOT NULL DEFAULT '0',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        CONSTRAINT `prod-id` FOREIGN KEY (`product_id`) REFERENCES `wp_simple_ecom_products` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_addons` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(500) NOT NULL DEFAULT '',
        `group_id` INT NOT NULL DEFAULT 0,
        `classification` VARCHAR(50) NOT NULL DEFAULT '0',
        `description` TEXT NOT NULL,
        `image` TEXT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CurrENT_TIMESTAMP() ON UPDATE CurrENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        INDEX `addon-name` (`name`)
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_product_addon_relation` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `product_id` INT NOT NULL DEFAULT 0,
        `addon_id` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        INDEX `product_id` (`product_id`),
        INDEX `addon_id` (`addon_id`),
        CONSTRAINT `product-id` FOREIGN KEY (`product_id`) REFERENCES `wp_simple_ecom_products` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
        CONSTRAINT `addon-id` FOREIGN KEY (`addon_id`) REFERENCES `wp_simple_ecom_addons` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_packages` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(150) NOT NULL,
        `slug` VARCHAR(50) NOT NULL,
        `description` TEXT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`)
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_orders` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `payment_method` VARCHAR(50) NOT NULL DEFAULT '',
        `grand_total` FLOAT NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`)
    )".$charset_collate.";";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."simple_ecom_order_items` (
        `id` INT(10) NOT NULL AUTO_INCREMENT,
        `order_id` INT(10) NOT NULL DEFAULT '0',
        `pricing_id` INT(10) NOT NULL DEFAULT '0',
        `type` VARCHAR(150) NOT NULL DEFAULT 'trademark',
        `addon_ids` TEXT NULL,
        `name` VARCHAR(250) NULL,
        `email` VARCHAR(250) NULL,
        `phonenumber` VARCHAR(50) NULL,
        `notes` VARCHAR(50) NULL,
        `trademark_type` VARCHAR(50) NULL,
        `trademark_text` VARCHAR(250) NULL,
        `logo_file` VARCHAR(250) NULL DEFAULT NULL,
        `trademark_inuse` ENUM('Y','N') NULL DEFAULT 'N',
        `trademark_date` DATE NULL DEFAULT NULL,
        `additional_notes` TEXT NULL DEFAULT NULL,
        `total` INT(10) NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`) USING BTREE
    )".$charset_collate.";";
    dbDelta( $sql );
}

register_activation_hook(__FILE__,'ecom_plugin_activate');

add_action( 'rest_api_init', function() {
    register_rest_route('ecom/api', '/remove-from-cart', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'remove_item_from_cart'
    ]);
    register_rest_route('ecom/api', '/add-to-cart-additional-service', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'add_to_cart_additional_service'
    ]);
    register_rest_route('ecom/api', '/remove-from-cart-additional-service', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'remove_from_cart_additional_service'
    ]);
    register_rest_route('ipn', '/paypal', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'paypal_ipn'
    ]);
    register_rest_route('ipn', '/worldpay', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'worldpay_ipn'
    ]);
    register_rest_route('ipn', '/stripe', [
        'methods' => "POST",
        'permission_callback' => '__return_true',
        'callback' => 'stripe_ipn'
    ]);
});
add_action( 'plugins_loaded', function () {
    EcomAdmin::get_instance();
} );
/*
add_action( 'admin_menu',function(){
    add_menu_page( "Basic Ecommerce Solution", 
    'Ecommerce', 'manage_options', 
    'simple_ecom_controlpanel', 'control_panel_page', 
    ecom_icon_svg(), 99 );
    add_submenu_page('simple_ecom_controlpanel', 'General', 'General', 'manage_options', 'simple_ecom_controlpanel', 'control_panel_page');
    add_submenu_page('simple_ecom_controlpanel', 'Products', 'Products', 'manage_options', 'ecom_products', 'products_page');
    add_submenu_page('simple_ecom_controlpanel', 'Addons', 'Addons', 'manage_options', 'ecom_addons', 'addons_page');
});
*/
function ecom_icon_svg(){
    $svg = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
    viewBox="0 0 483.688 483.688" style="enable-background:new 0 0 483.688 483.688;" xml:space="preserve"><g><path d="M473.6,92.43c-8.7-10.6-21.9-16.5-35.6-16.5H140.7c-8.5,0-16.6,2.4-23.6,6.7l-15.2-53.1c-2.5-8.7-10.4-14.7-19.4-14.7H59.4
        H15.3c-8.4,0-15.3,6.8-15.3,15.3v1.6c0,8.4,6.8,15.3,15.3,15.3h57.8l29.5,104.3l40.6,143.9c-23.1,5.8-40.2,26.7-40.2,51.5
        c0,28.1,21.9,51.2,49.6,53c-2.3,6.6-3.4,13.9-2.8,21.4c2,25.4,22.7,45.9,48.1,47.6c30.3,2.1,55.6-22,55.6-51.8c0-6-1-11.7-2.9-17.1
        h60.8c-2.5,7.1-3.5,15-2.6,23.1c2.8,24.6,23.1,44,47.9,45.8c30.3,2.1,55.7-21.9,55.7-51.8c0-28.9-24-52-52.8-52H156.5
        c-9.9,0-18.3-7.7-18.7-17.5c-0.4-10.4,7.9-18.9,18.2-18.9h30.5h165.3h46.5c20.6,0,38.6-14.1,43.6-34.1l40.4-162.6
        C485.8,117.83,482.6,103.53,473.6,92.43z M360.5,399.73c9.4,0,17.1,7.7,17.1,17.1s-7.7,17.1-17.1,17.1s-17.1-7.7-17.1-17.1
        S351,399.73,360.5,399.73z M201.6,399.73c9.4,0,17.1,7.7,17.1,17.1s-7.7,17.1-17.1,17.1c-9.4,0-17.1-7.7-17.1-17.1
        C184.5,407.43,192.1,399.73,201.6,399.73z M138.8,151.13l-7.8-27.5c-1.2-4.2,0.5-7.3,1.7-8.8c1.1-1.5,3.7-4,8-4h32.6l8.9,40.4
        h-43.4V151.13z M148.6,185.93h41.2l8.2,37.4h-38.9L148.6,185.93z M186.5,293.53c-4.5,0-8.5-3-9.7-7.4l-7.9-28h36.7l7.8,35.3h-26.9
        V293.53z M273.6,293.53H249l-7.8-35.3h32.3v35.3H273.6z M273.6,223.33h-40l-8.2-37.4h48.2V223.33z M273.6,151.13h-55.8l-8.9-40.4
        h64.7V151.13z M336,293.53h-27.5v-35.3h34.9L336,293.53z M350.8,223.33h-42.3v-37.4h50.2L350.8,223.33z M308.5,151.13v-40.4h66
        l-8.5,40.4H308.5z M408.2,285.93c-1.1,4.5-5.1,7.7-9.8,7.7h-26.8l7.5-35.3h36L408.2,285.93z M423.7,223.33h-37.3l7.9-37.4H433
        L423.7,223.33z M448.5,123.23l-6.9,27.8h-40l8.5-40.4h28.6c4.3,0,6.8,2.4,7.9,3.9C447.8,116.03,449.6,119.13,448.5,123.23z"/></g></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode( $svg );
}