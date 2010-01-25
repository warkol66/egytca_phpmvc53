<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/connector/RequestBase.php,v 1.5 2006/02/22 08:33:43 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 08:33:43 $
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
* Abstract implementation of a HTTP <b>Request</b> 
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br> 
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.5 $
* @public
*/
class RequestBase {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The attributes associated with this Request, keyed by attribute name.
	* @private
	* @type array
	*/
	var $attributes = array();	// HashMap

	/**
	* The authorization credentials sent with this Request.
	* @private
	* @type string
	*/
	var $authorization = NULL;

	/**
	* The character encoding for this Request.
	* @private
	* @type string
	*/
	var $characterEncoding = NULL;

	/**
	* The content length associated with this request.
	* @private
	* @type int
	*/
	var $contentLength = -1;

	/**
	* The content type associated with this request.
	* @private
	* @type string
	*/
	var $contentType = NULL;

	/**
	* The Context within which this Request is being processed.
	* @private
	* @type Context
	*/
	var $context = NULL;

	/**
	* The default Locale if none are specified.
	* @private
	* @type Locale
	*/
	var $defaultLocale = NULL;	// Locale.getDefault()!!!

	/**
	* Descriptive information about this Request implementation.
	* Eg: "phpmvc.connector.RequestBase/1.0"
	* @private
	* @type string
	*/
	var $info = 'phpmvc.connector.RequestBase/1.0';

	/**
	* The preferred Locales assocaited with this Request.
	* @private
	* @type array
	*/
	var $locales = array();

	/**
	* The protocol name and version associated with this Request.
	* @private
	* @type string
	*/
	var $protocol = NULL;


	/**
	* The remote address associated with this request.
	* @private
	* @type string
	*/
	var $remoteAddr = NULL;

	/**
	* The fully qualified name of the remote host.
	* @private
	* @type string
	*/
	var $remoteHost = NULL;

	/**
	* The response with which this request is associated.
	* @private
	* @type Response
	*/
	var $response = NULL;

	/**
	* The scheme associated with this Request. !!!!!!!!!!
	* @private
	* @type string
	*/
	var $scheme = NULL;

	/**
	* Was this request received on a secure connection?
	* @private
	* @type boolean
	*/
	var $secure = False;

	/**
	* The server name associated with this Request.
	* @private
	* @type  string
	*/
	var $serverName = NULL;

	/**
	* The server port associated with this Request.
	* @private
	* @type ini
	*/
	var $serverPort = -1;

	/**
	* The socket through which this Request was received.
	* @private
	* @type string
	*/
	var $socket = NULL;

	/**
	* The Wrapper within which this Request is being processed.
	* @private
	* @type Wrapper
	*/
	var $wrapper = NULL;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the authorization credentials sent with this request.
	* 
	* @public
	* @returns string
	*/
	function getAuthorization() {

		return $this->authorization;

	}


	/**
	* Set the authorization credentials sent with this request.
	*
	* @param authorization String, The new authorization credentials
	* @public
	* @returns void
	*/
	function setAuthorization($authorization) {

		$this->authorization = $authorization;

	}


	/**
	* Return the Context within which this Request is being processed.
	* @public
	* @returns Context
	*/
	function getContext() {

		return $this->context;

	}


	/**
	* Set the Context within which this Request is being processed.  This
	* must be called as soon as the appropriate Context is identified, because
	* it identifies the value to be returned by <code>getContextPath()</code>,
	* and thus enables parsing of the request URI.
	*
	* @param Context	The newly associated Context
	* @public
	* @returns void
	*/
	function setContext($context) {

		$this->context = $context;

	}


	/**
	* Return descriptive information about this Request implementation and
	* the corresponding version number, in the format
	* <code>&lt;description&gt;/&lt;version&gt;</code>.
	* @public
	* @returns string
	*/
	function getInfo() {

		return $this->info;

	}


	/**
	* Return the <code>Server Request</code> for which this object
	* is the facade.  This method must be implemented by a subclass.
	* @public
	* @returns Request
	*/
	function getRequest() {

		return $this->facade;

	}


	/**
	* Return the Response with which this Request is associated.
	* @public
	* @returns Response
	*/
	function getResponse() {

		return $this->response;

	}


	/**
	* Set the Response with which this Request is associated.
	*
	* @param response Response, The new associated response
	* @public
	* @returns void
	*/
	function setResponse($response) {

		$this->response = $response;

	}


	/** !!!!!!!!!!!!!!!!
	* Return the Wrapper within which this Request is being processed.
	*
	* @public
	* @returns Wrapper
	*/
	function getWrapper() {

		return $this->wrapper;

	}


