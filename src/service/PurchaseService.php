<?php

/**
 * Main class for Bigstock API Purchase Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#purchase
 * For licensing and examples:
 *
 * @see https://github.com/jacobemerick/bigstock-api-services
 *
 * @author jacobemerick (http://home.jacobemerick.com/)
 */

namespace BigstockAPI\Service;

// @todo autoloader
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AbstractService.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ServiceInterface.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Exception.php';

use BigstockAPI\AbstractService;
use BigstockAPI\ServiceInterface;
use BigstockAPI\Exception;

class PurchaseService extends AbstractService implements ServiceInterface
{

    /**
     * Image ID to purchase
     */
    protected $image_id;

    /**
     * Requested size for purchase
     */
    protected $size_code;

    /**
     * Secret key needed to perform purchase
     * @url https://www.bigstockphoto.com/partners/get-started/
     */
    protected $secret_key;

    /**
     * Override parent construct so the secret key can be passed in
     * Secret key is required for the purchase to go through
     *
     * @param   int     $account_id         account to use for requests
     * @param   string  $secret_key         secret key for this account to make payment/download requests
     * @param   bool    $use_production     flag to decide to use production or not, defaults to not
     */
    public function __construct($account_id, $secret_key, $use_production = false)
    {
        if (strlen($secret_key) < 1) {
            throw new Exception('An unacceptable secret key was passed in');
        }
        
        $this->secret_key = $secret_key;
        
        parent::__construct($account_id, $use_production);
    }

    /**
     * Set the image id for the download
     *
     * @param   string  $image_id  the image that you want to download
     */
    public function setImage($image_id)
    {
        if (!is_int($image_id) || $image_id < 1) {
            throw new Exception('An unacceptable image id passed in');
        }
        
        $this->image_id = $image_id;
    }

    /**
     * Flag to set a preference for size
     *
     * @param       string  $size   size setting for image purchase
     */
    public function setSizeCode($size)
    {
        if ($size != '' && !in_array($size, self::$ACCEPTABLE_SIZE_LIST)) {
            throw new Exception('An unacceptable size setting was passed in');
        }
        
        $this->size_code = $size;
    }

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'purchase';
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        if (!isset($this->image_id)) {
            throw new Exception('You must define an image for purchase');
        }
        if (!isset($this->size_code)) {
            throw new Exception('You must define a size code');
        }
        if (!isset($this->secret_key)) {
            throw new Exception('You must pass in a secret key before making a call');
        }
        
        $query_parameters = array();
        $query_parameters['image_id'] = $this->image_id;
        $query_parameters['size_code'] = $this->size_code;
        $query_parameters['auth_key'] = $this->create_hash_key($this->image_id);
        
        $domain = $this->getEndpointDomain();
        $query_string = http_build_query($query_parameters);
        
        return "{$domain}?{$query_string}";
    }

}
