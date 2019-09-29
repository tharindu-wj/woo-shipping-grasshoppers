<?php
namespace Grasshoppers\Inc;

use Grasshoppers\Inc\API_Requests\Create_Delivery_Request;

/**
 * Plugin class for woo-shipping-grasshoppers
 * This contains all the plugin lifecycle methods and woocommerce hooked methods
 */
class Woo_Shipping_Grasshoppers
{
    /**
     * Woo_Shipping_Grasshoppers constructor.
     */

    public function __construct()
    {

    }

    /**
     * Function triggers when plugin activated
     */
    public function activate()
    {

    }

    /**
     * Function triggers when plugin deactivated
     */
    public function deactivate()
    {

    }

    /**
     * Function triggers when plugin uninstalled
     */
    public function uninstall()
    {

    }

    /**
     * Function triggers when woocommerce_shipping_init
     * Register grasshoppers shipping method
     */
    public function register_shipping()
    {
        add_filter('woocommerce_shipping_methods', array($this, 'add_grasshoppers_shipping_method'));
    }


    /**
     * Add grasshoppers shipping to woocommerce shipping methods
     *
     * @param $methods
     *
     * @return array
     */
    public function add_grasshoppers_shipping_method($methods)
    {
        $methods['grasshoppers'] = WC_Shipping_Grasshoppers::class;
        return $methods;
    }


    /**
     * Function triggers when customer redirected to thank you page
     * Send delivery request to grasshoppers API
     *
     * @param $order_id
     */
    public function woocommerce_thankyou_action($order_id)
    {
        $order = \wc_get_order($order_id);

        if ($this->is_grasshopper_shipping($order)) {
            $delivery_order = new Delivery_Order($order);
            $body = $delivery_order->get_delivery_order();

            $delivery_request = new Create_Delivery_Request();
            $delivery_request->setBody($body);
            $response = $delivery_request->request();

            $delivery_order->add_order_meta('_grasshopper_order_reference', $response['REFERENCE_NO']);

            if($response['IS_SUCCESS']){
                wc_add_notice('Grasshoppers notice: Your order will prepared for delivery.', 'success');
            }
        }
    }


    /**
     * Function triggers after order  detail table
     * Add grasshoppers order tracking in order details page
     *
     * @param $order
     */
    public function woocommerce_order_details_after_order_table_action($order)
    {
        if ($this->is_grasshopper_shipping($order)) {
            $order_tracking = new Order_Tracking($order);
            $tracking_data = $order_tracking->get_tracking_data();
            include GRASSHOPPERS_PLUGIN_DIR . "/templates/order-tracking.php";
        }
    }


    /**
     * Check grasshoppers shipping method available for an order
     *
     * @param $order
     * @return bool
     */
    private function is_grasshopper_shipping($order)
    {
        if (in_array($order->get_shipping_method(), GRASSHOPPERS_SHIPPING_METHODS)) {
            return true;
        }
        return false;
    }


    /**
     * Function triggers after order placed
     * Add order weight to order meta
     *
     * @param $order_id
     */
    public function woocommerce_checkout_update_order_meta_action($order_id)
    {
        $weight = WC()->cart->get_cart_contents_weight();
        update_post_meta($order_id, '_grasshopper_order_weight', $weight);
    }
}
