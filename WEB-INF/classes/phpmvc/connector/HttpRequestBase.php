<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/connector/HttpRequestBase.php,v 1.6 2006/05/17 07:41:24 who Exp $
* $Revision: 1.6 $
* $Date: 2006/05/17 07:41:24 $
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
* <b>HttpRequest</b> implementation
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br> 
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.6 $
* @public
*/
class HttpRequestBase extends RequestBase {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The authentication type used for this request.
	* @private
	* @type string
	*/
	var $authType = NULL;


	/**
	* The context path for this request.
	* @private
	* @type string
	*/
	var $contextPath = '';


	/**
	* The set of cookies associated with this Request.
	* @private
	* @type array
	*/
	var $cookies = array();


	/**
	* An empty collection to use for returning empty Enumerations.  Do not
	* add any elements to this collection!
	* @private
	* @type array
	*/
	var $empty = array();


	/**
	* The HTTP headers associated with this Request, keyed by name.  The
	* values are ArrayLists of the corresponding header values.
	* @private
	* @type array
	*/
	var $headers = array();

	/**
	* The users AcceptType settings
	* Eg: Mime type = "text/html, application/pdf", 
	* USE RequestBase->contentType   "text/html; charset=ISO-8859-4"
	*
	* @private
	* @type string
	* @depreciated
	*/
	#var $acceptType = 'text/html';

	/**
	* The users AcceptCharset settings
	* Eg: Mime type = "iso-8859-1,*,utf-8", 
	* USE RequestBase->contentType   "text/html; charset=ISO-8859-4"
	*
	* @private
	* @type string
	* @depreciated
	*/
	#var $acceptType = 'text/html';

	/**
	* The Locales collection, consisting of language => country mappings
	* as specified in the users browser settings, and retrieced from the 
	* users request headers. 
	* This allows the user to control what language is used through their
	* browser settings.
	* Eg: array('en'=>'AU', 'en'=>'US', 'en'=>'GB', 'fr'=>'')
	*
	* @private
	* @type array
	*/
	var $locales = array();

	/**
	* The users language priotities
	* Eg: array('en', 'de', 'fr')
	*
	* @private
	* @type array
	*/
	var $languagePriority = array();


	/**
	* Descriptive information about this HttpRequest implementation.
	* @private
	* @type string
	*/
	var $info = 'HttpRequestBase/1.0';


	/**
	* The request method associated with this Request. [GET/POST]
	* @private
	* @type string
	*/
	var $method = NULL;


	/**
	* The parsed parameters for this request.  This is populated only if
	* parameter information is requested via one of the
	* <code>getParameter()</code> family of method calls.  The key is the
	* parameter name.
	* The value is a String value or array of String values
	* Eg:<br>
	* <code>&lt;input type="text" name="mytext"&gt;<br>
	*		    &nbsp;&nbsp;&nbsp;&lt;select name="multiList[]" multiple&gt;<br><br>
	* 	Array([mytext]=>text_input, [multiList] => Array([0]=>A, [1]=>B))
	* </code>
	* !!!!
	*
	* @private
	* @type array
	*/
	var $parameters = array();


	/**
	* Have the parameters for this request been parsed yet?
	* @private
	* @type boolean
	*/
	var $parsed = False;


	/**
	* The path information for this request.
	* @private
	* @type string
	*/
	var $pathInfo = NULL;


	/**
	* The query string for this request.
	* @private
	* @type string
	*/
	var $queryString = NULL;


	/**
	* Was the requested session ID received in a cookie?
	* @private
	* @type boolean
	*/
	var $requestedSessionCookie = False;


	/**
	* The requested session ID (if any) for this request.
	* @private
	* @type string
	*/
	var $requestedSessionId = NULL;


	/**
	* Was the requested session ID received in a URL?
	* @private
	* @type boolean
	*/
	var $requestedSessionURL = False;


	/**
	* The request URI associated with this request.
	* @private
	* @type string
	*/
	var $requestURI = NULL;


	/**
	* The decoded request URI associated with this request.
	* @private
	* @type string
	*/
	var $decodedRequestURI = NULL;


	/**
	* Was this request received on a secure channel?
	* @private
	* @type boolean
	*/
	var $secure = False;


	/**
	* The appServerPath path for this request.
	* @private
	* @type string
	*/
	var $appServerPath = NULL;


	/**
	* The currently active session for this request.
	* @private
	* @type string
	*/
	var $session = NULL;


