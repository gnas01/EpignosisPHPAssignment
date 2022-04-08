<?php
    class CustomRule
    {
        private string $error; 
        private $callback;

        public function __construct(string $error, $callback)
        {
            $this->error = $error;
            $this->callback = $callback;
        }

        public function getError(): string
        {
            return $this->error;
        }

        public function getCallback() 
        {
            return $this->callback;
        }
    }
?>