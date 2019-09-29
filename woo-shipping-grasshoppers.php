<?php

/**
 * Plugin Name: WooCommerce Grasshoppers Shipping
 * Plugin URI:
 * Description: WooCommerce Grasshoppers Shipping allows a store to obtain shipping rates for your orders dynamically via the Grasshoppers Shipping API.
 * Version: 1.0.0
 * WC requires at least: 2.6
 * WC tested up to: 3.6
 * Tested up to: 5.0
 * Author: Tharindu Wickramasinghe
 * Author URI:
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 * @package WC_Shipping_Grasshoppers
 */

defined('ABSPATH') or die();

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
} else {
    die('No autoload file for this plugin');
}

// set plugin directory path
define('GRASSHOPPERS_PLUGIN_DIR', dirname(__FILE__));

// define available shipping methods for grasshoppers
define('GRASSHOPPERS_SHIPPING_METHODS', array('Standard', 'Premium'));


use Grasshoppers\Inc\Woo_Shipping_Grasshoppers;

// check woocommerce plugin is activated
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    require_once WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

    $wooShippingGrasshoppers = new Woo_Shipping_Grasshoppers();

    register_activation_hook(__FILE__, array($wooShippingGrasshoppers, 'activate'));
    register_deactivation_hook(__FILE__, array($wooShippingGrasshoppers, 'deactivate'));
    register_uninstall_hook(__FILE__, array($wooShippingGrasshoppers, 'uninstall'));

    add_action('woocommerce_shipping_init', array($wooShippingGrasshoppers, 'register_shipping'));
    add_action('woocommerce_thankyou', array($wooShippingGrasshoppers, 'woocommerce_thankyou_action'), 10, 1);
    add_action('woocommerce_order_details_after_order_table',
        array($wooShippingGrasshoppers, 'woocommerce_order_details_after_order_table_action'));
    add_action('woocommerce_checkout_update_order_meta',
        array($wooShippingGrasshoppers, 'woocommerce_checkout_update_order_meta_action'));
}
