<?php

/**
 * Main class for Bigstock API Image Detail Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#image-detail
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

class ImageDetailService extends AbstractService implements ServiceInterface
{

    /**
     * Image ID to lookup details
     */
    protected $image_id;

    /**
     * Set the image id for the detail request
     *
     * @param   string  $image_id  the image that you want details about
     */
    public function setImage($image_id)
    {
        if (!is_int($image_id) || $image_id < 1) {
            throw new Exception('An unacceptable image id passed in');
        }
        
        $this->image_id = $image_id;
    }

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'image';
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        $endpoint = $this->getEndpointDomain();
        $endpoint .= $this->image_id;
        
        return $endpoint;
    }

}
