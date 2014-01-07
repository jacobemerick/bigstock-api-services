<?php

/**
 * Main class for Bigstock API Category Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#categories
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

class CategoryService extends AbstractService implements ServiceInterface
{

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'categories';
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        $domain = $this->getEndpointDomain();
        return $domain;
    }

}
