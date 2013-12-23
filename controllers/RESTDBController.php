<?php
namespace OrganicRest\Controllers;
use \OrganicRest\Exceptions\HTTPException;
use Phalcon\Mvc\Model\Resultset;

class RESTDBController extends RESTController {

    /**
     * TODO: Implement fast get total method
     *
     * @param array $context
     * @return mixed
     */
    public function getTotal($context = array())
    {
        $router = new \Phalcon\Mvc\Router();
        $router->handle();
        $name =  ucfirst($router->getControllerName());

        $context = array_merge(array(
            'models'     => array($name),
        ), $context);

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


            // Otherwise run the query
        }else{

            $records = $queryBuilder->getQuery()->execute()->setHydrateMode(Resultset::TYPE_RESULT_FULL)->toArray();

            apc_store($phql, $records);

        }

        //return $count;

    }

    /**
     * Get list
     *
     * @var string
     */
    public function getList($context = array())
    {

        $router = new \Phalcon\Mvc\Router();
        $router->handle();
        $name =  ucfirst($router->getControllerName());

        $context = array_merge(array(
            'models'     => array($name),
            'limit'  => $this->limit,
            'offset' => $this->offset,
            'order'  => $this->sort .' '. $this->direction
        ), $context);

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


        // Otherwise run the query
        }else{

            $records = $queryBuilder->getQuery()->execute()->setHydrateMode(Resultset::TYPE_RESULT_FULL)->toArray();

            apc_store($phql, $records);

        }

        return $this->provide($records);

    }

    /**
     * Get a single property by ID
     *
     * @var string
     */
    public function getItem($id)
    {
        $router = new \Phalcon\Mvc\Router();
        $router->handle();
        $name =  ucfirst($router->getControllerName());

        // TODO: Inflector here
        $name = 'Properties';

        $model = new $name();

        $records = $model::find($id);

        if(!count($records)) return false;

        $response = array();
        foreach($records as $record)
            $response[] = $record->toArray();

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