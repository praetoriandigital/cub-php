<?php
class Cub_Api
{
    public static function with_decoded_datetimes($json_body)
    {
        $json_body_with_datetimes = array();
        foreach ($json_body as $key => $value) {
            if (is_string($value) && substr($value, -1) === 'Z') {
                $datetime = DateTime::createFromFormat(
                    'Y-m-d\TH:i:sZ',
                    $value, new DateTimeZone('UTC')
                );
                if ($datetime) {
                    $json_body_with_datetimes[$key] = $datetime;
                } else {
                    $json_body_with_datetimes[$key] = $value;
                }
            } elseif (is_array($value)) {
                $json_body_with_datetimes[$key] = self::with_decoded_datetimes($value);
            } else {
                $json_body_with_datetimes[$key] = $value;
            }
        }
        return $json_body_with_datetimes;
    }
    public static function request($method, $url, $params=array(), $api_key=null)
    {
        if (!$api_key) {
            $api_key = Cub_Config::$api_key;
        }
        if (!$api_key) {
            throw new Cub_Unauthorized(
                "You did not provide an API key.\n".
                "There are 2 ways to do it:\n".
                "1) set it globally via Cub_Config, like this:\n".
                "Cub_Config::$api_key = \"<your-key>\";\n".
                "2) pass it after array of arguments to methods which\n".
                "communicate with the API, like this:\n".
                '$user = Cub_User->create(array(...), "<your-key>")'
            );
        }
        /*** Prepare request ***/
        $curl_ver = curl_version();
        $client_info = array(
            'publisher' => 'ivelum',
            'platform' => php_uname(),
            'language' => 'PHP '.phpversion(),
            'httplib' => 'curl '.$curl_ver['version'].
                ', features: '.$curl_ver['features']
        );
        $headers = array(
            'Authorization: Bearer '.$api_key,
            'User-Agent: Cub Client for PHP, v'.Cub_Client::VERSION,
            'Content-Type: application/x-www-form-urlencoded',
            'X-Cub-User-Agent-Info: '.json_encode($client_info)
        );
        $curl_options = array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => Cub_Config::$api_timeout,
            CURLOPT_TIMEOUT => Cub_Config::$api_timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => Cub_Config::$ssl_verify,
        );
        $abs_url = Cub_Config::$api_url . $url;
        $params_encoded = Cub_Utils::urlEncode($params);
        switch (strtolower($method)) {
            case 'get':
                $curl_options[CURLOPT_HTTPGET] = true;
                if ($params_encoded) {
                    $abs_url.= '?'.$params_encoded;
                }
                break;
            case 'post':
                $curl_options[CURLOPT_POST] = true;
                $curl_options[CURLOPT_POSTFIELDS] = $params_encoded;
                break;
            case 'delete':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if ($params_encoded) {
                    $abs_url.= '?'.$params_encoded;
                }
                break;
            default:
                throw new Cub_MethodNotAllowed(
                    "HTTP method not supported: $method"
                );
        }
        /*** Send request ***/
        $curl = curl_init($abs_url);
        curl_setopt_array($curl, $curl_options);
        $http_body = curl_exec($curl);
        /*** Handle connection errors ***/
        if ($http_body === false) {
            $err_code = curl_errno($curl);
            $err_msg = curl_error($curl);
            switch ($err_code) {
                case CURLE_COULDNT_CONNECT:
                case CURLE_COULDNT_RESOLVE_HOST:
                case CURLE_OPERATION_TIMEOUTED:
                    $msg = "Could not connect to Cub at $abs_url. ".
                        "Please check your internet connection and try again.";
                    break;
                case CURLE_SSL_CACERT:
                case CURLE_SSL_PEER_CERTIFICATE:
                    $msg = 'Could not verify Cub SSL certificate.
This could be a result of SSL inspection software running in your
network. You can check this by opening '.Cub_Config::$api_url.' in
your browser. If that is true, in development environment you can turn
SSL verification off by setting Cub_Config::ssl_verify = false. Note that
it should NOT be used in production, please always have ssl_verify = true
when dealing with real money.
If problem persists, please contact us at support@ivelum.com.';
                    break;
                default:
                    $msg = 'Unexpected network error. If problem persists, '.
                        'please contact us at support@ivelum.com';
            }
            $msg .= "\n\n(Network error [errno $err_code]: $err_msg)";
            throw new Cub_ConnectionError($msg);
        }
        /*** Interpret Cub response ***/
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json_body = json_decode($http_body, true);
        if (!is_array($json_body)) {
            if ($http_code == 401) {
                throw new Cub_Unauthorized(
                    'API key you provided is not active.',
                    $http_code, $http_body, $json_body
                );
            } else {
                throw new Cub_ApiError(
                    'Invalid response from the API (not a valid JSON). '.
                    'If problem persists, please contact us at support@ivelum.com',
                    $http_code, $http_body, $json_body
                );
            }
        }
        $err_msg = $http_code == 200 ? 'OK' : 'Unknown error. If problem '.
            'persists, please contact us at support@ivelum.com';
        if (array_key_exists('error', $json_body)) {
            if (array_key_exists('description', $json_body['error'])) {
                $err_msg = $json_body['error']['description'];
            }
            if (array_key_exists('params', $json_body['error'])) {
                $err_msg.='
Params:';
                $err_params = $json_body['error']['params'];
                if (is_array($err_params)) {
                    foreach ($err_params as $k => $v) {
                        $err_msg.= '
- '.$k.': '.$v;
                    }
                }
                $err_msg.='
';
            }
        }
        $json_body = self::with_decoded_datetimes($json_body);
        switch ($http_code) {
            case 200:
                return $json_body;
            case 400:
                throw new Cub_BadRequest($err_msg, $http_code,
                    $http_body, $json_body);
            case 401:
                throw new Cub_Unauthorized($err_msg, $http_code,
                    $http_body, $json_body);
            case 403:
                throw new Cub_Forbidden($err_msg, $http_code,
                    $http_body, $json_body);
            case 404:
                throw new Cub_NotFound($err_msg, $http_code,
                    $http_body, $json_body);
            case 405:
                throw new Cub_MethodNotAllowed($err_msg, $http_code,
                    $http_body, $json_body);
            case 500:
                throw new Cub_InternalError($err_msg, $http_code,
                    $http_body, $json_body);
            case 502:
                throw new Cub_BadGateway($err_msg, $http_code,
                    $http_body, $json_body);
            case 503:
                throw new Cub_ServiceUnavailable($err_msg, $http_code,
                    $http_body, $json_body);
            default:
                throw new Cub_ApiError($err_msg, $http_code,
                    $http_body, $json_body);
        }
    }

    public static function get($url, $params=array(), $api_key=null)
    {
        return Cub_Api::request('get', $url, $params, $api_key);
    }

    public static function post($url, $params=array(), $api_key=null)
    {
        return Cub_Api::request('post', $url, $params, $api_key);
    }

    public static function delete($url, $params=array(), $api_key=null)
    {
        return Cub_Api::request('delete', $url, $params, $api_key);
    }
}
