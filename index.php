<?php

use Phalcon\Mvc\Micro\Collection,
	Phalcon\Config\Adapter\Ini as IniConfig;

/**
 * By default, namespaces are assumed to be the same as the path.
 * This function allows us to assign namespaces to alternative folders.
 * It also puts the classes into the PSR-0 autoLoader.
 */
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(array(
//	'Models' => __DIR__ . '/models/',
	'OrganicRest\Controllers' => __DIR__ . '/controllers/',
	'OrganicRest\Exceptions' => __DIR__ . '/exceptions/',
	'OrganicRest\Responses' => __DIR__ . '/responses/'
))->register();

$loader->registerDirs(array(
    __DIR__ .'/models/'
))->register();

/**
 * The DI is our direct injector.  It will store pointers to all of our services
 * and we will insert it into all of our controllers.
 * @var DefaultDI
 */
$di = new \Phalcon\DI\FactoryDefault();


/**
 * Return array of the Collections, which define a group of routes, from
 * routes/collections.  These will be mounted into the app itself later.
 */
$di->set('collections', function(){
	return include('./routes/routeLoader.php');
});

/**
 * $di's setShared method provides a singleton instance.
 * If the second parameter is a function, then the service is lazy-loaded
 * on its first instantiation.
 */
$di->setShared('config', function() {
	return new IniConfig("config/config.ini");
});

// As soon as we request the session service, it will be started.
$di->setShared('session', function(){
	$session = new \Phalcon\Session\Adapter\Files();
	$session->start();
	return $session;
});

$di->set('modelsCache', function() {

	//Cache data for one day by default
	$frontCache = new \Phalcon\Cache\Frontend\Data(array(
		'lifetime' => 1800
	));

	//File cache settings
	$cache = new \Phalcon\Cache\Backend\Apc($frontCache, array(
        "prefix" => 'modelCache',
	));

	return $cache;
});


/**
 * Database setup.  Here, we'll use a simple SQLite database of Disney Princesses.
 */
//Set up the database service
$di->set('db', function(){
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "localhost",
        "username" => "root",
        "password" => "vodofx",
        "dbname" => "properties"
    ));
});

/**
 * If our request contains a body, it has to be valid JSON.  This parses the 
 * body into a standard Object and makes that vailable from the DI.  If this service
 * is called from a function, and the request body is nto valid JSON or is empty,
 * the program will throw an Exception.
 */
$di->setShared('requestBody', function() {
	$in = file_get_contents('php://input');
	$in = json_decode($in, FALSE);

	// JSON body could not be parsed, throw exception
	if($in === null){
		throw new HTTPException(
			'There was a problem understanding the data sent to the server by the application.',
			409,
			array(
				'dev' => 'The JSON body sent to the server was unable to be parsed.',
				'internalCode' => 'REQ1000',
				'more' => ''
			)
		);
	}

	return $in;
});

$di['modelsMetadata'] = function() {

    // Create a meta-data manager with APC
    $metaData = new \Phalcon\Mvc\Model\MetaData\Apc(array(
        "lifetime" => 86400,
        "prefix"   => "meta-data-cache"
    ));

    return $metaData;
};

/**
 * Out application is a Micro application, so we mush explicitly define all the routes.
 * For APIs, this is ideal.  This is as opposed to the more robust MVC Application
 * @var $app
 */
$app = new Phalcon\Mvc\Micro();
$app->setDI($di);

/**
 * Before every request, make sure user is authenticated.
 * Returning true in this function resumes normal routing.
 * Returning false stops any route from executing.
 */

/*
This will require changes to fit your application structure.
It supports Basic Auth, Session auth, and Exempted routes.

It also allows all Options requests, as those tend to not come with
cookies or basic auth credentials and Preflight is not implemented the
same in every browser.
*/

