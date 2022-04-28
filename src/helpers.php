<?php

use nkoporec\Pmd\Pmd;

if (! function_exists('pmd')) {
<<<<<<< HEAD
    function pmd(...$args) {
=======
    function pmd(...$args)
    {
>>>>>>> f106334892e4b8ed1c08458e20afed79cd476f21
        $pmd = new Pmd();

        return $pmd->send(...$args);
    }
}