	/**
	* The Principal who has been authenticated for this Request.
	* <p>
	* A principal can represent an individual, a corporation, anything which 
	* can have an identity. This property is used by the <code>getRemoteUser()
	* </code> method to retrieve the name of the remote user that has been 
	* authenticated for this Request.
	*
	* @private
	* @type Principal
	*/
	var $oUserPrincipal = Null;


	/** JCW 
	* The request variables.
	* @private
	* @type array
	*/
	var $_get_vars = NULL;

	/** JCW 
	* The request variables.
	* @private
	* @type array
	*/
	var $_post_vars = NULL;

	/** JCW 
	* The request variables.
	* @private
	* @type array
	*/
	var $_files_vars = NULL;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return descriptive information about this Request implementation and
	* the corresponding version number, in the format
	* <code>&lt;description&gt;/&lt;version&gt;</code>.
	*
	* @public
	* @returns string
	*/
	function getInfo() {

		return $this->info;

	}

	/**
	* Return the principal that has been authenticated for this Request.
	* @returns Principal
	*/
	function getUserPrincipal() {
		return $this->oUserPrincipal;
	}

    /**
     * Set the Principal who has been authenticated for this Request.
     *
     * @param Principal The user Principal
     */
	function setUserPrincipal($oPrincipal) {
		$this->oUserPrincipal= $oPrincipal;
    }


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Set a GET request variable array to the Request object.
	*
	* @param _get_vars		The GET request variable array
	* @public
	* @returns void
	*/
	function setGetVars(&$_get_vars) {

		$this->_get_vars = $_get_vars;

	}

	/**
	* Get a GET request variable array from the Request object. (reference)
	*
	* @public
	* @returns _get_vars
	*/
	function &getGetVars() {

		return $this->_get_vars;

	}

	/**
	* Set a POST request variable array to the Request object.
	*
	* @param _post_vars		The POST request variable array
	* @public
	* @returns void
	*/
	function setPostVars(&$_post_vars) {

		$this->_post_vars = $_post_vars;

	}

	/**
	* Get a POST request variable array from the Request object. (reference)
	*
	* @public
	* @returns _post_vars
	*/
	function &getPostVars() {

		return $this->_post_vars;

	}

	/**
	* Set a FILES request variable array to the Request object.
	*
	* @param _files_vars		The POST request variable array
	* @public
	* @returns void
	*/
	function setFilesVars(&$_files_vars) {

		$this->_files_vars = $_files_vars;

	}

	/**
	* Get a FILES request variable array from the Request object. (reference)
	*
	* @public
	* @returns _files_vars
	*/
	function &getFilesVars() {

		return $this->_files_vars;

	}

	/**
	* Add a Cookie to the set of Cookies associated with this Request.
	*
	* @param Cookie	The new cookie
	* @public
	* @returns void
	*/
	function addCookie($cookie) {

		$this->cookies[] = $cookie;	// add() cookie

	}

	/**
	* Add a Header to the set of Headers associated with this Request.
	*
	* @param string	The new header name
	* @param string	The new header value. The values are ArrayLists of the 
	*  corresponding header values.
	* @public
	* @returns void
	*/
	function addHeader($name, $value) {

		$name = strtolower($name);
		$values = $this->headers[$name];	// ArrayList
		if($values == NULL) {
			// No existing header values found for this "name" key
			//  so create a new header "values" array
			$values = array();				// ArrayList	 
		}
		// Add the new header "value" to 
		$values[] = $value;	// values.add(value)
		// Add the "values" to the header for this "name" key
		// (overwrite old "values" array in headers[], if any)
		$headers[$name] = $values;

	}

	/**
	* Add a parameter name and corresponding set of values to this Request.
	* (This is used when restoring the original request on a form based
	* login).
	*
	* @param string	The name of this request parameter
	* @param string	The corresponding values for this request parameter
	* @public
	* @returns void
	*/
	function addParameter($name, $values) {

		$this->parameters[$name] = $values;	// parameters.put(name, values)

	}

	/**
	* Clear the collection of Cookies associated with this Request.
	* @public
	* @returns void
	*/
	function clearCookies() {

		$this->cookies = array();

	}

	/**
	* Clear the collection of Headers associated with this Request.
	* @public
	* @returns void
	*/
	function clearHeaders() {

		$this->headers = array();

	}

	/**
	* Clear the collection of Locales associated with this Request.
	* @public
	* @returns void
	*/
	function clearLocales() {

		$this->locales = array();

	}