	/**
	* Set the Wrapper within which this Request is being processed.  This
	* must be called as soon as the appropriate Wrapper is identified, and
	* before the Request is ultimately passed to an application servlet.
	*
	* @param wrapper Wrapper, The newly associated Wrapper
	* @public
	* @returns void
	*/
	function setWrapper($wrapper) {

		$this->wrapper = $wrapper;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add a Locale to the set of preferred Locales for this Request.  The
	* first added Locale will be the first one returned by getLocales().
	*
	* @param Locale	The new preferred Locale
	* @public
	* @returns void
	*/
	function addLocale($locale) {

		$this->locales[] = $locale;	// locales.add(locale)

	}


	/**
	* Perform whatever actions are required to flush and close the input
	* stream or reader, in a single operation.
	*
	* @public
	* @returns void
	*/
	function finishRequest() {

		// If a Reader has been acquired, close it
		if($this->reader != NULL) {
			$this->reader->close();
		}

		// If a ServletInputStream has been acquired, close it
		if($this->stream != NULL) {
			$this->stream->close();
		}

		// The underlying input stream (perhaps from a socket)
		// is not our responsibility

	}


	/**
	* Release all object references, and initialize instance variables, in
	* preparation for reuse of this object.
	*
	* @public
	* @returns void
	*/
	function recycle() {

		$this->attributes			= array();
		$this->authorization		= NULL;
		$this->characterEncoding= NULL;
		// connector is NOT reset when recycling !!!!!!!!!!!!!!!!!!!!
		$this->contentLength		= -1;
		$this->contentType		= NULL;
		$this->context				= NULL;
		$this->input				= NULL;
		$this->locales				= array();
		$this->notes				= array();
		$this->protocol			= NULL;
		$this->reader				= NULL;
		$this->remoteAddr			= NULL;
		$this->remoteHost			= NULL;
		$this->response			= NULL;
		$this->scheme				= NULL;
		$this->secure				= False;
		$this->serverName			= NULL;
		$this->serverPort			= -1;
		$this->socket				= NULL;
		$this->stream				= NULL;
		$this->wrapper				= NULL;

	}


	/**
	* Set the content length associated with this Request.
	*
	* @param int	The new content length
	* @public
	* @returns void
	*/
	function setContentLength($length) {

		$this->contentLength = $length;

	}


	/**
	* Set the content type (and optionally the character encoding)
	* associated with this Request.  For example,
	* <code>text/html; charset=ISO-8859-4</code>.
	*
	* @param string	The new content type
	* @public
	* @returns void
	*/
	function setContentType($type) {

		$this->contentType = $type;
		# !!!!!!!!!!!!!!!!!!!!!!!
		#if(type.indexOf(';') >= 0)
		#	characterEncoding = RequestUtil.parseCharacterEncoding(type);

	}


	/**
	* Set the protocol name and version associated with this Request.
	*
	* @param string	The protocol name and version
	* @public
	* @returns void
	*/
	function setProtocol($protocol) {

		$this->protocol = $protocol;

	}


	/**
	* Set the IP address of the remote client associated with this Request.
	*
	* @param string	The remote IP address
	* @public
	* @returns void
	*/
	function setRemoteAddr($remoteAddr) {

		$this->remoteAddr = $remoteAddr;

	}


	/**
	* Set the fully qualified name of the remote client associated with this
	* Request.
	*
	* @param string	The remote host name
	* @public
	* @returns void
	*/
	function setRemoteHost($remoteHost) {

		$this->remoteHost = $remoteHost;

	}


	/**
	* Set the name of the scheme associated with this request.  Typical values
	* are <code>http</code>, <code>https</code>, and <code>ftp</code>.
	*
	* @param string	The scheme
	* @public
	* @returns void
	*/
	function setScheme($scheme) {

		$this->scheme = $scheme;

	}


	/**
	* Set the value to be returned by <code>isSecure()</code>
	* for this Request.
	*
	* @param boolean	The new isSecure value
	* @public
	* @returns void
	*/
	function setSecure($secure) {

		$this->secure = $secure;

	}


	/**
	* Set the name of the server (virtual host) to process this request.
	*
	* @param string	The server name
	* @public
	* @returns void
	*/
	function setServerName($name) {

		$this->serverName = $name;

	}


	/**
	* Set the port number of the server to process this request.
	*
	* @param int	The server port
	* @public
	* @returns void
	*/
	function setServerPort($port) {

		$this->serverPort = $port;

	}


	// ----- ServletRequest Methods ----------------------------------------- //

	/**
	* Return the specified request attribute if it exists; otherwise, return
	* <code>NULL</code>.
	*
	* @param string	The name of the request attribute to return
	* @public
	* @returns object
	*/
	function getAttribute($name) {
		if( array_key_exists($name, $this->attributes) )
			return $this->attributes[$name];	// attributes.get(name)
		else
			return NULL;
	}


	/**
	* Return the names of all request attributes for this Request, or an
	* empty <code>Enumeration</code> if there are none.
	*	
	* @public
	* @returns array
	*/
	function getAttributeNames() {

		return array_keys($this->attributes);	// new Enumerator(attributes.keySet())

	}


	/**
	* Return the character encoding for this Request.
	*	
	* @public
	* @returns string
	*/
	function getCharacterEncoding() {

		return $this->characterEncoding;

	}


	/**
	* Return the content length for this Request.
	*	
	* @public
	* @returns int
	*/
	function getContentLength() {

		return $this->contentLength;

	}


	/**
	* Return the content type for this Request.
	*	
	* @public
	* @returns string
	*/
	function getContentType() {

		return $this->contentType;

	}


	/**
	* Return the preferred Locale that the client will accept content in,
	* based on the value for the first <code>Accept-Language</code> header
	* that was encountered.  If the request did not specify a preferred
	* language, the server's default Locale is returned.
	*	
	* @public
	* @returns Locale
	*/
	function getLocale() {

		if( count($locales) > 0 )
			return $locales[0]; // locales.get(0)
		else
			return $this->defaultLocale;

	}


	/**
	* Return the set of preferred Locales that the client will accept
	* content in, based on the values for any <code>Accept-Language</code>
	* headers that were encountered.  If the request did not specify a
	* preferred language, the server's default Locale is returned.
	*	
	* @public
	* @returns array
	*/
	function getLocales() {

		if( count($locales) > 0 )
			return $this->locales; 			// new Enumerator(locales)

		$results = array(); 					// new ArrayList()
		$results[] = $this->defaultLocale;	// results.add(defaultLocale)
		return $results;						// new Enumerator(results)

	}


	/**
	* Return the value of the specified request parameter, if any; otherwise,
	* return <code>null</code>.  If there is more than one value defined,
	* return only the first one.
	* <p>Abstract
	*
	* @param name String, Name of the desired request parameter
	*	
	* @public
	* @returns string
	*/
	function getParameter($name) {}


	/**
	* Returns a <code>Map</code> of the parameters of this request.
	* Request parameters are extra information sent with the request.
	* For HTTP servlets, parameters are contained in the query string
	* or posted form data.
	* <p>Abstract
	*	
	* @public
	* @returns array
	*/
	function getParameterMap() {}


	/**
	* Return the names of all defined request parameters for this request.
	* <p>Abstract
	*	
	* @public
	* @returns array
	*/
	function getParameterNames() {}


	/**
	* Return the defined values for the specified request parameter, if any;
	* otherwise, return <code>NULL</code>.
	* <p>Abstract
	*
	* @param string	The name of the desired request parameter
	* @public
	* @returns string
	*/
	function getParameterValues($name) {}


	/**
	* Return the protocol and version used to make this Request.
	*
	* @public
	* @returns string
	*/
	function getProtocol() {

		return $this->protocol;

	}


	/**
	* Return the remote IP address making this Request.
	*	
	* @public
	* @returns string
	*/
	function getRemoteAddr() {

		return $this->remoteAddr;

	}


	/**
	* Return the remote host name making this Request.
	*	
	* @public
	* @returns string
	*/
	function getRemoteHost() {

		return $this->remoteHost;

	}


	/**
	* Return a RequestDispatcher that wraps the resource at the specified
	* path, which may be interpreted as relative to the current request path.
	* <p>Abstract
	*
	* @param string	The path of the resource to be wrapped
	* @public
	* @returns RequestDispatcher
	*/
	function getRequestDispatcher($path) {}


	/**
	* Return the scheme used to make this Request.
	*
	* @public
	* @returns string
	*/
	function getScheme() {

		return $this->scheme;

	}


	/**
	* Return the server name responding to this Request.
	*
	* @public
	* @returns string
	*/
	function getServerName() {

		return $this->serverName;

	}


	/**
	* Return the server port responding to this Request.
	*
	* @public
	* @returns int
	*/
	function getServerPort() {

		return $this->serverPort;

	}


	/**
	* Was this request received on a secure connection?
	*
	* @public
	* @returns boolean
	*/
	function isSecure() {

		return $this->secure;

	}


	/**
	* Remove the specified request attribute if it exists.
	*
	* @param string	The name of the request attribute to remove
	* @public
	* @returns void
	*/
	function removeAttribute($name) {

		// Remove the specified element from the attributes array, including the key.
		HelperUtils::zapArrayElement($name, $this->attributes);

	}


	/**
	* Set the specified request attribute to the specified value.
	*
	* @param string	The name of the request attribute to set
	* @param object	The associated value
	* @public
	* @returns void
	*/
	function setAttribute($name, $value) {

		// Name cannot be null
		if($name == NULL) {
			# $this->sm["requestBase.setAttribute.namenull"]; // StringManager 
			return;
		}

		// Null value is the same as removeAttribute() !!!!!!!!!!
		if($value == NULL) {
			$this->removeAttribute($name);
			return;
		}

		$this->attributes[$name] = $value; // attributes.put(name, value)

	}


	/**
	* Overrides the name of the character encoding used in the body of
	* this request.  This method must be called prior to reading request
	* parameters or reading input using <code>getReader()</code>.
	*
	* @param string	The character encoding to be used
	* @public
	* @returns void
	*/
	function setCharacterEncoding($enc) {

		// Ensure that the specified encoding is valid
		// ...

		// Save the validated encoding
		$this->characterEncoding = $enc;

    }

}
?>