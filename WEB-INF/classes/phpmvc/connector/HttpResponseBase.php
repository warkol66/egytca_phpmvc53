<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/connector/HttpResponseBase.php,v 1.3 2006/02/22 08:15:34 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:15:34 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2002-2006 John C.Wildenauer.  All rights reserved.
*
* This file is part of the php.MVC Web applications framework
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.

* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.

* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/**
* Convenience base implementation of the <b>HttpResponse</b> interface, which
* can be used for the <code>Response</code> implementation required by most
* <code>Connectors</code> that deal with HTTP.  Only the connector-specific
* methods need to be implemented.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br> 
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)<br>
*  Remy Maucherat (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class HttpResponseBase extends ResponseBase {

	// ----- Protected Instance Variables ------------------------------------//

	/**
	* The set of Cookies associated with this Response.
	* @private
	* @type array
	*/
	var $cookies = array();


	/**
	* The date format we will use for creating date headers.
	* @private
	* @type array
	*/
	var $format = array("EEE, dd MMM yyyy HH:mm:ss zzz", 'AU');  // !!!


	/**
	* The HTTP headers explicitly added via addHeader(), but not including
	* those to be added with setContentLength(), setContentType(), and so on.
	* This collection is keyed by the header name, and the elements are
	* ArrayLists containing the associated values that have been set.
	*
	* @private
	* @type array
	*/
	var $headers = array();


	/**
	* Descriptive information about this HttpResponse implementation.
	* @private
	* @type string
	*/
	var $info = 'phpmvc.connector.HttpResponseBase/1.0';


	// ----- Constructors --------------------------------------------------- //

	function HttpResponseBase() {

		// ...

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return an array of all cookies set for this response, or
	* a zero-length array if no cookies have been set.
	* @public
	* @returns array
	*/
	function getCookies() {

		return $this->cookies;

    }


	/**
	* Return the value for the specified header, or <code>null</code> if this
	* header has not been set.  If more than one value was added for this
	* name, only the first is returned; use getHeaderValues() to retrieve all
	* of them.
	*
	* @param string	The header name to look up
	* @public
	* @returns string
	*/
	function getHeader($name) {

		$values = NULL;
		$values = $this->headers[$name];

		if (values != NULL)
			return $values;
		else
			return NULL;

    }


	/**
	* Release all object references, and initialize instance variables, in
	* preparation for reuse of this object.
	*
	* @public
	* @returns void
	*/
	function recycle() {

		parent::recycle();
		$this->cookies = array();
		$this->headers = array(); 

	}


	/** JCW Duplicate method - see reset(..) below
	* Reset this response, and specify the values for the HTTP status code
	* and corresponding message.
	*
	* @param int  
	* @param string
	* @public
	* @returns void
	*/
	#function reset($status, $message) {
	#
	#	$this->reset();
	#
	#}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Returns a default status message for the specified HTTP status code.
	*
	* @param int	The status code for which a message is desired
	* @private
	* @returns string
	*/
	function getStatusMessage($status) {
		// UNTESTED
        switch ($status) {
        case SC_OK:
            return ("OK");
        case SC_ACCEPTED:
            return ("Accepted");
        case SC_BAD_GATEWAY:
            return ("Bad Gateway");
        case SC_BAD_REQUEST:
            return ("Bad Request");
        case SC_CONFLICT:
            return ("Conflict");
        case SC_CONTINUE:
            return ("Continue");
        case SC_CREATED:
            return ("Created");
        case SC_EXPECTATION_FAILED:
            return ("Expectation Failed");
        case SC_FORBIDDEN:
            return ("Forbidden");
        case SC_GATEWAY_TIMEOUT:
            return ("Gateway Timeout");
        case SC_GONE:
            return ("Gone");
        case SC_HTTP_VERSION_NOT_SUPPORTED:
            return ("HTTP Version Not Supported");
        case SC_INTERNAL_SERVER_ERROR:
            return ("Internal Server Error");
        case SC_LENGTH_REQUIRED:
            return ("Length Required");
        case SC_METHOD_NOT_ALLOWED:
            return ("Method Not Allowed");
        case SC_MOVED_PERMANENTLY:
            return ("Moved Permanently");
        case SC_MOVED_TEMPORARILY:
            return ("Moved Temporarily");
        case SC_MULTIPLE_CHOICES:
            return ("Multiple Choices");
        case SC_NO_CONTENT:
            return ("No Content");
        case SC_NON_AUTHORITATIVE_INFORMATION:
            return ("Non-Authoritative Information");
        case SC_NOT_ACCEPTABLE:
            return ("Not Acceptable");
        case SC_NOT_FOUND:
            return ("Not Found");
        case SC_NOT_IMPLEMENTED:
            return ("Not Implemented");
        case SC_NOT_MODIFIED:
            return ("Not Modified");
        case SC_PARTIAL_CONTENT:
            return ("Partial Content");
        case SC_PAYMENT_REQUIRED:
            return ("Payment Required");
        case SC_PRECONDITION_FAILED:
            return ("Precondition Failed");
        case SC_PROXY_AUTHENTICATION_REQUIRED:
            return ("Proxy Authentication Required");
        case SC_REQUEST_ENTITY_TOO_LARGE:
            return ("Request Entity Too Large");
        case SC_REQUEST_TIMEOUT:
            return ("Request Timeout");
        case SC_REQUEST_URI_TOO_LONG:
            return ("Request URI Too Long");
        case SC_REQUESTED_RANGE_NOT_SATISFIABLE:
            return ("Requested Range Not Satisfiable");
        case SC_RESET_CONTENT:
            return ("Reset Content");
        case SC_SEE_OTHER:
            return ("See Other");
        case SC_SERVICE_UNAVAILABLE:
            return ("Service Unavailable");
        case SC_SWITCHING_PROTOCOLS:
            return ("Switching Protocols");
        case SC_UNAUTHORIZED:
            return ("Unauthorized");
        case SC_UNSUPPORTED_MEDIA_TYPE:
            return ("Unsupported Media Type");
        case SC_USE_PROXY:
            return ("Use Proxy");
        case 207:       // WebDAV
            return ("Multi-Status");
        case 422:       // WebDAV
            return ("Unprocessable Entity");
        case 423:       // WebDAV
            return ("Locked");
        case 507:       // WebDAV
            return ("Insufficient Storage");
        default:
            return ("HTTP Response Status " . $status);
        }

    }


	/**
	* Send the HTTP response headers, if this has not already occurred.
	* @private
	* @returns void
	*/
	function sendHeaders() {

		//if($this->isCommitted())
			return;

		// Check if the request was an HTTP/0.9 request
		// ...

		// The response is now committed
		$this->committed = True;

    }


	// ----- ServletResponse Methods ---------------------------------------- //

	/**
	* Flush the buffer and commit this response.  If this is the first output,
	* send the HTTP headers prior to the user data.
	* <p>To-Do
	*
	* @public
	* @returns void
	*/
	function flushBuffer() {

		// ...

	}


	/**
	* Clear all cookies and headers and reset the message and status
	* 
	* @param int		The HTTP status code
	* @param string	The message string
	* @public
	* @returns void
	*/
	function reset($status='', $message='') {

		# !!!!!!!!!!!!!!!!!!
		#if(included)
		#	return;     // Ignore any call from an included servlet

		parent::reset();
		$this->cookies		= array();
		$this->headers		= array();
		#$this->message	= $message;
		#$this->status		= $status; // HttpServletResponse.SC_OK;

	}


	/**
	* Set the content type for this Response.
	*
	* @param string	The new content type
	* @public
	* @returns void
	*/
	function setContentType($type) {

		if($this->isCommitted())
			return;

		parent::setContentType($type);

	}


	/**
	* Set the Locale that is appropriate for this response, including
	* setting the appropriate character encoding.
	*
	* @param Locale	The new locale
	* @public
	* @returns void
	*/
	function setLocale($locale) {

		if($this->isCommitted())
			return;

		#if($this->included)
		#	return;     // Ignore any call from an included servlet

		parent::setLocale($locale);

		// ...
		// To-do
		#setHeader("Content-Language", $value);

	}


	// ----- HttpServletResponse Methods ------------------------------------ //

	/**
	* Add the specified Cookie to those that will be included with
	* this Response.
	*
	* @param Cookie	The cookie to be added
	* @public
	* @returns void
	*/
	function addCookie($cookie) {

		if($this->isCommitted())
			return;

		#if(included)
		#	return;     // Ignore any call from an included servlet

		$this->cookies[] = $cookie;

	}


	/**
	* Set the specified header to the specified value.
	*
	* @param string	The name of the header to set
	* @param string	The value to be set
	* @public
	* @returns void
	*/
	function setHeader($name, $value) {

		// CHECK THIS

		if($this->isCommitted())
			return;

		#if($this->included)
		#	return;     // Ignore any call from an included servlet

		$values = array();
		$values[] = $value;

		$this->headers[$name] = $values; // headers.put(name, values)

		$match = strtolower($name); // String
		if($match == "content-length") {
			$contentLength = -1; // Int

			$contentLength = (int) $value; // Integer.parseInt(value)
			// Catch (NumberFormatException e) !!!!
			;
	
			if($contentLength >= 0)
				$this->setContentLength($contentLength);

		} elseif($match == "content-type") {
			$this->setContentType($value);
		}

	}


	/**
	* Set the specified integer header to the specified value.
	*
	* @param string	The name of the header to set
	* @param int		The integer value to be set
	* @public
	* @returns void
	*/
	function setIntHeader($name, $value) {

		if($this->isCommitted())
			return;

		#if($this->included)
		#	return;     // Ignore any call from an included servlet

		$this->setHeader($name, "" . $value);

	}


}
?>