<?php

namespace RunetId\ApiClientBundle\Cache;

use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Response\Response;

/**
 * Interface CacheInterface
 */
interface CacheInterface
{
    /**
     * @param Request $request
     * @return null|Response
     */
    public function processRequest(Request $request);

    /**
     * @param Request  $request
     * @param Response $response
     */
    public function processResponse(Request $request, Response $response);
}
