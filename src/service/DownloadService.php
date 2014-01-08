<?php

/**
 * Main class for Bigstock API Download Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#download
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

class DownloadService extends AbstractService implements ServiceInterface
{

    /**
     * Download id passed over from purchase service
     */
    protected $download_id;

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
    // @todo this might work better as a trait to override abstract
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
    public function setDownload($download_id)
    {
        if (!is_int($download_id) || $download_id < 1) {
            throw new Exception('An unacceptable download id passed in');
        }
        
        $this->download_id = $download_id;
    }

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'download';
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        if (!isset($this->download_id)) {
            throw new Exception('You must define a download id');
        }
        if (!isset($this->secret_key)) {
            throw new Exception('You must pass in a secret key before making a call');
        }
        
        $query_parameters = array();
        $query_parameters['download_id'] = $this->download_id;
        $query_parameters['auth_key'] = $this->create_hash_key($this->download_id);
        
        $domain = $this->getEndpointDomain();
        $query_string = http_build_query($query_parameters);
        
        return "{$domain}?{$query_string}";
    }

}
