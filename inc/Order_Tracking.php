<?php

namespace Grasshoppers\Inc;

use Grasshoppers\Inc\API_Requests\Get_Delivery_Status;

/**
 * Class Order_Tracking
 * @package Grasshoppers\Inc
 */
class Order_Tracking
{

    private $order;

    /**
     * Order_Tracking constructor.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get tracking information from grasshoppers API
     *
     * @return array|mixed|object
     */
    public function get_tracking_data()
    {

        $body = array(
            'corporateId' => Form_Fields::get_settings_option('corporateId'),
            'referenceNo' => $this->order->get_meta('_grasshopper_order_reference'),
        );

        $delivery_status_request = new Get_Delivery_Status();
        $delivery_status_request->setBody($body);

        return $delivery_status_request->request();
    }
}
