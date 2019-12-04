<?php

class Cub_Test_TestCase extends \PHPUnit\Framework\TestCase

{
    const API_URL = 'https://id.lexipol.com/v1/';
    public $details;
    public $credentials;
    public function setUp()
    {
        Cub_Config::$api_key = getenv('INTEGRATION_TESTS_SECRET_KEY');
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