/*
$app->before(function() use ($app, $di) {

	// Browser requests, user was stored in session on login, replace into DI
	if ($di->getShared('session')->get('user') != false) {
		$di->setShared('user', function() use ($di){
			return $di->getShared('session')->get('user');
		});
		return true;
	}

	// Basic auth, for programmatic responses
	if($app->request->getServer('PHP_AUTH_USER')){
		$user = new \OrganicRest\Controllers\UsersController();
		$user->login(
			$app->request->getServer('PHP_AUTH_USER'),
			$app->request->getServer('PHP_AUTH_PW')
		);
		return true;
	}


	// All options requests get a 200, then die
	if($app->__get('request')->getMethod() == 'OPTIONS'){
		$app->response->setStatusCode(200, 'OK')->sendHeaders();
		exit;
	}


	// Exempted routes, such as login, or public info.  Let the route handler
	// pick it up.
	switch($app->getRouter()->getRewriteUri()){
		case '/users/login':
			return true;
			break;
		case '/example/route':
			return true;
			break;
	}

	// If we made it this far, we have no valid auth method, throw a 401.
	throw new \OrganicRest\Exceptions\HTTPException(
		'Must login or provide credentials.',
		401,
		array(
			'dev' => 'Please provide credentials by either passing in a session token via cookie, or providing password and username via BASIC authentication.',
			'internalCode' => 'Unauth:1'
		)
	);

	return false;
});*/


/**
 * Mount all of the collections, which makes the routes active.
 */
foreach($di->get('collections') as $collection){
	$app->mount($collection);
}


/**
 * After a route is run, usually when its Controller returns a final value,
 * the application runs the following function which actually sends the response to the client.
 *
 * The default behavior is to send the Controller's returned value to the client as JSON.
 * However, by parsing the request querystring's 'type' paramter, it is easy to install
 * different response type handlers.  Below is an alternate csv handler.
 */
$app->after(function() use ($app) {

	// OPTIONS have no body, send the headers, exit
	if($app->request->getMethod() == 'OPTIONS'){
		$app->response->setStatusCode('200', 'OK');
		$app->response->send();
		return;
	}

	// Respond by default as JSON
	if(!$app->request->get('format') || $app->request->get('format') == 'json'){

		// Results returned from the route's controller.  All Controllers should return an array
		$records = $app->getReturnedValue();

		$response = new \OrganicRest\Responses\JSONResponse();
		$response->useEnvelope(true) //this is default behavior
			->convertSnakeCase(true) //this is also default behavior
			->send($records);

		return;
	}
	else if($app->request->get('format') == 'csv'){

		$records = $app->getReturnedValue();
		$response = new \OrganicRest\Responses\CSVResponse();
		$response->useHeaderRow(true)->send($records);

		return;
	}
	else {
		throw new \OrganicRest\Exceptions\HTTPException(
			'Could not return results in specified format',
			403,
			array(
				'dev' => 'Could not understand type specified by type parameter in query string.',
				'internalCode' => 'NF1000',
				'more' => 'Type may not be implemented. Choose either "csv" or "json"'
			)
		);
	}
});

/**
 * The notFound service is the default handler function that runs when no route was matched.
 * We set a 404 here unless there's a suppress error codes.
 */
$app->notFound(function () use ($app) {
	throw new \OrganicRest\Exceptions\HTTPException(
		'Not Found.',
		404,
		array(
			'dev' => 'That route was not found on the server.',
			'internalCode' => 'NF1000',
			'more' => 'Check route for misspellings.'
		)
	);
});

/**
 * If the application throws an HTTPException, send it on to the client as json.
 * Elsewise, just log it.
 * TODO:  Improve this.
 */
set_exception_handler(function($exception) use ($app){
	//HTTPException's send method provides the correct response headers and body
	if(is_a($exception, 'OrganicRest\\Exceptions\\HTTPException')){
		$exception->send();
	}
	error_log($exception);
	error_log($exception->getTraceAsString());
});

$app->handle();

