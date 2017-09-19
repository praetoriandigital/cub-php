<?php
class Cub_User extends Cub_Object
{
    public static function classUrl($class)
    {
        return 'user';
    }

    public function instanceUrl()
    {
        $class_url = self::classUrl(get_class());
        if ($this->id) {
            return $class_url .'s/'.$this->id;
        }
        return $class_url;
    }

    public function __construct($params = array(), $api_key = null)
    {
        parent::__construct($params, $api_key);
    }

    public static function get($params = array(), $api_key = null)
    {
        $instance = new Cub_User($params, $api_key);
        return $instance->reload();
    }

    public function execReload($params = array())
    {
        $this->__construct(
            Cub_Api::get($this->instanceUrl(), $params, $this->api_key)
        );
        return $this;
    }

    public function save()
    {
        return $this->execSave();
    }

    public static function login($username, $password, $provider = '', $api_key = null)
    {
        $url = static::classUrl(get_class()).'/login';
        return self::fromArray(
            Cub_Api::post($url, array(
                'username' => $username,
                'password' => $password,
                'provider' => $provider,
            ), $api_key)
        );
    }
}
