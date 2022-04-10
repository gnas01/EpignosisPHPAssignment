<?php

namespace core;

/**
 * Base class for all the middleware classes.
 * The router will only continue the execution of the request if the middleware returns true.
 */
abstract class Middleware
{
    abstract static public function execute(): bool;
}

?>