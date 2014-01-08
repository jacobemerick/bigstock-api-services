<?php

/**
 * Main class for Bigstock API Lightbox Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#lightbox
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

class LightboxService extends AbstractService implements ServiceInterface
{

    /**
     * Lightbox id to pull in
     */
    protected $lightbox_id;

    /**
     * Page to pull from (basic pagination)
     */
    protected $page;

    /**
     * Limit for items returned
     */
    protected $limit;

    /**
     * Lightbox key for private lightbox request
     * Used when pulling other user's private lightbox info
     */
    protected $lightbox_key;

    /**
     * Secret key needed to pull private lightboxes
     * @url https://www.bigstockphoto.com/partners/get-started/
     */
    protected $secret_key;

    /**
     * Set the lightbox id for lookup
     *
     * @param   string  $lightbox   the lightbox that you want to look up
     */
    public function setLightbox($lightbox_id)
    {
        if (!is_int($lightbox_id) || $lightbox_id < 1) {
            throw new Exception('An unacceptable lightbox id passed in');
        }
        
        $this->lightbox_id = $lightbox_id;
    }

    /**
     * Set the page variable if doing pagination
     *
     * @param   int     $page   the page (set) that you want to pull
     */
    public function setPage($page)
    {
        if (!is_int($page) || $page < 1) {
            throw new Exception('An unacceptable value for page was passed in');
        }
        
        $this->page = $page;
    }

    /**
     * Set the limit for results
     *
     * @param   int     $limit  cap of how many results to return
     */
    public function setLimit($limit)
    {
        if (!is_int($limit) || $limit < 1) {
            throw new Exception('An unacceptable value for the limit was passed in');
        }
        
        $this->limit = $limit;
    }

    /**
     * Set the lightbox key for private lightbox requests
     *
     * @param   int     $lightbox_key   the key from the private lightbox that you want to fetch
     */
    public function setLightboxKey($lightbox_key)
    {
        if (!is_int($lightbox_key) || $lightbox_key < 1) {
            throw new Exception('An unacceptable lightbox key passed in');
        }
        
        $this->lightbox_key = $lightbox_key;
    }

    /**
     * Set the secret key
     * Secret key is required for certian authenticated requests
     *
     * @param   string  $secret_key     secret key for this account to make private requests
     */
    public function setSecretKey($secret_key)
    {
        if (strlen($secret_key) < 1) {
            throw new Exception('An unacceptable secret key was passed in');
        }
        
        $this->secret_key = $secret_key;
    }

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'lightbox';
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        $query_parameters = array();
        $domain = $this->getEndpointDomain();
        
        if (isset($this->lightbox_id)) {
            $domain .= $this->lightbox_id;
        }
        if (isset($this->page)) {
            $query_parameters['page'] = $this->page;
        }
        if (isset($this->limit)) {
            $query_parameters['limit'] = $this->limit;
        }
        
        if (isset($this->secret_key)) {
            $auth_variable = '';
            if (isset($this->lightbox_id)) {
                $auth_variable .= $this->lightbox_id;
            }
            if (isset($this->lightbox_key)) {
                $auth_variable .= $this->lightbox_key;
            }
            $query_parameters['auth_key'] = $this->create_hash_key($auth_variable);
        }
        
        $query_string = http_build_query($query_parameters);
        
        return "{$domain}?{$query_string}";
    }

}
