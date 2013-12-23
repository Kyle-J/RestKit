<?php
namespace OrganicRest\Responses;

class Http extends Response {

    // [Successful 2xx]
    const OK                        = 200;
    const CREATED                   = 201;
    const ACCEPTED                  = 202;
    const NO_CONTENT                = 204;
    const RESET_CONTENT             = 205;
    const PARTIAL_CONTENT           = 206;

    // [Redirection 3xx]
    const MOVED_PERMANENTLY         = 301;
    const FOUND                     = 302;
    const SEE_OTHER                 = 303;
    const NOT_MODIFIED              = 304;
    const USE_PROXY                 = 305;
    const TEMPORARY_REDIRECT        = 307;

    // [Client Error 4xx]
    const BAD_REQUEST               = 400;
    const UNAUTHORIZED              = 401;
    const FORBIDDEN                 = 403;
    const NOT_FOUND                 = 404;
    const METHOD_NOT_ALLOWED        = 405;
    const NOT_ACCEPTABLE            = 406;
    const REQUEST_TIMEOUT           = 408;
    const CONFLICT                  = 409;
    const GONE                      = 410;
    const LENGTH_REQUIRED           = 411;
    const PRECONDITION_FAILED       = 412;
    const REQUEST_ENTITY_TOO_LARGE  = 413;
    const REQUEST_URI_TOO_LONG      = 414;
    const UNSUPPORTED_MEDIA_TYPE    = 415;
    const EXPECTATION_FAILED        = 417;

    // [Server Error 5xx]
    const INTERNAL_SERVER_ERROR     = 500;
    const NOT_IMPLEMENTED           = 501;
    const BAD_GATEWAY               = 502;
    const SERVICE_UNAVAILABLE       = 503;
    const GATEWAY_TIMEOUT           = 504;
    const VERSION_NOT_SUPPORTED     = 505;

}