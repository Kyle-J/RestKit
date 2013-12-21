<?php
namespace OrganicRest\Responses\Schemas;

class Meta extends Schema {

    public $status = '';

    public $count = 0;

    public $type = '';

    public function setCount($count) {
        $this->count = $count;
    }

}