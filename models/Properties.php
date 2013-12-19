<?php

namespace OrganicRest\Models;
use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Properties extends Model
{
    public $id;

    public $title;

    public function validation()
    {
        //Type must be: droid, mechanical or virtual
        $this->validate(new InclusionIn(
            array(
                "field"  => "type",
                "domain" => array("flat", "house", "commercial")
            )
        ));

        //Robot name must be unique
        $this->validate(new Uniqueness(
            array(
                "field"   => "title",
                "message" => "The property name must be unique"
            )
        ));

        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}