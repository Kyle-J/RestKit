<?php
namespace OrganicRest\Responses\Schemas;

class REST extends Schema {

    public $status = '';

    public $href = '';

    public $first = '';

    public $previous = '';

    public $next = '';

  //  public $last = '';

    public $count = 0;

    public $limit = 0;

    public $offset = 0;

    public $sort = '';

    public $direction = '';

    public $search = '';

    public $fields = '';

    public $records = array();

    public function __construct($data) {

        // If you need to manipulate the incoming data to
        // build your own schema you can do so in here.

        parent::__construct($data);

    }

    public function full()
    {

//        $result = array(
//
//            'meta'    => (array) $this->meta,
//            'state'   => (array) $this->state,
//            'records' => (array) $this->records,
//            'links'   => (array) $this->links
//
//        );

        $result = (array) $this;

        return $result;
    }

    public function records()
    {
        $result = array('records' => (array) $this->records);

        return $result;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCount()
    {
        return $this->count;
    }

}