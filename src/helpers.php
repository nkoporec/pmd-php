<?php

use nkoporec\Pmd\Pmd;

if (! function_exists('pmd')) {

    function pmd(...$args) {
        $pmd = new Pmd();
        return $pmd->send(...$args);
    }

}
