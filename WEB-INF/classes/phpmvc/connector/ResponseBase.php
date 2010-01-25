<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/connector/ResponseBase.php,v 1.3 2006/02/22 08:45:12 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:45:12 $
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
* Abstract base implementation of the <b>Response</b> interface, which can
* be used for the Response implementation required by most Connectors.  Only
* the connector-specific methods need to be implemented.
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br> 
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)<br>
*  Remy Maucherat (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class ResponseBase {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this response been committed by the application yet?
	* @private
	* @type boolean
	*/
	var $appCommitted = False;

	/**
	* The buffer through which all of our output bytes are passed.<br>
	* (new byte[1024])
	* @private
	* @type string 
	*/
	var $buffer = ''; // new byte[1024]

	/**
	* The number of data bytes currently in the buffer.
	* @private
	* @type int
	*/
	var $bufferCount = 0;

	/** JCW 
	* The response data. This is the file body returned to the client
	* @private
	* @type string
	*/
	var $responseBuffer = NULL;

	/**
	* Has this response been committed yet?
	*
	* <p>See: ResponseBase->flushBuffer()
	*      HttpResponseBase->sendHeaders()
	* @private
	* @type boolean
	*/
	var $committed = False;

	/**
	* The actual number of bytes written to this Response.
	* @private
	* @type int
	*/
	var $contentCount = 0;

	/**
	* The content length associated with this Response.
	* @private
	* @type int
	*/
	var $contentLength = -1;

	/**
	* The content type associated with this Response.
	* @private
	* @type string
	*/
	var $contentType = NULL;

	/**
	* The Context within which this Response is being produced.
	* @private
	* @type Context
	*/
	var $context = NULL;

	/**
	* The character encoding associated with this Response.
	* @private
	* @type string
	*/
	var $encoding = NULL;

	/**
	* Are we currently processing inside a RequestDispatcher.include()?
	* @private
	* @type boolean
	*/
	var $included = False;

	/**
	* Descriptive information about this Response implementation.
	* @private
	* @type string
	*/
	var $info = 'phpmvc.connector.ResponseBase/1.0';

	/**
	* The Locale associated with this Response.
	* @private
	* @type Locale
	*/
	var $locale = NULL; // Locale.getDefault()

	/**
	* The output stream associated with this Response.
	* @private
	* @type OutputStream
	*/
	var $output = NULL;

	/**
	* The Request with which this Response is associated.
	* @private
	* @type Request
	*/
	var $request = NULL;

	/**
	* Has this response output been suspended?
	* @private
	* @type boolean
	*/
	var $suspended = False;

	/**
	* Error flag. True if the response is an error report.
	* @private
	* @type boolean
	*/
	var $error = False;


	// ----- Properties ----------------------------------------------------- //

	/** JCW 
	* The response data.
	*
	* @param string	The content (body) returned to the client
	* @public
	* @returns void
	*/
	function setResponseBuffer(&$responseBuffer) {

		$this->responseBuffer = $responseBuffer;

	}


	/** JCW 
	* The response data.
	*
	* @public
	* @returns string
	*/
	function getResponseBuffer() {

		return $this->responseBuffer;

	}


	/**
	* Application commit flag accessor.
	*
	* @public
	* @returns boolean
	*/
	function isAppCommitted() {

		return ($this->appCommitted || $this->committed);

	}


	/**
	* Return the "processing inside an include" flag.
	*
	* @public
	* @returns boolean
	*/
	function getIncluded() {

		return $this->included;

	}


	/**
	* Set the "processing inside an include" flag.
	*
	* @param boolean	<code>True</code> if we are currently inside a
	*  RequestDispatcher.include(), else <code>False</code>
	* @public
	* @returns void
	*/
	function setIncluded($included) {

		$this->included = $included;

	}


	/**
	* Return descriptive information about this Response implementation and
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
	* Return the Request with which this Response is associated.
	*
	* @public
	* @returns Request
	*/
	function getRequest() {

		return $this->request;

	}


	/**
	* Set the Request with which this Response is associated.
	*
	* @param Request	The new associated request
	* @public
	* @returns void
	*/
	function setRequest($request) {

		$this->request = $request;

	}


	/**
	* Return the output stream associated with this Response.
	*
	* @public
	* @returns OutputStream
	*/
	function getStream() {

		return $this->output;

	}


	/**
	* Set the output stream associated with this Response.
	*
	* @param OutputStream	The new output stream
	* @public
	* @returns void
	*/
	function setStream($stream) {

		$this->output = $stream;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return the content length that was set or calculated for this Response.
	*
	* @public
	* @returns string
	*/
	function getContentLength() {

		return $this->contentLength;

	}


	/**
	* Return the content type that was set or calculated for this response,
	* or <code>null</code> if no content type was set.
	*
	* @public
	* @returns string
	*/
	function getContentType() {

		return $this->contentType;

	}


	/**
	* Release all object references, and initialize instance variables, in
	* preparation for reuse of this object.
	*
	* @public
	* @returns void
	*/
	function recycle() {

		// buffer is NOT reset when recycling
		$this->bufferCount	= 0;
		$this->committed		= False;
		$this->appCommitted	= False;
		$this->suspended		= False;
		// connector is NOT reset when recycling
		$this->contentCount	= 0;
		$this->contentLength	= -1;
		$this->contentType	= NULL;
		$this->context			= NULL;
		$this->encoding		= NULL;
		$this->included		= False;
		$this->locale			= Locale.getDefault();
		$this->output			= NULL;
		$this->request			= NULL;
		$this->stream			= NULL;
		$this->writer			= NULL;
		$this->error			= False;

	}


	// ----- ServerResponse Methods ----------------------------------------- //

	/**
	* Flush the buffer and commit this response.
	*
	* @public
	* @returns void
	*/
	function flushBuffer() {

		$this->committed = True;
		if(bufferCount > 0) {
			#try {
			# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			#	output.write(buffer, 0, bufferCount);
			#} finally {
				$this->bufferCount = 0;
			#}
		}

	}


	/**
	* Return the actual buffer size used for this Response.
	*
	* @public
	* @returns int
	*/
	function getBufferSize() {

		return strlen($this->buffer);

	}


	/**
	* Return the character encoding used for this Response.
	*
	* @public
	* @returns string
	*/
	function getCharacterEncoding() {

        if (encoding == null)
            return ("ISO-8859-1");
        else
            return (encoding);

    }


	/**
	* Return the Locale assigned to this response.
	*
	* @public
	* @returns Locale
	*/
	function getLocale() {

		return $this->locale;

	}


	/**
	* Has the output of this response already been committed?
	*
	* @public
	* @returns boolean
	*/
	function isCommitted() {

		return $this->committed;

	}


	/**
	* Clear any content written to the buffer.
	*
	* @public
	* @returns void
	*/
	function reset() {

		if($this->committed)
			return '"responseBase.reset.ise"'; // (sm.getString("responseBase.reset.ise")
 
		if($this->included)
			return;     // Ignore any call from an included servlet

		#if($this->stream != NULL)
		#	((ResponseStream) stream).reset();

		$this->bufferCount = 0;
		$this->contentLength = -1;
		$this->contentType =NULL;

    }


	/**
	* Reset the data buffer but not any status or header information. Returns 
	* void, or non-null on error
	*
	* @public
	* @returns void
	*/
	function resetBuffer() {

		if($this->committed) {
			#throw new IllegalStateException (sm.getString("responseBase.resetBuffer.ise"));
			return 'responseBase.resetBuffer.ise';
		}

		$this->bufferCount = 0;
		return NULL;

	}


	/**
	* Set the content type for this Response.
	*
	* @param string	The new content type
	* @public
	* @returns void
	*/
	function setContentType($type) {

		// ...
		return;

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

		// ...

	}

}
?>