<?php

namespace OrganicRest\Controllers;
use \OrganicRest\Exceptions\HTTPException;


class PropertiesController extends RESTDBController {

    /**
     * Sets which fields may be searched against, and which fields are allowed to be returned in
     * partial responses.  This will be overridden in child Controllers that support searching
     * and partial responses.
     * @var array
     */
    protected $allowedFields = array(
        'search' => array('title', 'city'),
        'partials' => array('id', 'title', 'city')
    );

    /**
     * Sets the default sort column
     *
     * @var string
     */
    protected $sort = 'title';

    /**
     * Sets the default sort direction
     *
     * @var string
     */
    protected $direction = 'desc';


    public function getList($context = array())
    {
        $context = array('columns' => array('id','title','type','address_1','address_2','city','state'));

        return parent::getList($context);
    }

}