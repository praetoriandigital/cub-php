<?php

require_once 'TestCase.php';

class Cub_Test_ApiTest extends Cub_Test_TestCase
{
    public function testNoApiKey()
    {
        $this->expectException('Cub_Unauthorized');
        Cub_Config::$api_key = null;
        Cub_Api::get('');
    }
    public function testBadApiKey()
    {
        $this->expectException('Cub_Unauthorized');
        Cub_Config::$api_key = 'sk_totallyBadKey';
        Cub_Api::get('');
    }
    public function testWrongMethod()
    {
        $this->expectException('Cub_MethodNotAllowed');
        Cub_Api::request('put', '');
    }
    public function testWrongResponse()
    {
        $this->expectException('Cub_ApiError');
        Cub_Config::$api_url = 'https://cub.policeone.com/';
        Cub_Api::get('');
    }
    public function testApiWrongUrl()
    {
        $this->expectException('Cub_NotFound');
        Cub_Api::get('non-existing-method');
    }
    public function testApiNotFound()
    {
        $this->expectException('Cub_NotFound');
        Cub_Api::get('members/111111111111404');
    }
    public function testApiBadRequest()
    {
        $this->expectException('Cub_BadRequest');
        $this->expectExceptionMessage(
            "The following parameters are invalid: username, password\n".
            "Params:\n".
            "- password: This field is required.\n".
            "- username: This field is required.\n"
        );
        Cub_Api::post('user/login');
    }
}

