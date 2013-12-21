<?php

namespace OrganicRest\Controllers;
use \OrganicRest\Exceptions\HTTPException;
use Phalcon\Mvc\Model\Resultset;

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
    protected $direction = 'desc';


    /**
     * Get properties
     *
     * @var string
     */
    public function get(){

        $context = array(
            'models'     => array('Properties'),
            'columns'    => array('id','title','type','address_1','address_2','city','state'),
            'limit'  => $this->limit,
            'offset' => $this->offset,
            'order'  => $this->sort .' '. $this->direction
        );

        $queryBuilder = new \Phalcon\Mvc\Model\Query\Builder($context);

        // Build search conditions
        if($this->isSearch){

            $context['conditions'] = '';
            $context['bind'] = array();

            $last_field = @end(array_keys($this->searchFields));
            foreach ($this->searchFields as $field => $value) {

                $context['conditions'] .= " $field LIKE :{$field}_value:";
                $context['bind'][$field.'_value'] = '%'.$value.'%';

                if($field != $last_field) $context['conditions'] .= ' OR ';

            }

            $queryBuilder->where($context['conditions'], $context['bind']);
        }

        // Get the Phalcon Query Language output to use as APC key
        $phql  = $queryBuilder->getPhql();

        // Check if records exist in cache
        if(apc_exists($phql)) {

            $records = apc_fetch($phql);

            return $this->provide($records);

        // Otherwise run the query
        }else{

            $query = $queryBuilder->getQuery();

            $query->cache(array('key' => $phql, 'lifetime' => 1800)); // 30 minutes
            $records = $query->execute()->setHydrateMode(Resultset::TYPE_RESULT_FULL)->toArray();

            apc_store($phql, $records);

            return $this->provide($records);
        }

    }

//    public function get()
//    {
//        // ORM (slower style)
//        $properties = new Properties();
//
//        $context = array(
//            'limit'  => $this->limit,
//            'offset' => $this->offset,
//            'order'  => $this->sort .' '. $this->direction
//        );
//
//        if($this->isSearch){
//
//            $context['conditions'] = '';
//            $context['bind'] = array();
//
//            $last_field = @end(array_keys($this->searchFields));
//            foreach ($this->searchFields as $field => $value) {
//
//                $context['conditions'] .= " $field LIKE :{$field}_value:";
//                $context['bind'][$field.'_value'] = '%'.$value.'%';
//
//                if($field != $last_field) $context['conditions'] .= ' OR ';
//
//            }
//        }
//
//        $list = $properties::find($context);
//
//        $records = array();
//        foreach($list as $item)
//            $records[] = $item->toArray();
//
//
//        return $this->provide($records);
//    }

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