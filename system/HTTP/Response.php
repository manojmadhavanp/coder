<?php

namespace System\HTTP;


class Response{

    /**
     * Send a JSON response with a given HTTP status code.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     */
    public static function send($data, int $statusCode = 200, array $headers = [])
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        
        echo json_encode($data);
        exit;
    }

    /**
     * Send a success response for a CRUD operation.
     *
     * @param string $operation
     * @param mixed $data
     * @param array $additionalData
     */
    public static function sendSuccess(string $operation, $data = null, array $additionalData = [])
    {
        $response = [
            'status' => 'success',
            'operation' => $operation,
            'data' => $data
        ];
        
        $response = array_merge($response, $additionalData);
        
        self::send($response);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $statusCode
     */
    public static function sendError(string $message, int $statusCode = 400)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        self::send($response, $statusCode);
    }

    /**
     * Send a 404 Not Found response.
     *
     * @param string $message
     */
    public static function sendNotFound(string $message = 'Resource not found')
    {
        self::sendError($message, 404);
    }

    /**
     * Send a 405 Method Not Allowed response.
     *
     * @param string $allowedMethods
     */
    public static function sendMethodNotAllowed(string $allowedMethods)
    {
        self::sendError('Method Not Allowed', 405);
        header('Allow: ' . $allowedMethods);
    }

    /**
     * Send a 500 Internal Server Error response.
     *
     * @param string $message
     */
    public static function sendServerError(string $message = 'Internal Server Error')
    {
        self::sendError($message, 500);
    }

   public static function sendUnauthorized(){
        self::sendError('Unauthorized', 401);
    }

   
    public function notFound()
    {
        http_response_code(404);
        echo "404 Not Found";
        exit();
    }

    public function serverError()
    {
        http_response_code(500);
        echo "500 Internal Server Error";
        exit();
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public function json($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        return $this;
    }

    private $data = [];
    private $statusCode = 200;

    public function view(string $template, array $data = []): self {
        $viewPath = VIEW_PATH . $template . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View template not found: $viewPath");
        }

        extract($data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        echo $content;
        return $this;
    }

    public function with(string $key, $value): self {
        $_SESSION['flash'][$key] = $value;
        return $this;
    }

    public function redirect(string $path): self {
        header("Location: $path");
        exit();
    }
};
?>