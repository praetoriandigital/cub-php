<?php
class Cub_Utils
{
    public static function urlify($key, $val) {
        if (is_array($val)) {
            $params = array();
            foreach ($val as $k => $v) {
                $prefix = $key ? $key.'__' : '';
                $params[] = self::urlify($prefix.$k, $v);
            }
            return implode('&', $params);
        } elseif (is_bool($val)) {
            return urlencode($key) . '=' . ($val ? 'true' : 'false');
        } elseif (is_null($val)) {
            return urlencode($key) . '=null';
        } elseif (is_float($val)) {
            // Preserve type if float can be implicitly converted to int.
            // For example, 1.0 should remain 1.0, and not become 1.
            $val_str = (string) $val;
            if ((int) $val == $val) {
                $val_str = $val_str . '.0';
            }
            return urlencode($key) . '=' . urlencode($val_str);
        } else {
            if (is_string($val)) {
                if (is_numeric($val) || $val == 'true' || $val == 'false' || $val == 'null') {
                    $val = '"'.$val.'"';
                }
            }
            return urlencode($key) . '=' . urlencode($val);
        }
    }
    /**
     * Convert associative array to query string parameters format.
     *
     * Supports nested arrays. Nested arrays will be encoded using
     * prefixes made of parent key and '__'.
     *
     * Examples:
     *
     *   var_dump(Cub_Utils::urlEncode(array('foo' => 1, 'bar' => 'baz')));
     *   // output: string(17) "foo=1&bar=baz"
     *
     *   var_dump(Cub_Utils::urlEncode(array(
     *       'foo' => array('bar' => 'baz', 'key' => 'val')
     *   )));
     *   // output: string(25) "foo__bar=baz&foo__key=val"
     *
     * @param $arr
     * @return string
     */
    public static function urlEncode($arr)
    {
        return self::urlify('', $arr);
    }
}
