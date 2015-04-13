<?php

class CurlTask
{

    /** @var array */
    public $options;
    /** @var callable */
    public $callback;

    /**
     * @param array $options
     * @param callable $callback
     * @throws CException
     */
    public function __construct($options, $callback)
    {
        $this->options = $options;
        if (!is_callable($callback))
            throw new CException('Curl task callback is not callable');
        $this->callback = $callback;
    }

}
