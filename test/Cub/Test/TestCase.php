<?php

class Cub_Test_TestCase extends \PHPUnit\Framework\TestCase

{
    const API_URL = 'https://id.lexipol.com/v1/';
    const API_KEY = getenv('INTEGRATION_TESTS_SECRET_KEY');
    public $details;
    public $credentials;
    public function setUp()
    {
        Cub_Config::$api_key = self::API_KEY;
        Cub_Config::$api_url = self::API_URL;
        $this->details = array(
            'original_username' => 'ivelum',
            'first_name' => 'do not remove of modify',
            'last_name' => 'user for tests',
        );
        $this->credentials = array(
            'username' => 'support@ivelum.com',
            'password' => 'SJW8Gg',
        );
    }
}
