<?php
/**
 * Base class for all Cub exceptions.
 */
class Cub_Exception extends Exception
{
    protected $http_code, $http_body, $json_body;
    public function __construct($message=null, $http_code=null,
                                $http_body=null, $json_body=null)
    {
        parent::__construct($message);
        $this->http_code = $http_code;
        $this->http_body = $http_body;
        $this->json_body = $json_body;
    }
    public function getHttpCode()
    {
        return $this->http_code;
    }
    public function getHttpBody()
    {
        return $this->http_body;
    }
    public function getJsonBody()
    {
        return $this->json_body;
    }
}
