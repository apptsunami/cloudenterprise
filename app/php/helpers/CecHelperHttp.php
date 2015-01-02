<?php
/* Copyright (C) 2008 App Tsunami, Inc. */
/* 
 *  This program is free software: you can redistribute it and/or modify 
 *  it under the terms of the GNU General Public License as published by 
 *  the Free Software Foundation, either version 3 of the License, or 
 *  (at your option) any later version. 
 * 
 *  This program is distributed in the hope that it will be useful, 
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 *  GNU General Public License for more details. 
 * 
 *  You should have received a copy of the GNU General Public License 
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
/* CecHelperHttp.php */
$rDir = '';
require_once($rDir.'cec/php/helpers/CecHelperRfc.php');

class CecHelperHttp {

  const HTTP_HEADER_ATTR_ACCEPT = 'Accept';
  const HTTP_HEADER_ATTR_ACCEPT_CHARSET = 'Accept-Charset';
  const HTTP_HEADER_ATTR_ACCEPT_ENCODING = 'Accept-Encoding';
  const HTTP_HEADER_ATTR_ACCEPT_LANGUAGE = 'Accept-Language';
  const HTTP_HEADER_ATTR_CACHE_CONTROL = 'Cache-Control';
  const HTTP_HEADER_ATTR_CHARSET = "Charset";
  const HTTP_HEADER_ATTR_CONNECTION = "Connection";
  const HTTP_HEADER_ATTR_CONTENT_DESCRIPTION = 'Content-Description';
  const HTTP_HEADER_ATTR_CONTENT_DISPOSITION = 'Content-Disposition';
  const HTTP_HEADER_ATTR_CONTENT_LENGTH = "Content-Length";
  const HTTP_HEADER_ATTR_CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding';
  const HTTP_HEADER_ATTR_CONTENT_TYPE = "Content-Type";
  const HTTP_HEADER_ATTR_DEPTH = "Depth";
  const HTTP_HEADER_ATTR_EXPIRES = 'Expires';
  const HTTP_HEADER_ATTR_KEEP_ALIVE = 'Keep-Alive';
  const HTTP_HEADER_ATTR_LOCATION = "Location";
  const HTTP_HEADER_ATTR_SERVER = "Server";
  const HTTP_HEADER_ATTR_SET_COOKIE = "Set-Cookie";
  const HTTP_HEADER_ATTR_TRANSLATE = "Translate";

  const HTTP_HEADER_VALUE_ACCEPT_DEFAULT = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
  const HTTP_HEADER_VALUE_ACCEPT_LANGUAGE_EN_US = 'en-us,en;q=0.5';
  const HTTP_HEADER_VALUE_ACCEPT_ENCODING_DEFLATE = 'deflate';
  const HTTP_HEADER_VALUE_FORM_URLENCODED = "application/x-www-form-urlencoded";
  const HTTP_HEADER_VALUE_TRANSLATE_F = "f";
  const HTTP_HEADER_VALUE_TEXT_XML = "text/xml";
  const HTTP_HEADER_VALUE_UTF8 = "utf-8";

  const HTTP_AGENT_MOZILLA_5 = 'User-Agent: Mozilla/5.0 (X11; Linux i686; rv:11.0) Gecko/20100101 Firefox/11.0';

  const HTTP_STATUS_CONTINUE = 100;
  const HTTP_STATUS_SWITCHING_PROTOCOLS = 101;
  const HTTP_STATUS_OK = 200;
  const HTTP_STATUS_CREATED = 201;
  const HTTP_STATUS_ACCEPTED = 202;
  const HTTP_STATUS_NON_AUTHORITATIVE_INFO = 203;
  const HTTP_STATUS_NO_CONTENT = 204;
  const HTTP_STATUS_RESET_CONTENT = 205;
  const HTTP_STATUS_PARTIAL_CONTENT = 206;
  const HTTP_STATUS_OK_MULTI_STATUS = 207;
  const HTTP_STATUS_MULTIPLE_CHOICES = 300;
  const HTTP_STATUS_MOVED_PERMANENTLY = 301;
  const HTTP_STATUS_MOVED = 302;
  const HTTP_STATUS_MOVED_USE_GET = 303;
  const HTTP_STATUS_NOT_MODIFIED = 304;
  const HTTP_STATUS_USE_PROXY = 305;
  const HTTP_STATUS_TEMPORARY_REDIRECT = 307;
  const HTTP_STATUS_POST_ALLOWED = 340;
  const HTTP_STATUS_BAD_REQUEST = 400;
  const HTTP_STATUS_UNAUTHORIZED = 401;
  const HTTP_STATUS_FORBIDDEN = 403;
  const HTTP_STATUS_NOT_FOUND = 404;
  const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
  const HTTP_STATUS_NOT_ACCEPTABLE = 406;
  const HTTP_STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
  const HTTP_STATUS_REQUEST_TIME_OUT = 408;
  const HTTP_STATUS_CONFLICT = 409;
  const HTTP_STATUS_GONE = 410;
  const HTTP_STATUS_LENGTH_REQUIRED = 411;
  const HTTP_STATUS_PRECONDITION_FAILED = 412;
  const HTTP_STATUS_REQUEST_ENTITY_TOO_LARGE = 413;
  const HTTP_STATUS_REQUEST_URI_TOO_LONG = 414;
  const HTTP_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
  const HTTP_STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
  const HTTP_STATUS_EXPECTATION_FAILED = 417;
  const HTTP_STATUS_POST_DISALLOWED = 440;
  const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
  const HTTP_STATUS_NOT_IMPLEMENTED = 501;
  const HTTP_STATUS_BAD_GATEWAY = 502;
  const HTTP_STATUS_SERVICE_UNAVAILABLE = 503;
  const HTTP_STATUS_GATEWAY_TIME_OUT = 504;
  const HTTP_STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
  const HTTP_STATUS_STRING_200 = 'HTTP/1.1 200 OK';
  const HTTP_STATUS_STRING_404 = 'HTTP/1.1 404 Resource Not Found';

} // CecHelperHttp
?>
