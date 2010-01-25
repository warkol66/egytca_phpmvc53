<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ControllerConfig.php,v 1.7 2006/02/22 07:22:10 who Exp $
* $Revision: 1.7 $
* $Date: 2006/02/22 07:22:10 $
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
* <p>A PHP class representing the configuration information of a
* <code>&lt;controller&gt;</code> element in a php.MVC application
* configuration file.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br> 
*  Craig R. McClanahan (original Struts class: see jakarta.apache.org)
* @version $Revision: 1.7 $
*
* @public
*/
class ControllerConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured?
	* @private
	* @type boolean
	*/
	var $configured = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* The input buffer size for file uploads.
	* @private
	* @type int
	*/
	var $bufferSize = 4096;

	/**
	* The content type and character encoding to be set on each response.
	* @private
	* @type string
	*/
	var $contentType = 'text/html';

	/**
	* The debugging detail level that determines logging verbosity.
	* @private
	* @type int
	*/
	var $debug = 0;

	/**
	* Should we store a Locale object in the user's session if needed?
	* @private
	* @type boolean
	*/
	var $locale = False;

	/**
	* The maximum file size to process for file uploads.
	* @private
	* @type string
	*/
	var $maxFileSize = '2M';

	/**
	* The fully qualified class name of the MultipartRequestHandler
	* class to be used.
	* @private
	* @type string
	*/
	var $multipartClass = 'DiskMultipartRequestHandler';

	/**
	* Should we set no-cache HTTP headers on each response?
	* @private
	* @type boolean
	*/
	var $nocache = False;

	/**
	* The fully qualified class name of the RequestProcessor implementation
	* class to be used for this application.
	* @private
	* @type string
	*/
	var $processorClass = 'RequestProcessor';

	/**
	* The temporary working directory to use for file uploads.
	* @private
	* @type string
	*/
	var $tempDir = NULL;


	/**
	* @public
	* @returns int
	*/
	function getBufferSize() {
		return $this->bufferSize;
	}

	/**
	* @param int
	* @public
	* @returns void
	*/ 
	function setBufferSize($bufferSize) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->bufferSize = $bufferSize;
	}


	/**
	* @public
	* @returns string
	*/
	function getContentType() {
		return $this->contentType;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setContentType($contentType) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->contentType = $contentType;
	}


	/**
	* @public
	* @returns int
	*/
	function getDebug() {
		return ($this->debug);
	}

	/**
	* @param int
	* @public
	* @returns void
	*/
	function setDebug($debug) {
		if ($this->configured) {
			return 'Configuration is frozen';
		}
		$this->debug = $debug;
	}


	/**
	* @public
	* @returns boolean
	*/
	function getLocale() {
		return $this->locale;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setLocale($locale) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->locale = $locale;
	}


	/**
	* @public
	* @returns string
	*/
	function getMaxFileSize() {
		return $this->maxFileSize;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setMaxFileSize($maxFileSize) {
		if ($this->configured) {
			return 'Configuration is frozen';
		}
		$this->maxFileSize = $maxFileSize;
	}


	/**
	* @public
	* @returns string
	*/
	function getMultipartClass() {
		return $this->multipartClass;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setMultipartClass($multipartClass) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->multipartClass = $multipartClass;
	}


	/**
	* @public
	* @returns boolean
	*/
	function getNocache() {
		return $this->nocache;
    }

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setNocache($nocache) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->nocache = $nocache;
	}


	/**
	* @public
	* @returns string
	*/
	function getProcessorClass() {
		return $this->processorClass;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setProcessorClass($processorClass) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->processorClass = $processorClass;
	}


	/**
	* @public
	* @returns string
	*/
	function getTempDir() {
		return $this->tempDir;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTempDir($tempDir) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->tempDir = $tempDir;
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add a Controller property.
	*
	* <p>Calls a setter method on this class:<br>
	* Eg: "processorClass: maps to "setProcessorClass(...)".
	*
	* @param string	The property name
	* @param string	The property value
	* @public
	* @returns void
	*/
	function addProperty($name, $value) {

		if($this->configured) {
			return 'Configuration is frozen';
		}

		// XML boolean needs to be converted
		if( strtolower($value) == 'true') {
			$value = True;
		} elseif(strtolower($value) == 'false') {
			$value = False;
		}

		// Set the method matching the "name" parameter
		$beanUtils = new PhpBeanUtils();
		$beanUtils->setProperty($this, $name, $value);

	}


	/**
	* Freeze the configuration of this component.
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

	}


	/**
	* Return a String representation of this object.
	* @public
	* @returns string
	*/
	function toString() {

		$strBuff = 'ControllerConfig[';
		$strBuff .= 'bufferSize=';
		$strBuff .= $this->bufferSize;
		if($this->contentType != NULL) {
			$strBuff .= ',contentType=';
			$strBuff .= $this->contentType;
		}
		$strBuff .= ',locale=';
		$strBuff .= $this->locale;
		if($this->maxFileSize != NULL) {
			$strBuff .= ',maxFileSzie=';
			$strBuff .= $this->maxFileSize;
		}
		$strBuff .= ',nocache=';
		$strBuff .= $this->nocache;
		$strBuff .= ',processorClass=';
		$strBuff .= $this->processorClass;
		if($this->tempDir != NULL) {
			$strBuff .= ',tempDir=';
			$strBuff .= $this->tempDir;
        }
		$strBuff .= ']';
		return $strBuff;

	}


	// ----- Class Serialisation ID ----------------------------------------- //

	/** JCW
	* Serialize version info. This is to ensure that the serialized
	* php.MVC configuration data stored on disk is compatable with
	* this config class.
	* Update this info if making changes to the config classes that
	* would be incompatable with older versions
	*
	* <p>Returns a serial string, something like:
	*    "$className:$fileName:$versionID"
	*
	* @public
	* @returns string
	*/
	function getClassID() {

		// Class ID serialize version info
		$className = 'ControllerConfig';
		$fileName  = 'ControllerConfig.php';
		$versionID = '20021025-0955'; // date stamp

		return "$className:$fileName:$versionID";

	}

}
?>