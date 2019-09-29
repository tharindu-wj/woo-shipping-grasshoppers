<?php
namespace Grasshoppers\Inc;

use Grasshoppers\Inc\API_Requests\Get_City_Price;

/**
 * Main shipping class for grasshoppers
 */
class WC_Shipping_Grasshoppers extends \WC_Shipping_Method
{
    /**
     * Constructor for your shipping class
     *
     * @access public
     *
     * @return void
     */
    public function __construct($instance_id = 0)
    {
        $this->id = 'grasshoppers';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('Grasshoppers Shipping', 'grasshoppers');
        $this->method_description = __('Custom Shipping Method for Grasshoppers', 'grasshoppers');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'settings'
        );
        // Availability & Countries
        $this->availability = 'including';
        $this->countries = array(
            'LK', // Sri Lanka
        );

        $this->init();

        $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
        $this->shipping_availability = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
        $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Grasshoppers Shipping', 'grasshoppers');

        $this->variations = GRASSHOPPERS_SHIPPING_METHODS;

    }


    /**
     * Init your settings
     *
     * @access public
     *
     * @return void
     */
    function init()
    {
        // Load the settings API
        $this->init_form_fields();
        $this->init_settings();

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }


    /**
     * Define settings field for this shipping
     *
     * @return void
     */
    function init_form_fields()
    {
        $this->form_fields = Form_Fields::options();
    }


    /**
     * Shipping method will available for only weight limit
     *
     * @param array $package
     *
     * @return bool
     */
    public function is_available($package)
    {
        if ($this->shipping_availability == 'no') {
            return false;
        }

        $weight = $this->calculate_package_weight($package, 'kg');

        if ($weight > $this->settings['weight']) {
            wc_add_notice('Grasshoppers notice: Shipping weight limit exceeded.', 'notice');
            return false;
        }

        return true;

    }


    /**
     * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
     *
     * @access public
     * @param mixed $package
     *
     * @return void
     */
    public function calculate_shipping($package)
    {

        $cost = 0;
        $city = $package["destination"]["city"];

        $weight = $this->calculate_package_weight($package, 'kg');


        foreach ($this->variations as $variations) {
            $key = strtolower($variations);


            $body = [
                "keyword" => $city,
                "deliveryMethod" => $key,
                "weight" => $weight,
                "COD" => 0
            ];

            $city_price_request = new Get_City_Price();
            $city_price_request->setBody($body);

            $city_price_response = $city_price_request->request();

            if (!isset($city_price_response[0]['Error'])) {
                $cost = $city_price_response[0]['price'];

                $rate = array(
                    'id' => $key,
                    'label' => $variations,
                    'cost' => $cost,
                    'calc_tax' => 'per_item'
                );

                $this->add_rate($rate);
            }
        }

    }


    /**
     * Calculate total weight of a package
     *
     * @param $package
     * @param $unit
     *
     * @return float
     */
    private function calculate_package_weight($package, $unit)
    {
        $weight = 0;

        foreach ($package['contents'] as $item_id => $values) {
            $_product = $values['data'];
            $weight = $weight + $_product->get_weight() * $values['quantity'];
        }

        return wc_get_weight($weight, $unit);
    }
}
