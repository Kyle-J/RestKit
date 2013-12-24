RestCore 0.1
============

https://bitbucket.org/organicdevelopment/restcore

A base project for APIs using the [Phalcon][phalcon] framework based on PhalconRest by Sean Moore
-------------------------------------------------------------------------------------------------

Updated and adapted by Kyle Waters kyle@theorganicagency.com


Changes from PhalconRest
------------------------

 - Added state to RSON Response
 - Added sorting and direction to options
 - Added 'type' to meta data
 - Refactored results passing from Rest controller to response. See RestController->provide().
 - Added properties example
 - Changed 'data' folder to 'schema'
 - Removed unused files / folders
 - Removed phalcon-rest example data and files
 - Change search parameter name to ?search
 - Updated partial fields and allowed fields logic
 - Updated and cleaned up parse params code
 - Added Schemas
 - Changed JSON response schema and implemented HATEOAS best practises
 - Added search and field params to the response
 - Updated status in response to HTTP code (was "SUCCESS")
 - Added RESTDB controller for processing standard look ups via database
 - Change queries to use query builder instead of ORM for speed (200% faster lookups)
 - Added APC caching for record look-ups
 - Removed version number from URL and added to content type (application/json;application&v=1)
 - Changed ETag to only generate for look ups under 20 records (was causing PHP memory limit errors for large result sets)

 ** The changes are now becoming too many to list, many parts have stayed a long way from the original framework now.


Original Project - https://github.com/cmoore4/phalcon-rest


Many changed made based on research and recommendations for best practise REST API implementation.

http://www.youtube.com/watch?v=hdSrT4yjS1g
http://www.youtube.com/watch?v=ITmcAGvfcJI
http://www.youtube.com/watch?v=HW9wWZHWhnI

http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api



The Phalcon framework is an awesome PHP framework that exists as a C-extension to the language.
This allows it to be incredibly fast.  But aside from its quickness, it is an amazingly
powerful framework with excellent [documentation][phalconDocs] that follows many best practises of
modern software development.  This includes using the Direct Injection pattern to handle service
resolution across classes, a PSR-0 compliant auto-loader, MVC architecture (or not), caching
handlers for database, flat file, redis, etc.. and a ton of additional features.

The purpose of this project is to establish a base project with Phalcon that uses the best practices
from the Phalcon Framework to implement best practises of [API Design][apigeeBook].

Writing routes that respond with JSON is easy in any of the major frameworks.  What I've done here is to 
go beyond that and extend the framework such that APIs written using this project are pragmatically 
REST-ish and have convenience methods and patterns implemented that are more than a simple
'echo json_encode($array)'.

Provided are robust Error messages, controllers that parse searching strings and partial responses,
response classes for sending multiple MIME types based on the request, and examples of how to implement
authentication in a few ways, as well as a few templates for implementing common REST-ish tasks.

It is highly recommended to read through the index.php, HTTPException.php and RESTController.php files, as
I've tried to comment them extensively.


API Assumptions
---------------

**URL Structure**

```
base.tld/collection?search=(search1:value1,search2:value2)&fields=(field1,field2,field3)&limit=10&offest=20&type=csv
base.tld/resource/id?fields=(field1,field2,field3)
```

**Request Bodies**

Request bodies will be submitted as valid JSON.

The Fields
-----------

**Search**

Searches are determined by the 'search' parameter.  Following that is a parenthesis enclosed list of key:value pairs, separated by commas.

> ex: search=(city:Exeter,title:Property 1)

**Search Type **

Changed search type. Filter uses 'AND' glue, default uses 'OR' glue.

> ex: search_type=filter

**Partial Responses**

Partial responses are used to only return certain explicit fields from a record. They are determined by the 'fields' parameter, which is a list of field names separated by commas, enclosed in parenthesis.

> ex: fields=(id,title,location)

**Limit and Offset**

Often used to paginate large result sets.  Offset is the record to start from, and limit is the number of records to return.

> ex: limit=20&offset=20   will return results 21 to 40

**Return Format**

Overrides any accept headers.  JSON is assumed otherwise.  Return type handler must be implemented.

> ex: format=xml

**Suppressed Error Codes**

Some clients require all responses to be a 200 (Flash, for example), even if there was an application error.
With this parameter included, the application will always return a 200 response code, and clients will be
responsible for checking the response body to ensure a valid response.

> ex: suppress_error_codes=true

Responses
---------

All route controllers must return an array.  This array is used to create the response object.

**JSON**

JSON is the default response type.  It comes with an envelope wrapper, so responses will look like this:

```
GET properties?search=(city:exeter)&offset=1&limit=2&fields=id,title

{
    "status": 200,
    "href": "http://organic-rest.loc/properties?limit=2&offset=1&sort=title&direction=desc&fields=id,title&search=(city:exeter)",
    "first": "http://organic-rest.loc/properties?limit=2&offset=0&sort=title&direction=desc&fields=id,title&search=(city:exeter)",
    "previous": "http://organic-rest.loc/properties?limit=2&offset=0&sort=title&direction=desc&fields=id,title&search=(city:exeter)",
    "next": "http://organic-rest.loc/properties?limit=2&offset=2&sort=title&direction=desc&fields=id,title&search=(city:exeter)",
    "count": 2,
    "limit": "2",
    "offset": "1",
    "sort": "title",
    "direction": "desc",
    "search": {
        "city": "exeter"
    },
    "fields": [
        "id",
        "title"
    ],
    "records": [
        {
            "id": "9005",
            "title": "Property 2"
        },
        {
            "id": "8005",
            "title": "Property 2"
        }
    ]
}

The envelope can be suppressed for responses via the 'envelope=false' query parameter.  This will return just the record set by itself as the body, and the meta information via X- headers.

Often times, database field names are snake_cased.  However, when working with an API, developers 
generally prefer JSON fields to be returned in camelCase (many API requests are from browsers, in JS).
This project will by default convert all keys in a records response from snake_case to camelCase.

This can be turned off for your API by setting the JSONResponse's function "convertSnakeCase(false)".

**CSV**

CSV is the other implemented handler.  It uses the first record's keys as the header row, and then creates a csv from each row in the array.  The header row can be toggled off for responses.

```
title,type,city
Property 1, Flat, Exeter
```

Errors
-------

OrganicRest\Exception\HTTPException extends PHP's native exceptions.  Throwing this type of exception
returns a nicely formatted JSON response to the client.

```
throw new \OrganicRest\Exceptions\HTTPException(
	'Could not return results in specified format',
	403,
	array(
		'dev' => 'Could not understand type specified by type paramter in query string.',
		'internalCode' => 'NF1000',
		'more' => 'Type may not be implemented. Choose either "csv" or "json"'	
	)
);
```

Returns this:

```
{
    "_meta": {
        "status": "ERROR"
    },
    "error": {
        "devMessage": "Could not understand type specified by type paramter in query string.",
        "error": 403,
        "errorCode": "NF1000",
        "more": "Type may not be implemented. Choose either \"csv\" or \"json\"",
        "userMessage": "Could not return results in specified format"
    }
}
```

[phalcon]: http://phalconphp.com/index
[phalconDocs]: http://docs.phalconphp.com/en/latest/
[apigeeBook]: https://blog.apigee.com/detail/announcement_new_ebook_on_web_api_design