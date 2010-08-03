<?php
class PluginResponse {
    var $params;
    var $http_accept;
    var $method;
    var $path;

    public function __construct($path='', $method='get', $params=array()) 
    { // {{{
        $this->http_accept = (strpos($_SERVER['HTTP_ACCEPT'], 'json') ? 'json' : 'xml');
        $this->path = $path;
        $this->method = $method;
        $this->params = $params;
    } // }}}
}

class TestException extends Exception {
}

class PluginServer {
    public static function processRequest($path='') 
    { // {{{
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $params = array();
        if(strpos($path, '?') !== FALSE) $path = substr($path, 0, strpos($path, '?'));

        switch($request_method) {
            case 'get':
                foreach($_GET as $k=>$v) $params[strtolower($k)] = $v;
                break;

            case 'post':
                foreach($_POST as $k=>$v) $params[strtolower($k)] = $v;
                break;

            case 'put':
                parse_str(file_get_contents('php://input'), $put_vars);
                $params = $put_vars;
                break;
        }

        $response = new PluginResponse($path, $request_method, $params);

        return $response;
    } // }}}

    public static function getStatusCode($status) 
    { // {{{
        // Move to ini file
        $codes = array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        ); 

        return (isset($codes[$status])) ? $codes[$status] : '';
    } // }}}

    public static function sendResponse($status=200, $key='', $body='', $data=array(), $content_type='text/html') 
    { // {{{
        $message = '';

        switch($status) {
            case 401:
                $message = 'You must be authorized to view this page.';
                break;

            case 404:
                $message = 'The requested URL '.$_SERVER['REQUEST_URI'].' was not found.';
                break;

            case 500:
                $message = 'The server encountered an error processing your request.';
                break;

            case 501:
                $message = 'The requested method is not implemented.';
                break;

            default:
                $message = PluginServer::getStatusCode($status);
                break;
        }

        $body = $body == '' ? $message : $body;
        $resp = array(
			'key' => $key,
			'status' => $status,
			'message' => $body,
			'data' => $data
        );
        echo json_encode($resp);
        exit;
    } // }}}
}
?>
