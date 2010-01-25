<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ViewResourcesConfig.php,v 1.3 2006/02/22 08:59:09 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:59:09 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2004-2006 John C.Wildenauer.  All rights reserved.
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
* <p>A Bean representing the configuration information of a
* <code>&lt;view-resource&gt;</code> element in a php.MVC application
* configuration file.<p>
*
* @author John C. Wildenauer 
* @version $Revision: 1.3 $
* @public
*/
class ViewResourcesConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured (via the phpmvc-config.xml)?
	* @private
	* @type boolean
	*/
	var $configured = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* The application title
	* @private
	* @type string
	*/
	var $appTitle		= 'My Web Application';

	/**
	* The application version
	* @private
	* @type string
	*/
	var $appVersion	= '1.0';

	/**
	* The copyright notice
	* @private
	* @type string
	*/
	var $copyright		= 'Copyright © YYYY My Name. All rights reserved.';

	/**
	* The contact information
	* @private
	* @type string
	*/
	var $contactInfo	= 'webmaster@myhost.com';

	/**
	* Do we run the template engine processor<br>
	*  - Use <code>True</code> during active development<br>
	*  - Use <code>False</code> when pages are in production (complete).
	* @private
	* @type boolean
	*/
	var $processTags	= False;	// Development/production flag. [True|False]

	/**
	* Force compile current page, and all included pages.
	*  - Use <code>True</code> to compile the current page and all included tag pages<br>
	*  - Use <code>False</code> to compile only changed files.
	* @private
	* @type boolean
	*/
	var $compileAll	= False;

	/**
	* The left tag identifier. Defaults to '&lt;@'
	* @private
	* @type string
	*/
	var $tagL 		= '<@';					// The dafault tags

	/**
	* The right tag identifier. Defaults to '@&gt;'
	* @private
	* @type string
	*/
	var $tagR		= '@>';

	/**
	* The view resource templates directory. No trailing slash.
	* @private
	* @type string
	*/
	var $tplDir		= './WEB-INF/tpl';

	/**
	* The compiled templates directory. No trailing slash.
	* @private
	* @type string
	*/
	var $tplDirC	= './WEB-INF/tpl_C';

	/**
	* The compiled file notation. Eg: "pageContent.sspC".
	* @private
	* @type string
	*/
	var $extC		= 'C';

	/**
	* The maximum size of the template files we are allowed to write to disk, in bytes.
	* @private
	* @type integer
	*/
	var $maxFileLength = 250000;

	/**
	* Indicates tag template source file(s) to be pre-processed. Eg: "myPage.ssp".<br>
	* This extension triggers tag processing.
	* @private
	* @type string
	*/
	var $tagFlagStr = '.ssp';

	/**
	* The number of trailing filename characters to sample. [xxxYyy.ssp].<br>
	* For example, -4 says to sample the last four characters of the resource file
	* name. Something like ".ssp".
	* @private
	* @type integer
	*/
	var $tagFlagCnt = -4;


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setAppTitle($appTitle) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->appTitle = $appTitle;
	}

	/**
	* @public
	* @returns string
	*/
	function getAppTitle() {
		return $this->appTitle;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setAppVersion($appVersion) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->applVersion = $appVersion;
	}

	/**
	* @public
	* @returns string
	*/
	function getAppVersion() {
		return $this->appVersion;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setCopyright($copyright) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->copyright = $copyright;
	}

	/**
	* @public
	* @returns string
	*/
	function getCopyright() {
		return $this->copyright;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setContactInfo($contactInfo) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->contactInfo = $contactInfo;
	}

	/**
	* @public
	* @returns string
	*/
	function getContactInfo() {
		return $this->contactInfo;
	}


	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setProcessTags($process) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->processTags = $process;
	}

	/**
	* @public
	* @returns boolean
	*/
	function getProcessTags() {
		return $this->processTags;
	}


	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setCompileAll($compileAll) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->compileAll = $compileAll;
	}

	/**
	* @public
	* @returns boolean
	*/
	function getCompileAll() {
		return $this->compileAll;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTagLeft($tagL) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tagL = $tagL;
	}

	/**
	* @public
	* @returns string
	*/
	function getTagLeft() {
		return $this->tagL;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTagRight($tagR) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tagL = $tagR;
	}

	/**
	* @public
	* @returns string
	*/
	function getTagRight() {
		return $this->tagR;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTplDir($tplDir) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tplDir = $tplDir;
	}

	/**
	* @public
	* @returns string
	*/
	function getTplDir() {
		return $this->tplDir;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTplDirC($tplDirC) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tplDir = $tplDirC;
	}

	/**
	* @public
	* @returns string
	*/
	function getTplDirC() {
		return $this->tplDirC;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setComplieExtChar($extC) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->extC = $extC;
	}

	/**
	* @public
	* @returns string
	*/
	function getComplieExtChar() {
		return $this->extC;
	}


	/**
	* @param integer
	* @public
	* @returns void
	*/
	function setMaxFileLength($maxFileLength) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->maxFileLength = $maxFileLength;
	}

	/**
	* @public
	* @returns integer
	*/
	function getMaxFileLength() {
		return $this->maxFileLength;
	}


	/**
	* @param string
	* @public
	* @returns void
	*/
	function setTagFlagStr($tagFlagStr) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tagFlagStr = $tagFlagStr;
	}

	/**
	* @public
	* @returns string
	*/
	function getTagFlagStr() {
		return $this->tagFlagStr;
	}


	/**
	* @param integer
	* @public
	* @returns void
	*/
	function setTagFlagCnt($tagFlagCnt) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->tagFlagCnt = $tagFlagCnt;
	}

	/**
	* @public
	* @returns integer
	*/
	function getTagFlagCnt() {
		return $this->tagFlagCnt;
	}


	/**
	* Add a configuration property.
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


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Freeze the configuration of this component.
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

	}


	/**
	* Return a String representation of this object
	* @public
	* @returns string
	*/
	function toString() {

		$sb = 'ViewResourcesConfig[';
		$sb .= 'configured=';
		($this->configured == True) ? $sb .= "True" : $sb .= "False";
		$sb .= ', appTitle=';
		$sb .= $this->appTitle;
		$sb .= ', appVersion=';
		$sb .= $this->appVersion;
		$sb .= ', copyright=';
		$sb .= $this->copyright;
		$sb .= ', contactInfo=';
		$sb .= $this->contactInfo;
		$sb .= ', processTags=';
		($this->processTags == True) ? $sb .= "True" : $sb .= "False";
		$sb .= ', compileAll=';
		($this->compileAll == True) ? $sb .= "True" : $sb .= "False";
		$sb .= ', tagL=';
		$sb .= $this->tagL;
		$sb .= ', tagR=';
		$sb .= $this->tagR;
		$sb .= ', tplDir=';
		$sb .= $this->tplDir;
		$sb .= ', tplDirC	=';
		$sb .= $this->tplDirC;	
		$sb .= ', extC=';
		$sb .= $this->extC;		
		$sb .= ', maxFileLength=';
		$sb .= $this->maxFileLength;		
		$sb .= ', tagFlagStr=';
		$sb .= $this->tagFlagStr;
		$sb .= ', tagFlagCnt=';
		$sb .= $this->tagFlagCnt;
		$sb .= ']';
		return $sb;

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
		$className = 'ViewResourcesConfig';
		$fileName  = 'ViewResources.php';
		$versionID = '20040811-1200'; // date stamp

		return "$className:$fileName:$versionID";

	}
}
?>