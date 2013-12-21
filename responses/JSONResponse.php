<?php
namespace OrganicRest\Responses;

class JSONResponse extends Response{

	protected $snake = true;
	protected $envelope = true;

	public function __construct(){
		parent::__construct();
	}

	public function send($schema, $error = false){

        // Error's come from HTTPException.  This helps set the proper envelope data
		$response = $this->di->get('response');
		$success = ($error) ? 'ERROR' : 'SUCCESS';

        $schema->_meta->status = $success;
        if($error) $schema->_meta->count = 1;

        // If the query string 'envelope' is set to false, do not use the envelope.
		// Instead, return headers.
		$request = $this->di->get('request');

        // If the envelope is on (default), the JSON response will contain
        // meta, state and links data. If envelope is false, the response
        // will contain records only.
		if($request->get('envelope', null, null) !== 'false'){

            $body = $schema->full();

		} else {

			$response->setHeader('X-Record-Count', count($schema->records));
			$response->setHeader('X-Status', $success);

            $body = $schema->raw();

		}

		$response->setContentType('application/json');
	//	$response->setHeader('E-Tag', md5(serialize($body)));

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
