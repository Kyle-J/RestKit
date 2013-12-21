<?php
namespace OrganicRest\Responses\Schemas;

class REST extends Schema {

    public $_meta = null;

    public $_state = null;

    public $records = array();

    public $links = null;


    public function __construct($data) {

        if(isset($data['meta']))
            $this->_meta = new Meta($data['meta']);

        if(isset($data['state']))
            $this->_state = new State($data['state']);

        parent::__construct($data);


      //  $this->records = array();

      //  $this->_state = new Links($data['state']);

    }


    public function toJSON() {



    }

}