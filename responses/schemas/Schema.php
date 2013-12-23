<?php
namespace OrganicRest\Responses\Schemas;

class Schema implements SchemaInterface {

    public function __construct($data) {

        // Insert values for explicitly defined schema properties only.
        // This should help avoid any surprise data showing up in responses.
        foreach(get_object_vars($this) as $name => $value){

            // Otherwise just assign the data to the property as it
            // is provided
            if(isset($data[$name])) {

                $this->$name = $data[$name];
            }
        }
    }

    public function full()
    {
        return null;
    }

    public function records()
    {
        return null;
    }

    public function getStatus()
    {
        return null;
    }

    public function getCount()
    {
        return null;
    }

}