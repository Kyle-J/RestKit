<?php
namespace OrganicRest\Responses\Schemas;

class Schema {

    public function __construct($data) {

        // Insert values for explicitly defined schema properties only.
        // This should help avoid any surprise data showing up in responses.
        $vars = get_object_vars($this);

        foreach(get_object_vars($this) as $name => $value){
            if(isset($data[$name])) $this->$name = $data[$name];
        }
    }

    public function full()
    {
        $result = array(

            '_meta'   => (array) $this->_meta,
            '_state'  => (array) $this->_state,
            'records' => (array) $this->records,
            'links'   => (array) $this->links

        );

        return $result;
    }

    public function raw()
    {
        $result = array('records' => (array) $this->records);

        return $result;
    }

}