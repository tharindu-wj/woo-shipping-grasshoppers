<?php
namespace Grasshoppers\Inc;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class Grasshoppers_API
 * @package Grasshoppers\Inc
 */
abstract class Grasshoppers_API
{
    protected $api_url = '';
    protected $corporateId = '';
    protected $headers = ['content-type' => 'application/json'];
    protected $body = [];

    /**
     * Grasshoppers_API constructor.
     */
    public function __construct()
    {
        $this->http_client = new GuzzleClient();

        $this->corporateId = Form_Fields::get_settings_option('corporateId');

        $api_url = Form_Fields::get_settings_option('apiURL');
        $this->api_url = !empty($api_url) ? $api_url : 'http://www.grasshoppers.lk/customers/WebService/';

        $this->body = ['corporateId' => $this->corporateId];
    }

    /**
     * Set body property
     *
     * @param $body
     * @param bool $merge
     */
    public function setBody($body, $merge = true)
    {
        if ($merge) {
            $this->body = array_merge($this->body, $body);
        } else {
            $this->body = $body;
        }
    }

    /**
     * Send request to grasshoppers API
     *
     * @return array|mixed|object
     */
    public function request()
    {
        $res = $this->http_client->post($this->api_url . $this->api_method, [
            'headers' => $this->headers,
            'json' => $this->body,
        ]);

        $response_obj = $this->response_content_to_array($res);

        if (isset($response_obj[0]['Error'])) {
            wc_add_notice('Grasshoppers notice: ' . $response_obj[0]['Error'], 'notice');
            return false;
        } else {
            return $response_obj;
        }
    }

    /**
     * Response body content convert to array
     *
     * @param $response
     *
     * @return array|mixed|object
     */
    protected function response_content_to_array($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
