<?php

return call_user_func(function(){

    $defaultCollection = new \Phalcon\Mvc\Micro\Collection();

    $defaultCollection
        ->setPrefix('/')
        ->setHandler('\OrganicRest\Controllers\DefaultController')
        ->setLazy(true);

    // Set Access-Control-Allow headers.
    $defaultCollection->options('/', 'optionsBase');

    $defaultCollection->get('/', 'get');

    return $defaultCollection;

});