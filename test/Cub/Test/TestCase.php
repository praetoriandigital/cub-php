<?php

class Cub_Test_TestCase extends \PHPUnit\Framework\TestCase

{
    const API_URL = 'https://cub.policeone.com/v1/';
    const API_KEY = 'sk_23a00c357cb44c358';
    const credentials = array(
        'username' => 'support@ivelum.com',
        'password' => 'SJW8Gg',
    );
    public $details;
    public function setUp()
    {
        Cub_Config::$api_key = self::API_KEY;
        Cub_Config::$api_url = self::API_URL;
        $this->details = array(
            'original_username' => 'ivelum',
            'first_name' => 'do not remove of modify',
            'last_name' => 'user for tests',
        );
    }
}
