<?php
namespace OrganicRest\Responses\Schemas;

interface SchemaInterface {

    public function getCount();

    public function getStatus();

    public function full();

    public function records();

}