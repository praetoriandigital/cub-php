<?php

require_once 'TestCase.php';

class Cub_Test_ApiTest extends Cub_Test_TestCase
{
    public function testNoApiKey()
    {
        $this->setExpectedException('Cub_Unauthorized');
        Cub_Config::$api_key = null;
        Cub_Api::get('');
    }
    public function testBadApiKey()
    {
        $this->setExpectedException('Cub_Unauthorized');
        Cub_Config::$api_key = 'sk_totallyBadKey';
        Cub_Api::get('');
    }
    public function testWrongMethod()
    {
        $this->setExpectedException('Cub_MethodNotAllowed');
        Cub_Api::request('put', '');
    }
    public function testWrongResponse()
    {
        $this->setExpectedException('Cub_ApiError');
        Cub_Config::$api_url = 'https://cub.policeone.com/';
        Cub_Api::get('');
    }
    public function testApiWrongUrl()
    {
        $this->setExpectedException('Cub_NotFound');
        Cub_Api::get('non-existing-method');
    }
    public function testApiNotFound()
    {
        $this->setExpectedException('Cub_NotFound');
        Cub_Api::get('members/111111111111404');
    }
    /**
     * @expectedException Cub_BadRequest
     * @expectedExceptionMessageRegExp /The following parameters are invalid: username, password?\w+/
     */
    public function testApiBadRequest()
    {
        Cub_Api::post('user/login');
    }
}

