<?PHP
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
/* CecHttpUtil.php */
$rDir = '';

class CecHttpUtil {

  const HTTP_REQUEST_METHOD_GET = 'GET';
  const HTTP_REQUEST_METHOD_HEAD = 'HEAD';
  const HTTP_REQUEST_METHOD_POST = 'POST';
  const HTTP_REQUEST_METHOD_PUT = 'PUT';
  const HTTP_REQUEST_METHOD_DELETE = 'DELETE';
  const HTTP_REQUEST_METHOD_OPTIONS = 'OPTIONS';
  const HTTP_REQUEST_METHOD_TRACE = 'TRACE';
  const HTTP_REQUEST_METHOD_COPY = 'COPY';
  const HTTP_REQUEST_METHOD_MOVE = 'MOVE';
  const HTTP_REQUEST_METHOD_MKCOL = 'MKCOL';
  const HTTP_REQUEST_METHOD_PROPFIND = 'PROPFIND';
  const HTTP_REQUEST_METHOD_PROPPATCH = 'PROPPATCH';
  const HTTP_REQUEST_METHOD_LOCK = 'LOCK';
  const HTTP_REQUEST_METHOD_UNLOCK = 'UNLOCK';
  const HTTP_REQUEST_METHOD_SEARCH = 'SEARCH';
  const HTTP_REQUEST_METHOD_BPROPFIND = 'BPROPFIND';

  const HTTP_HEADER_ATTR_ACCEPT = 'Accept';
  const HTTP_HEADER_ATTR_ACCEPT_CHARSET = 'Accept-Charset';
  const HTTP_HEADER_ATTR_ACCEPT_ENCODING = 'Accept-Encoding';
  const HTTP_HEADER_ATTR_ACCEPT_LANGUAGE = 'Accept-Language';
  const HTTP_HEADER_ATTR_CACHE_CONTROL = 'Cache-Control';
  const HTTP_HEADER_ATTR_CHARSET = "Charset";
  const HTTP_HEADER_ATTR_COMMERCE_SERVER_SOFTWARE = "COMMERCE-SERVER-SOFTWARE";
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
  const HTTP_HEADER_ATTR_MS_OFFICE_WEB_SERVER = "MicrosoftOfficeWebServer";
  const HTTP_HEADER_ATTR_MS_SHAREPOINT_TEAM_SERVICES = "MicrosoftSharePointTeamServices";
  const HTTP_HEADER_ATTR_PRAGMA = 'Pragma';
  const HTTP_HEADER_ATTR_RANGE = "Range";
  const HTTP_HEADER_ATTR_SERVER = "Server";
  const HTTP_HEADER_ATTR_SET_COOKIE = "Set-Cookie";
  const HTTP_HEADER_ATTR_TRANSLATE = "Translate";
  const HTTP_HEADER_ATTR_WWW_AUTHENTICATE = 'WWW-Authenticate';
  const HTTP_HEADER_ATTR_SOAP_ACTION = 'SOAPAction';

  const HTTP_HEADER_VALUE_ACCEPT_DEFAULT = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
  const HTTP_HEADER_VALUE_ACCEPT_LANGUAGE_EN_US = 'en-us,en;q=0.5';
  const HTTP_HEADER_VALUE_ACCEPT_ENCODING_DEFLATE = 'deflate';
  const HTTP_HEADER_VALUE_ACCEPT_CHARSET_DEFLATE = 'ISO-8859-1,utf-8;q=0.7,*;q=0.7';
  const HTTP_HEADER_VALUE_APACHE = "Apache";
  const HTTP_HEADER_VALUE_APPLICATION_UNKNOWN = 'application/unknown';
  const HTTP_HEADER_VALUE_APPLICATION_OCTET_STREAM = 'application/octet-stream';
  const HTTP_HEADER_VALUE_APPLICATION_FORCE_DOWNLOAD = 'application/force-download';
  const HTTP_HEADER_VALUE_ASP_NET_SESSION_ID = "ASP.NET_SessionId";
  const HTTP_HEADER_VALUE_ASP_SESSION_ID = "ASPSESSIONID";
  const HTTP_HEADER_VALUE_ATTACHMENT_FILE_NAME_SPRINTF = "Attachment; Filename=%s\r\n";
  const HTTP_HEADER_VALUE_BINARY = 'binary';
  const HTTP_HEADER_VALUE_DS_LAUNCH_URL = "DSLaunchURL";
  const HTTP_HEADER_VALUE_EXPIRES_IMMEDIATELY = '0';
  const HTTP_HEADER_VALUE_FILE_TRANSFER = 'File Transfer';
  const HTTP_HEADER_VALUE_FORM_URLENCODED = "application/x-www-form-urlencoded";
  const HTTP_HEADER_VALUE_G1_WEB_COOKIE = "g1web-cookie";
  const HTTP_HEADER_VALUE_KEEP_ALIVE = "Keep-Alive"; 
  const HTTP_HEADER_VALUE_MICROSOFT = "Microsoft";
  const HTTP_HEADER_VALUE_MUST_REVALIDATE = 'must-revalidate';
  const HTTP_HEADER_VALUE_NO_CACHE = 'no-cache';
  const HTTP_HEADER_VALUE_NO_STORE = 'no-store';
  const HTTP_HEADER_VALUE_POST_CHECK_0 = 'post-check=0';
  const HTTP_HEADER_VALUE_PRE_CHECK_0 = 'pre-check=0';
  const HTTP_HEADER_VALUE_PUBLIC = 'public';
  const HTTP_HEADER_VALUE_TEXT_CSV = "text/csv";
  const HTTP_HEADER_VALUE_TEXT_XML = "text/xml";
  const HTTP_HEADER_VALUE_TEXT_PLAIN = "text/plain";
  const HTTP_HEADER_VALUE_TRANSLATE_F = "f";
  const HTTP_HEADER_VALUE_TRANSLATE_T = "t";
  const HTTP_HEADER_VALUE_UTF8 = "utf-8";

