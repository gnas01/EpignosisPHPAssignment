<?php

/** Class that provides necessary functions 
 * for creating and handling routes.
*/
class Router
{
    private array $handlers;

    /** Callback function that will be called
     * when the route is not found.
     */
    private $notFoundHandler;
    
    private const METHOD_POST = 'POST';
    private const METHOD_GET = 'GET';

    /**
     * Set get route.
     */
    public function get(string $path, $handler, $middleware = null)
    {
        $this->addHandler(self::METHOD_GET, $path, $handler, $middleware);
    }

    /** 
     * Set post route.
     */
    public function post(string $path, $handler, $middleware = null)
    {
        $this->addHandler(self::METHOD_POST, $path, $handler, $middleware);
    }
    
    /**
     * Stores the handler for the route.
     *
     * @param string $method The type of request.
     * @param string $path The path of the route.
     * @param [type] $handler The callback function that will be called when the route is matched.
     * @param [type] $middleware optional middleware to be called before the callback.
     * @return void
     */
    private function addHandler(string $method, string $path, $handler, $middleware)
    {
        $this->handlers[$method . $path] = 
        [
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Sets the callback function that will be called when the route is not found.
     *
     * @param [type] $handler The callback function that will be called when the route is not found.
     * @return void
     */
    public function addNotFoundHandler($handler)
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * When a route is hit, this function will be called.
     * It will check if the route matches any of the stored handlers
     * and call the callback function if it does.
     *
     * @return void
     */
    public function run()
    {
        $requestURI = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestURI['path'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $callbackHandler = null;

        foreach($this->handlers as $handler)
        {
            $doesMatch = $handler['path'] === $requestPath && $handler['method'] === $requestMethod;


            if(!$doesMatch)
                continue;


            if($handler['middleware'])
            {
                $middleware = $handler['middleware'];
                $middleware();
            }

            $callbackHandler = $handler['handler'];
            break;
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

    /**
     * Generates a callback function from a string.
     *
     * @param string $callbackHandler The string that contains the class and method name.
     * @return callable The callback function.
     */
    public function getGeneratedCallbackHandler(string $callbackHandler): callable
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