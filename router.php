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

        /*if the callback function is in a form of a string
        then we need to parse the string as "class + function name
        and then manually instantiate the class and call the coresponding method"*/
        if(is_string($callbackHandler))
        {
            $callbackHandler = $this->getGeneratedCallbackHandler($callbackHandler);
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

    public function getGeneratedCallbackHandler(string $callbackHandler)
    {
        $callbackFuntionParts = explode('::', $callbackHandler);
        $className = $callbackFuntionParts[0];
        $functionName = $callbackFuntionParts[1];

        $class = new $className();

        $callbackHandler = [$class, $functionName];

        return $callbackHandler;
    }
    

}

?>