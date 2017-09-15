<?php

class Cub_Test_TestCase extends \PHPUnit\Framework\TestCase

{
    const API_URL = 'https://cub.policeone.com/v1/';
    const API_KEY = 'sk_23a00c357cb44c358';
    public function setUp()
    {
        Cub_Config::$api_key = self::API_KEY;
        Cub_Config::$api_url = self::API_URL;
    }
}
