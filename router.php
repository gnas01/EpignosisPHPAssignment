<?php

class Router
{
    private array $handlers;

    private $notFoundHandler;
    
    private const METHOD_POST = 'POST';
    private const METHOD_GET = 'GET';

    public function get(string $path, $handler)
    {
        $this->addHandler(self::METHOD_GET, $path, $handler);
    }

    public function post(string $path, $handler)
    {
        $this->addHandler(self::METHOD_POST, $path, $handler);
    }

    private function addHandler(string $method, string $path, $handler)
    {
        $this->handlers[$method . $path] = 
        [
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
        ];
    }

    public function addNotFoundHandler($handler)
    {
        $this->notFoundHandler = $handler;
    }

    public function run()
    {
        $requestURI = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestURI['path'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $callbackHandler = null;

        foreach($this->handlers as $handler)
        {
            $doesMatch = $handler['path'] === $requestPath && $handler['method'] === $requestMethod;

            if($doesMatch)
            {
                $callbackHandler = $handler['handler'];
                break;
            }
        }

        if(!$callbackHandler)
        {
            if(!empty($this->notFoundHandler))
            {
                $callbackHandler = $this->notFoundHandler;
            }
        }

        call_user_func_array($callbackHandler, [array_merge($_GET, $_POST)]);
    }
}