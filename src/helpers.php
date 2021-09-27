<?php

use Nkoporec\Dump\Dump;

if (! function_exists('dump')) {
    function dump(...$args)
    {
        $dump = new Dump();

        return $dump->send(...$args);
    }
}
