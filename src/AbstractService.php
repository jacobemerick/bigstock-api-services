<?php

/**
 * Abstract class for all Bigstock API Services
 * For licensing and examples:
 *
 * @see https://github.com/jacobemerick/bigstock-api-services
 *
 * @author jacobemerick (http://home.jacobemerick.com/)
 */

namespace BigstockAPI;

abstract class AbstractService
{

    /**
     * URL endpoints for API requests
     */
    const TEST_ENDPOINT = 'http://testapi.bigstockphoto.com/2/%d/%s/';
    const PRODUCTION_ENDPOINT = 'http://api.bigstockphoto.com/2/%d/%s/';

    /**
     * Account ID provided by Bigstock
     * Required for all requests
     * @url https://www.bigstockphoto.com/partners/get-started/
     */
    protected $account_id;

    /**
     * List of acceptable size settings
     */
    public static $ACCEPTABLE_SIZE_LIST = array(
        'm',
        'l',
        'xl',
    );

    /**
     * Flag on whether to use the production endpoint or not
     * Default to false (use test endpoint) for safety
     */
    protected $use_production_endpoint = false;

    /**
     * Basic construct method
     * Since all requests need the account parameter, pass that in here
     * If it makes sense we can override this method in the children
     *
     * @param   int     $account_id         account to use for requests
     * @param   bool    $use_production     flag to decide to use production or not, defaults to not
     */
    public function __construct($account_id, $use_production = false)
    {
        $this->account_id = $account_id;
        $this->use_production_endpoint = $use_production;
    }

    /**
     * Helper method to construct the main endpoint domain
     * Determines whether to use production endpoint or testing endpoint
     * Also plugs in minimal required pieces, the account and service request
     * May be overridden to pass in extra pieces to the path
     *
     * @return  string  root domain for endpoint request
     */
    public function getEndpointDomain()
    {
        $domain = ($this->use_production_endpoint) ? self::PRODUCTION_ENDPOINT : self::TEST_ENDPOINT;
        
        $account = $this->account_id;
        $service = $this->getServiceName();
        
        return sprintf($domain, $account, $service);
    }

    /**
     * Helper method to create auth hash
     * Auth hash uses sha1 with (secret key . account id . {parameter})
     *
     * @param   string  $parameter  extra parameter for the request (varies by request)
     * @return  string              hash for the request
     */
    protected function create_hash_key($parameter)
    {
        return sha1("{$this->secret_key}{$this->account_id}{$parameter}");
    }

    /**
     * Fetch the response as a JSON string
     *
     * @return  stdclass    json response from endpoint
     */
    public function fetchJSON()
    {
        $response = $this->fetchResponse();
        return json_decode($response);
    }

    /**
     * Make a request based on the defined service and parameters
     *
     * @return  string  raw response from bigstock api
     */
    public function fetchResponse()
    {
        $url = $this->getEndpoint();
        $response = $this->executeRequest($url);
        return $response;
    }

    // @todo look into jsonp request differences

    /**
     * Actual request execution step via the curl
     * Accepts fully built and parameterized endpoint and asks bigstock for information
     *
     * @param   $url    string  full endpoint for the service request
     * @return  string          string response from the request
     */
    // @todo set additional curl params
    // @todo handle fails
    protected function executeRequest($url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        return curl_exec($handle);
    }

}
