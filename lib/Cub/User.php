<?php
class Cub_User extends Cub_Object
{
    public static function classUrl($class)
    {
        return 'user';
    }
    public function instanceUrl()
    {
        return self::classUrl(get_class()).'s/'.$this->id;
    }

    public function __construct($params = array(), $api_key = null)
    {
        parent::__construct($params, $api_key);
    }
}
