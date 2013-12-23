<?php
namespace OrganicRest\Responses;

class JSONResponse extends Response{

	protected $snake = true;
	protected $envelope = true;

	public function __construct(){
		parent::__construct();
	}

	public function send($schema, $error = false)
    {

        // Error's come from HTTPException.  This helps set the proper envelope data
		$response = $this->di->get('response');



        // If the query string 'envelope' is set to false, do not use the envelope.
		// Instead, return headers.
		$request = $this->di->get('request');


        // Store a record count before the schema gets array converted
        $count = $schema->getCount();


        //TODO: Some refactoring to error / exception handling needed now!
//		$success = ($error) ? 'ERROR' : 'SUCCESS';
//
//        $schema->_meta->status = $success;
        //  if($error) $schema->getCount() = 1;



        // If the envelope is on (default), the JSON response will contain
        // meta, state and links data. If envelope is false, the response
        // will contain records only.
		if($request->get('envelope', null, null) !== 'false'){

            $body = $schema->full();

		} else {

			$response->setHeader('X-Record-Count', count($schema->records));
			$response->setHeader('X-Status', $schema->getStatus());

            $body = $schema->records();

		}

		$response->setContentType('application/json;application&v=1');

        // ETags are important for caching and performance but when
        // there are thousands of rows serialising them uses too much
        // memory.
        //
        // Only generate ETags for results sets with 20 records or less
        // this should cover most standard UI use cases, and for other cases
        // client side caching won't be applied anyway.
        //
        // TODO: Keep looking for a way to generate a good ETag for large record sets
        // TODO: without the performance hit
        if($count <= 20) $response->setHeader('E-Tag', md5(serialize($body['records'])));



		// HEAD requests are detected in the parent constructor. HEAD does everything exactly the
		// same as GET, but contains no body.
		if(!$this->head) $response->setJsonContent($body);

		$response->send();

		return $this;
	}

	public function convertSnakeCase($snake){
		$this->snake = (bool) $snake;
		return $this;
	}

	public function useEnvelope($envelope){
		$this->envelope = (bool) $envelope;
		return $this;
	}

}
