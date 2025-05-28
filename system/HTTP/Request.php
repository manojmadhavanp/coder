<?php

namespace System\HTTP;

use System\Utils\Sanitizer;

class Request{
    private array $params = [];
    private $method;
    private $path;
    private $requestpath;
    private $isAuth;
    private $Auth_uri;
    private $isApi;
    
    

    function __construct()
    {
        $this->isAuth = false;
        $this->isApi = false;
        $base_route = str_replace($_SERVER['DOCUMENT_ROOT'], "", ROUTE_BASE);
        $requestUri = parse_url($_SERVER['REQUEST_URI']); 
        $this->requestpath = str_replace($base_route, "", $requestUri['path']);
        $this->path = $this->requestpath;
        $this->method = $_SERVER['REQUEST_METHOD'];
      
        // Handle GET and POST parameters
        // Handle parameters based on request method
        // Handle multipart PUT request
        if ($this->method === 'PUT' && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
            $this->handleMultipartPutRequest();
        } elseif ($this->method === 'POST' || $this->method === 'PUT') {
            // Handle regular POST and PUT requests
            $rawPostData = file_get_contents('php://input');
            if (!empty($rawPostData)) {
                $postData = json_decode($rawPostData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->addParams(Sanitizer::sanitize($postData));
                }
            } else {
                $this->addParams(Sanitizer::sanitize(array_merge($_GET, $_POST)));
            }
        } else {
            $this->addParams(Sanitizer::sanitize(array_merge($_GET, $_POST)));
        }
        
        // Extract dynamic parameters from the request path
        $this->extractDynamicParams();

        $uriarray = explode("/", $this->requestpath);
        if(strtolower($uriarray[0]) == 'auth'){
            $this->isAuth = true;
            $this->Auth_uri = $uriarray[1];
        }
        $this->isApi = $this->checkisApi();
        
    }

    private function handleMultipartPutRequest()
    {
        $inputData = file_get_contents('php://input');

        // Get boundary from content type
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1] ?? '';

        if (!$boundary) {
            throw new \Exception("Boundary not found in Content-Type header.");
        }

        // Split the input data by the boundary
        $blocks = preg_split("/-+$boundary/", $inputData);
        array_pop($blocks); // Remove the last empty block after the boundary

        foreach ($blocks as $block) {
            if (empty($block)) continue;

            // Check if it's a file or a normal input field
            if (strpos($block, 'Content-Disposition: form-data; name="') !== false) {
                // Extract form field
                preg_match('/name="([^"]*)"/', $block, $fieldMatches);
                $fieldName = $fieldMatches[1];

                // Extract value
                preg_match('/\r\n\r\n(.*)\r\n$/', $block, $valueMatches);
                $fieldValue = isset($valueMatches[1]) ? $valueMatches[1] : '';

                // Assign to params array
                $this->params[$fieldName] = Sanitizer::sanitize($fieldValue);
            }

            // Check if it's a file upload
            if (strpos($block, 'Content-Disposition: form-data; name="') !== false && strpos($block, 'filename="') !== false) {
                // Extract file field name and filename
                preg_match('/name="([^"]*)"; filename="([^"]*)"/', $block, $fileMatches);
                $fieldName = $fileMatches[1];
                $fileName = $fileMatches[2];

                // Get the file content
                preg_match('/\r\n\r\n(.*)\r\n$/s', $block, $fileContentMatches);
                $fileContent = $fileContentMatches[1];

                // Store file details (you can handle file saving separately)
                $this->params[$fieldName] = [
                    'name' => $fileName,
                    'content' => $fileContent
                ];
            }
        }
    }

    // Handle multipart form-data in a PUT request
   /* private function handleMultipartPutRequest()
    {
        // Handle $_FILES if files are uploaded
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                $this->params[$key] = $file;
            }
        }

        // Parse form fields from php://input (multipart form may still contain text fields)
        if (!empty($_POST)) {
            $this->addParams(Sanitizer::sanitize($_POST));
        }

        // To handle any text fields coming via `php://input`
        $postData = [];
        parse_str(file_get_contents('php://input'), $postData);
        if (!empty($postData)) {
            $this->addParams(Sanitizer::sanitize($postData));
        }
    }*/

    private function checkisApi(){
        // Check for X-Requested-With header (typically set by AJAX libraries)
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Check for Accept header that expects JSON (common in API calls)
        $acceptsJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

        return $isAjax || $acceptsJson;
    }
    private function extractDynamicParams() {
        // Example route: /{username}/dashboard
        $routePattern = '/{:(\w+)}/'; // Regex to match dynamic parameters
        $routeSegments = explode('/', $this->requestpath);
        
        foreach ($routeSegments as $segment) {
            if (preg_match($routePattern, $segment, $matches)) {
                $paramName = $matches[1]; // Get the parameter name
                $this->params[$paramName] = $segment; // Store the parameter value
            }
        }
    }

    

    

    protected function set(string $key, string $val){
        $this->params[$key] = Sanitizer::sanitize($val);
    }
    
    function GetURI(){
        return $this->path;
    }
    function GetMethod(){
        return $this->method;
    }
    function isAuth(){
        return $this->isAuth;
    }
    function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function getAllParams()
    {
        return $this->params;
    }

    public function addParam($key, $value)
    {
        $this->params[$key] = Sanitizer::sanitize($value);
    }
   
    public function addParams(array $urlparams)
    {
        $this->params = array_merge($this->params, Sanitizer::sanitize($urlparams));
    }
    
    public function getParams()
    {
        return $this->params;
    }
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    public function get(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    public function getParsedBody()
    {
        $data = file_get_contents('php://input');

        // Parse the JSON data if needed
        $parsedData = json_decode($data, true);

        // Process the data (for example, print it out or save it to the database)
        echo '<pre>';
        echo 'Parsed JSON data: ';
        if(empty($parsedData)) {
            print_r($data);
        }
        print_r($parsedData);
        echo '</pre>';// or use a more robust method to parse the body

    }
};

?>