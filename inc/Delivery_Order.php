<?php
namespace Grasshoppers\Inc;

/**
 * Class Delivery_Order
 * @package Grasshoppers\Inc
 */
class Delivery_Order
{

    private $order_id;
    private $order;

    private $order_data = [];
    private $pickup_data = [];
    private $delivery_data = [];
    private $payment_data = [];
    private $product_data = [];

    /**
     * Delivery_Order constructor.
     *
     * @param $order_id
     */
    public function __construct($order)
    {
        $this->order = $order;
        $this->order_id = $order->get_id();
    }

    /**
     * set order related data
     */
    private function set_order_data()
    {
        $this->order_data = [
            "customerOrderReferenceNo" => $this->order_id,
        ];
    }

    /**
     * set pickup location related data
     */
    private function set_pickup_data()
    {
        $this->pickup_data = [
            "pickupStreet" => get_option('woocommerce_store_address') . get_option('woocommerce_store_address_2'),
            "pickupCity" => get_option('woocommerce_store_city'),
            "pickupZipCode" => get_option('woocommerce_store_postcode'),
            "pickupLatitude" => 0,
            "pickupLongitude" => 0,
        ];
    }

    /**
     * set pickup delivery related data
     */
    private function set_delivery_data()
    {
        $this->delivery_data = [
            "recipientName" => $this->order->get_shipping_first_name() . $this->order->get_shipping_last_name(),
            "recipientContactNo" => $this->order->get_billing_phone(),
            "recipientContactNo2" => "N/A",
            "deliverStreet" => $this->order->get_shipping_address_1() . $this->order->get_shipping_address_2(),
            "deliverCity" => $this->order->get_shipping_city(),
            "deliveryZipCode" => $this->order->get_shipping_postcode(),
            "deliverLatitude" => 0,
            "deliverLongitude" => 0,
            "deliveryMethod" => $this->order->get_shipping_method(),
        ];
    }

    /**
     * set payment related data
     */
    private function set_payment_data()
    {
        $this->payment_data = [
            "paymentType" => $this->order->get_payment_method(),
            "priceCOD" => 0.0,
        ];
    }

    /**
     * set order product related data
     */
    private function set_product_data()
    {
        $weight = $this->order->get_meta('_grasshopper_order_weight');;
        $this->product_data['scaleValue'] = wc_get_weight($weight, 'kg');

        foreach ($this->order->get_items() as $item_id => $item_data) {
            $this->product_data[$item_id]['title'] = $item_data->get_name();
            $this->product_data[$item_id]['quantity'] = $item_data->get_quantity();
        }
    }

    /**
     * call all order setting methods
     */
    private function process_delivery_order()
    {
        $this->set_order_data();
        $this->set_pickup_data();
        $this->set_delivery_data();
        $this->set_payment_data();
        $this->set_product_data();
    }

    /**
     *  get merged order
     *
     * @return array
     */
    public function get_delivery_order()
    {
        $this->process_delivery_order();
        return array_merge($this->order_data,
            $this->pickup_data,
            $this->delivery_data,
            $this->payment_data,
            $this->product_data);
    }

    /**
     * add additional field to order
     */
    public function add_order_meta($meta_key, $value)
    {
        $this->order->update_meta_data($meta_key, $value);
        $this->order->save();
    }
}
