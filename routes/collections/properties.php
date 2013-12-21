<?php

return call_user_func(function(){


    $propertiesCollection = new \Phalcon\Mvc\Micro\Collection();

    $propertiesCollection
        // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
        ->setPrefix('/v1/properties')
        // Must be a string in order to support lazy loading
        ->setHandler('\OrganicRest\Controllers\PropertiesController')
        ->setLazy(true);

    // Set Access-Control-Allow headers.
    $propertiesCollection->options('/', 'optionsBase');
    $propertiesCollection->options('/{id}', 'optionsOne');

    // First paramter is the route, which with the collection prefix here would be GET /example/
    // Second paramter is the function name of the Controller.
    $propertiesCollection->get('/', 'get');
    // This is exactly the same execution as GET, but the Response has no body.
    $propertiesCollection->head('/', 'get');

    // $id will be passed as a parameter to the Controller's specified function
//    $propertiesCollection->get('/{id:[0-9]+}', 'getOne');
//    $propertiesCollection->head('/{id:[0-9]+}', 'getOne');
//    $propertiesCollection->post('/', 'post');
//    $propertiesCollection->delete('/{id:[0-9]+}', 'delete');
//    $propertiesCollection->put('/{id:[0-9]+}', 'put');
//    $propertiesCollection->patch('/{id:[0-9]+}', 'patch');

    return $propertiesCollection;

});