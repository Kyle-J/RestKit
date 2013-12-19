<?php

return call_user_func(function(){


    $propertyCollection = new \Phalcon\Mvc\Micro\Collection();

    $propertyCollection
        // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
        ->setPrefix('/v1/property')
        // Must be a string in order to support lazy loading
        ->setHandler('\OrganicRest\Controllers\PropertiesController')
        ->setLazy(true);

    // Set Access-Control-Allow headers.
    $propertyCollection->options('/', 'optionsBase');
    $propertyCollection->options('/{id}', 'optionsOne');

    // First paramter is the route, which with the collection prefix here would be GET /example/
    // Second paramter is the function name of the Controller.
    $propertyCollection->get('/{id:[0-9]+}', 'getSingle');

    // This is exactly the same execution as GET, but the Response has no body.
    $propertyCollection->head('/{id:[0-9]+}', 'getSingle');


    $propertyCollection->get('/', 'noId');

    // $id will be passed as a parameter to the Controller's specified function
//    $propertyCollection->get('/{id:[0-9]+}', 'getOne');
//    $propertyCollection->head('/{id:[0-9]+}', 'getOne');
//    $propertyCollection->post('/', 'post');
//    $propertyCollection->delete('/{id:[0-9]+}', 'delete');
//    $propertyCollection->put('/{id:[0-9]+}', 'put');
//    $propertyCollection->patch('/{id:[0-9]+}', 'patch');

    return $propertyCollection;

});