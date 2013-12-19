<?php

namespace OrganicRest\Controllers;
use OrganicRest\Models\Properties as Properties;
use \OrganicRest\Exceptions\HTTPException;

class PropertiesController extends RESTController {

    /**
     * Sets which fields may be searched against, and which fields are allowed to be returned in
     * partial responses.  This will be overridden in child Controllers that support searching
     * and partial responses.
     * @var array
     */
    protected $allowedFields = array(
        'search' => array('title', 'city'),
        'partials' => array()
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
    protected $direction = 'asc';


    /**
     * Get properties
     *
     * @var string
     */
    public function get(){

        $properties = new Properties();

        $context = array(
            'limit'  => $this->limit,
            'offset' => $this->offset,
            'order'  => $this->sort .' '. $this->direction
        );

        if($this->isSearch){

            $context['conditions'] = '';
            $context['bind'] = array();

            $last_field = @end(array_keys($this->searchFields));
            foreach ($this->searchFields as $field => $value) {

                $context['conditions'] .= " $field LIKE :{$field}_value:";
                $context['bind'][$field.'_value'] = '%'.$value.'%';

                if($field != $last_field) $context['conditions'] .= ' OR ';

            }
        }

        $list = $properties::find($context);

        $response = array();
        foreach($list as $item)
            $response[] = $item->toArray();

        return $this->provide($response);

    }

    /**
     * Get a single property by ID
     *
     * @var string
     */
    public function getSingle($id)
    {
        $properties = new Properties();

        $property = $properties::find($id);

        if(!count($property)) return false;

        $response = array();
        foreach($property as $property)
            $response[] = $property->toArray();

        return $this->provide($response);

    }

    public function noId()
    {
        throw new \OrganicRest\Exceptions\HTTPException(
            'Missing function call parameters',
            403,
            array(
                'dev' => 'Cannot access property without ID.',
                'internalCode' => 'NF1000',
                'more' => 'Ensure URL follows the format /property/{id}'
            )
        );
    }

}