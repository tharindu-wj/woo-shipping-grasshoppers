<?php
namespace Grasshoppers\Inc;

/**
 * Class Form_Fields
 * @package Grasshoppers\Inc
 */
class Form_Fields
{
    private static $plugin_name = 'woocommerce_grasshoppers_settings';


    /**
     * Set admin setting options for plugin
     *
     * @return array
     */
    public static function options()
    {
        return [
            'enabled' => array(
                'title' => __('Enable', 'grasshoppers'),
                'type' => 'checkbox',
                'description' => __('Enable this shipping.', 'grasshoppers'),
                'default' => 'yes'
            ),

            'corporateId' => array(
                'title' => __('Grasshoppers Corporate Id', 'grasshoppers'),
                'type' => 'text',
                'description' => __('Add corporate id', 'grasshoppers'),
                'default' => __('', 'grasshoppers')
            ),

            'apiURL' => array(
                'title' => __('Grasshoppers API URL', 'grasshoppers'),
                'type' => 'text',
                'description' => __('Default api url will used if you keep this empty', 'grasshoppers'),
                'default' => __('', 'grasshoppers')
            ),

            'weight' => array(
                'title' => __('Weight (kg)', 'grasshoppers'),
                'type' => 'number',
                'description' => __('Maximum allowed weight', 'grasshoppers'),
                'default' => 15
            ),
        ];
    }


    /**
     * Get all admin setting options
     *
     * @return array
     */
    public static function get_settings_options()
    {
        return get_option(self::$plugin_name, array());
    }

    /**
     * Get single admin setting option
     *
     * @param $option
     *
     * @return mixed
     */
    public static function get_settings_option($option)
    {
        $options = get_option(self::$plugin_name);
        return $options[$option];
    }
}