	/**
	* Clear the collection of parameters associated with this Request.
	* @public
	* @returns void
	*/
	function clearParameters() {

		if($this->parameters != NULL) {
            #parameters.setLocked(false); // !!!
            $this->parameters = array();
		} else {
            $this->parameters = array();
		}

	}

	/**
	* Release all object references, and initialize instance variables, in
	* preparation for reuse of this object.
	* @public
	* @returns void
	*/
	function recycle() {

		#super.recycle();
		$this->authType = NULL;
		$this->contextPath = '';
		$this->cookies = array();
		$this->headers = array();
		$this->method = NULL;
		if($this->parameters != NULL) {
            #parameters.setLocked(false);
            $this->parameters = array();
		}
		$this->parsed = False;
		$this->pathInfo = NULL;
		$this->queryString = NULL;
		$this->requestedSessionCookie = False;
		$this->requestedSessionId = NULL;
		$this->requestedSessionURL = False;
		$this-> requestURI = NULL;
		$this->decodedRequestURI = NULL;
		$this->secure = False;
		$this->servletPath = NULL;
		$this->session = NULL;
		$this->userPrincipal = NULL;

	}


	/**
	* Set the context path for this Request.  This will normally be called
	* when the associated Context is mapping the Request to a particular
	* Wrapper.
	*
	* @public
	* @param string 	The context path
	* @returns void
	*/
	function setContextPath($contextPath) {

		if($contextPath == '') {
			$this->contextPath = "";
		} else {
			$this->contextPath = $contextPath;
		}

	}


	/**
	* Set the HTTP request method used for this Request.
	*
	* @param string	The request method
	* @public
	* @returns void
	*/
	function setMethod($method) {

		$this->method = $method;

	}

	/**
	* Set the path information for this Request.  This will normally be called
	* when the associated Context is mapping the Request to a particular
	* Wrapper.
	*
	* @param string	The path information
	* @public
	* @returns void
	*/
	function setPathInfo($path) {

		$this->pathInfo = $path;

	}

	/**
	* Set the unparsed request URI for this Request.  This will normally
	* be called by the HTTP Connector, when it parses the request headers.
	*
	* @param string	The request URI
	* @public	
	* @returns void
	*/
	function setRequestURI($uri) {

		$this->requestURI = $uri;

	}

	/**
	* Set the appServerPath path for this Request.  This will normally be called
	* when the associated Context is mapping the Request to a particular
	* Wrapper.
	*
	* @param string	The appServerPath path
	* @public
	* @returns void
	*/
	function setAppServerPath($path) {	// setServletPath(...)

		$this->appServerPath = $path;

	}


