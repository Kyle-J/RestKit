<?php

return call_user_func(function(){

    $defaultCollection = new \Phalcon\Mvc\Micro\Collection();

    $defaultCollection
        ->setPrefix('/v1/')
        ->setHandler('\OrganicRest\Controllers\DescriptionController')
        ->setLazy(true);

    // Set Access-Control-Allow headers.
    $defaultCollection->options('/', 'optionsBase');

    $defaultCollection->get('/', 'get');

    return $defaultCollection;

});