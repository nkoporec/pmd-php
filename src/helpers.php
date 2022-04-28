<?php

use nkoporec\Pmd\Pmd;

if (! function_exists('pmd')) {

    /**
     * Poor's man debugger.
     *
     * @param mixed $args
     *   Args to send.
     *
     * @return string
     *  The response from PMD.
     */
    function pmd(...$args): string
    {
        $pmd = new Pmd();

        return $pmd->send(...$args);
    }
}