	/**
	* Return the name of the remote user that has been authenticated
	* for this Request.
	* @returns string
	*/
	function getRemoteUser() {

		if($this->oUserPrincipal != Null) {
			return $this->oUserPrincipal->getName();
		} else {
			return '';
		}

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Parse the parameters of this request, if it has not already occurred.
	* If parameters are present in both the query string and the request
	* content, they are merged.
	*
	* @private
	* @returns void
	*/
	function parseParameters() {

		// Assume register_globals = Off (php.ini)
		// Assume GET/POST variables are tainted

		if($this->parsed == True) {
			return;
		}

		// JCW 27.10.2004
		// Note: Depreciated using $_REQUEST. Issue with using "auth" as a request parameter
		// Eg: $_REQUEST[auth] => 0df223a73f42ccbe87cfdc357b5a71ea

		$results = array();	// array()

		// Detect PHP 4/5 
		if((int)phpversion() > 4) {
			$results = array_merge($_GET, $_POST);
		} else {
			// Php v4 support
			global $HTTP_SERVER_VARS;
			global $HTTP_POST_VARS;
			global $HTTP_GET_VARS;
			if( isset($HTTP_SERVER_VARS) ) {
				$results = array_merge($HTTP_GET_VARS, $HTTP_POST_VARS);
			}
		}

		// Merge the request context and any Get/Post variables
		if( count($this->parameters) > 0 ) {
			$results = array_merge($this->parameters, $results);
		}

		// Store the final results
		$this->parsed = True;
		$this->parameters = $results;

	}


	// ----- Request Methods ------------------------------------------------ //

	/**
	* Return the value of the specified request (GET/POST) parameter, if any;
	* otherwise, return <code>NULL</code>. If there is more than one value defined,
	* return only the first one.
	*
	* @param string	Name of the desired request parameter
	* @public
	* @returns string
	*/
	function getParameter($name) {

		if($this->parsed == False) {
			$this->parseParameters();
		}

		$values = NULL; //array()
		if(array_key_exists($name, $this->parameters)) {
			$values = $this->parameters[$name];
		}

		if($values != NULL) {
			if(is_array($values)) {
				return $values[0];	// return only the first one !!!
			}else {
				return $values;
			}
		} else {
			return NULL;
		}	

	}


	/**
	* Return the names of all defined request parameters for this request.
	* @public
	* @returns array
	*/
	function getParameterNames() {

		if($this->parsed == False) {
			$this->parseParameters();
		}

		return array_keys($this->parameters);	// parameters.keySet()

	}


	/**
	* Return the defined values for the specified request parameter, if any;
	* otherwise, return <code>NULL</code>.
	*
	* @param string	Name of the desired request parameter
	* @public
	* @return string
	*/
	function getParameterValues($name) {

		if($this->parsed == False) {
			$this->parseParameters();
		}

		$values = NULL; //array()
		if(array_key_exists($name, $this->parameters)) {
			$values = $this->parameters[$name];
		}

		if($values != NULL) {
			if(is_array($values)) {
				return $values;	// Eg: <select name="multiList[]" multiple>
			}else {
				#return $values;				// string [DEPRECIATED]
				return array($values);		// array [New behaviour ]
			}
		} else {
			return NULL;
		}

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


	// ----- HttpServletRequest Methods ------------------------------------- //

	/**
	* Return the authentication type used for this Request.
	*
	* @public
	* @returns string
	*/
	function getAuthType() {

		return $this->authType;

	}


	/**
	* Return the portion of the request URI used to select the Context
	* of the Request.
	*
	* @public
	* @returns string
	*/
	function getContextPath() {

		return $this->contextPath;

	}


	/**
	* Return the set of Cookies received with this Request.
	*
	* @public
	* @returns Cookie[]
	*/
	function getCookies() {

		// ....

	}


	/**
	* Return the first value of the specified header, if any; otherwise,
	* return <code>null</code>
	*
	* @param string	Name of the requested header
	* @public
	* @returns string
	*/
	function getHeader($name) {

		//....

    }


	/**
	* Return all of the values of the specified header, if any. Otherwise
	* return an empty enumeration.
	* <p>To-Do
	*
	* @param string	Name of the requested header
	* @public
	* @returns array
	*/
	function getHeaders($name) {

		// ...

    }


	/**
	* Return the HTTP request method used in this Request.
	*
	* @public
	* @returns string
	*/
	function getMethod() {

		return $this->method;

	}


	/**
	* Return the path information associated with this Request.
	*
	* @public
	* @returns string
	*/
	function getPathInfo() {

		return $this->pathInfo;

	}


	/**
	* Return the query string associated with this request.
	*
	* @public
	* @returns string
	*/
	function getQueryString() {

		return $this->queryString;

	}


	/**
	* Return the session identifier included in this request, if any.
	*
	* @public
	* @returns string
	*/
	function getRequestedSessionId() {

		return $this->requestedSessionId;

	}


	/**
	* Return the request URI for this request.
	*
	* @public
	* @returns string
	*/
	function getRequestURI() {

		return $this->requestURI;

	}


	/**
	* Return the portion of the request URI used to select the appServerPath
	* that will process this request.
	*
	* @public
	* @returns string
	*/
	function getAppServerPath() {

		return $this->appServerPath;

	}


	/**
	* Return the session (HttpSession) associated with this Request, creating
	* one if necessary.
	* <p>To-Do
	*
	* @public
	* @returns Session
	*/
	function getSession() {

		#### TO DO ####
		#return $this->getSession(True);	// see below
		return;
		
	}


	/**
	* Return <code>True</code> if the authenticated user principal
	* possesses the specified role name.
	*
	* @param string	Role name to be validated
	* @public
	* @returns boolean
	*/
	function isUserInRole($sRole, $oRealm=Null) {

		// Have we got an authenticated principal at all?
		if($this->oUserPrincipal == Null) {
			return False;
		}

		// Identify the Realm we will use for checking role assignmenets
		if($oRealm == Null) {
			return False;
		}

	  // Check for a role defined directly as an <action ... roles='...,...'>
	  return $oRealm->hasRole($this->oUserPrincipal, $sRole);

	}

}
?>