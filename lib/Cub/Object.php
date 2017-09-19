<?php
class Cub_Object
{
    private $keys;
    public $api_key;

    public function __construct($params=array(), $api_key=null)
    {
        if (!is_array($params)) {
            throw new Cub_Exception(
                'You must pass an array as a first agrument for constructor'
            );
        }
        $this->api_key = $api_key;
        if (!array_key_exists('id', $params)) {
            $params['id'] = null;
        }
        foreach ($params as $k => $v) {
            $this->__set($k, $v);
        }
    }

    public function __get($name)
    {
        return $this->keys[$name];
    }

    public function __set($name, $value)
    {
        if (is_array($value)) {
            return $this->keys[$name] = self::fromArray($value, $this->api_key);
        } else {
            return $this->keys[$name] = $value;
        }
    }

    public static function fromJson($json_str)
    {
        return self::fromArray(json_decode($json_str, true));
    }

    public static function fromArray($arr, $api_key=null)
    {
        $obj_name = null;
        if ($arr && array_key_exists('object', $arr)) {
            $obj_name = ucfirst(strtolower($arr['object']));
        }
        $child_classes = array(
                'User',
                'Organization',
                'Member',
                'Position',
                'MemberPosition',
                'Invitation',
                'InvitationBatch',
                'Group',
                'GroupMember',
                'ServiceSubscription',
                'Plan',
                'Customer',
                'Site',
                'MailingList',
                'Subscription',
                'Country',
                'State',
                'Product',
                'SKU',
                'Order',
                'OrderItem',
                'Charge',
        );
        if ($obj_name && in_array($obj_name, $child_classes)) {
            $class = 'Cub_'.$obj_name;
            unset($arr['object']);
            return new $class($arr, $api_key);
        }
        $drill_down = array();
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $drill_down[$k] = self::fromArray($v);
            } else {
                $drill_down[$k] = $v;
            }
        }
        return $drill_down;
    }

    /**
     * Get api url for a class.
     *
     * Example:
     *  var_dump(Cub_Object::classUrl('Cub_Member'));
     *  // output: string(7) "members"
     *
     * @param $class
     * @return string
     */
    public static function classUrl($class)
    {
        return strtolower(ltrim(strrchr($class, '_'), '_')).'s';
    }

    /**
     * Get api url for a class instance.
     *
     * Example:
     *  $user = new Cub_User(array('id' => 'usr_someuid'));
     *  var_dump($user->instanceUrl());
     *  // output: string(17) "users/usr_someuid"
     *
     * @return string
     */
    public function instanceUrl()
    {
        return self::classUrl(get_class($this)).'/'.$this->id;
    }

    public static function execGet($class, $id, $api_key)
    {
        return self::fromArray(
            Cub_Api::get(self::classUrl($class).'/'.$id, array(), $api_key)
        );
    }

    public function execReload()
    {
        if (!array_key_exists('id', $this->keys) || !$this->keys['id']) {
            throw new Cub_Exception(
                "Unable to reload object, because it's id is unknown."
            );
        }
        $this->__construct(
            Cub_Api::get($this->instanceUrl(), array(), $this->api_key)
        );
        return $this;
    }

    public function execSave()
    {
        $this->__construct(
            Cub_Api::post($this->instanceUrl(), $this->keys, $this->api_key)
        );
        return $this;
    }

    public static function execCreate($class, $params, $api_key=null)
    {
        return self::fromArray(
            Cub_Api::post(self::classUrl($class), $params, $api_key)
        );
    }

    public function execRemove()
    {
        $this->__construct(
            Cub_Api::delete($this->instanceUrl(), array(), $this->api_key)
        );
        return $this;
    }

    public static function get($id, $api_key = null)
    {
        $class = get_called_class();
        return self::execGet($class, $id, $api_key);
    }

    public function reload()
    {
        return $this->execReload();
    }

    public function delete()
    {
        return $this->execRemove();
    }

    public static function deleteById($id, $api_key = null)
    {
        $class = get_called_class();
        $instance = new $class(array('id' => $id), $api_key);
        return $instance->delete();
    }
}
