<?php
/**
 * API Description Controller
 *
 * @author      Kyle Waters <kyle@theorganicagency.com>
 * @copyright   The Organic Agency
 * @license     http://www.theorganicagency.com/license.txt
 * @version     0.1
 */
namespace OrganicRest\Controllers;
use \OrganicRest\Exceptions\HTTPException;

class DescriptionController extends RESTController {

    public function get()
    {
        $routes = $this->di->getRouter()->getRoutes();
        $routeDefinitions = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'PATCH' => array(), 'DELETE' => array(), 'HEAD' => array(), 'OPTIONS' => array());
        foreach($routes as $route){
            $method = $route->getHttpMethods();
            $routeDefinitions[$method][] = $route->getPattern();
        }

        return $this->provide($routeDefinitions);
    }

}