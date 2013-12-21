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

class DefaultController extends RESTController {

    public function get() {

        throw new \OrganicRest\Exceptions\HTTPException(
            'Not Found.',
            404,
            array(
                'dev' => 'That route was not found on the server.',
                'internalCode' => 'NF1000',
                'more' => 'Please ensure you are trying to access resources using a valid RESTful URL.'
            )
        );

    }
}