  const HTTP_AUTH_BASIC = 'Basic';
  const HTTP_AUTH_NEGOTIATE = 'Negotiate';
  const HTTP_AUTH_NTML = 'NTLM';

  /* See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html for details */
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

  const HTTP_HEADER_AGENT_IE = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; InfoPath.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; MS-RTC LM 8)";
  const HTTP_HEADER_AGENT_FIREFOX = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6";

  const MIME_FILE_ATTR_ERROR = 'error';
  const MIME_FILE_ATTR_NAME = 'name';
  const MIME_FILE_ATTR_SIZE = 'size';
  const MIME_FILE_ATTR_TMP_NAME = 'tmp_name';
  const MIME_FILE_ATTR_TYPE = 'type';

  const MIME_TEXT_PLAIN = 'text/plain';
  const MIME_TYPE_BZIP = 'application/x-bzip';
  const MIME_TYPE_GZIP = 'application/x-gzip';
  const MIME_TYPE_ZIP = 'application/zip';

  static public function formatHeaderAttributes($attributeValueArray) {
    $header2 = Array();
    foreach($attributeValueArray as $attr => $value) {
      if (is_integer($attr)) {
        $header2[] = $value;
      } else {
        if (is_array($value)) {
          $value = implode(', ',$value);
        } // if
        $header2[] = $attr.": ".$value;
      } // else
    } // foreach
    return($header2);
  } // formatHeaderAttributes

  static public function formatFileUploadError($file) {
    if ($file[CecHttpUtil::MIME_FILE_ATTR_ERROR] == UPLOAD_ERR_OK) return(null);

    $errorMsg = null;
    switch($file[CecHttpUtil::MIME_FILE_ATTR_ERROR]) {
    case UPLOAD_ERR_INI_SIZE:
      $msg = 'The uploaded file %s with size %d exceeds the upload_max_filesize directive in php.ini.';
      $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME],
        $file[CecHttpUtil::MIME_FILE_ATTR_SIZE]);
      break;
    case UPLOAD_ERR_FORM_SIZE:
      $msg = 'The uploaded file %s with size %d exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
      $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME],
        $file[CecHttpUtil::MIME_FILE_ATTR_SIZE]);
      break;
    case UPLOAD_ERR_PARTIAL:
      $msg = 'The uploaded file %s with size %d was only partially uploaded.';
      $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME],
        $file[CecHttpUtil::MIME_FILE_ATTR_SIZE]);
      break;
    case UPLOAD_ERR_NO_FILE:
      if (!empty($file[CecHttpUtil::MIME_FILE_ATTR_NAME])) {
        $msg = 'No file %s was uploaded.';
        $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME]);
      } // if
      break;
    case UPLOAD_ERR_NO_TMP_DIR:
      $msg = 'File %s missing a temporary folder.';
      $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME]);
      break;
    case UPLOAD_ERR_CANT_WRITE:
      $msg = 'Failed to write file %s to disk.';
      $errorMsg = sprintf($msg, $file[CecHttpUtil::MIME_FILE_ATTR_NAME]);
      break;
    default:
      $errorMsg = 'Unknown error code: '.$file[CecHttpUtil::MIME_FILE_ATTR_ERROR]
        .' in file '.$file[CecHttpUtil::MIME_FILE_ATTR_NAME];
    } // switch
    return($errorMsg);
  } // formatFileUploadError

} // CecHttpUtil
?>
