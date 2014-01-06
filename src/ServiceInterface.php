<?php

/**
 * Interface for all included Bigstock API Services
 * For licensing and examples:
 *
 * @see https://github.com/jacobemerick/bigstock-api-services
 *
 * @author jacobemerick (http://home.jacobemerick.com/)
 */

namespace BigstockAPI;

interface ServiceInterface
{

    /**
     * Type of service for the API to handle
     */
    public function getServiceName();

    /**
     * Endpoint build method
     */
    public function getEndpoint();

}
