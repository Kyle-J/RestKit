<?php

use \Phalcon\Mvc\Model,
    \Phalcon\Mvc\Model\Message,
    \Phalcon\Mvc\Model\Validator\InclusionIn,
    \Phalcon\Mvc\Model\Validator\Uniqueness;

class Properties extends \Phalcon\Mvc\Model
{
    public $id;

    public $title;

    public $city;

    public $address_1;

    public $address_2;

    public $state;

    public function columnMap()
    {
        //Keys are the real names in the table and
        //the values their names in the application
        return array(
            'id' => 'id',
            'title' => 'title',
            'type' => 'type',
            'city' => 'city',
            'address_1' => 'address_1',
            'address_2' => 'address_2',
            'state' => 'state'
        );
    }

//    public function validation()
//    {
//        //Type must be: droid, mechanical or virtual
//        $this->validate(new InclusionIn(
//            array(
//                "field"  => "type",
//                "domain" => array("flat", "house", "commercial")
//            )
//        ));
//
//        //Robot name must be unique
//        $this->validate(new Uniqueness(
//            array(
//                "field"   => "title",
//                "message" => "The property name must be unique"
//            )
//        ));
//
//        //Check if any messages have been produced
//        if ($this->validationHasFailed() == true) {
//            return false;
//        }
//    }

}