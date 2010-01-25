<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/plugins/PHPTALPlugInDriver.php,v 1.2 2006/03/01 02:18:13 who Exp $
* $Revision: 1.2 $
* $Date: 2006/03/01 02:18:13 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
* PHPTALPlugInDriver. is a concrete implementation of the abstract APlugIn class.
*
* <p>This class is a wrapper class that implements the PHPTAL caching PHP
* template engine.</p>
*
* @author John C. Wildenauer
* @version $Revision: 1.2 $
*/
class PHPTALPlugInDriver extends APlugIn {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Set template file path.
	*/
	function setTemplate($path) {
		$this->plugIn->setTemplate($path);
	}

	// Note: Can no longer set the cache directory here.
	// Define the cache path in the application prepend.php file
	// or use the PHPTAL default cache dir: 'C:\WINNT\Temp\' or '/tmp/'
	/**
	* Set template cache directory. Like: "./WEB-INF/cache/"
	*/
	function setCacheDir($cachePath) {
		// THIS DOED NOT WORK: THIS CONSTANT IS ALREADY DEFINED IN PHPTAL.php
		define('PHPTAL_PHP_CODE_DESTINATION', $cachePath);
	}

	/**
	* Set template source.
	*
	* Should be used only with temporary template sources, prefer plain
	* files.
	*
	* @param $src string The phptal template source.
	*/
	function setSource($src) {
		$path = False;
		$this->plugIn->setSource($src, $path);
	}

	/**
	* Specify where to look for templates.
	*
	* @param $rep String or Array of repositories
	*/
	function setTemplateRepository($value) {
		$this->plugIn->setTemplateRepository($value);
	}

	/**
	* Ignore XML/XHTML comments on parsing.
	*/
	function stripComments($bool) {
		$this->plugIn->stripComments($bool);
	}

	/**
	* Set output mode (PHPTAL::XML or PHPTAL::XHTML).
	*/
	function setOutputMode($mode) {
		$this->plugIn->setOutputMode($mode);
	}

	/**
	* Set ouput encoding.
	*/
	function setEncoding($enc) {
		$this->plugIn->setEncoding($enc);
	}

	/**
	* Set I18N translator.
	*/
	function setTranslator($t) {
		$this->plugIn->setTranslator($t);
	}

	/**
	* Set template pre filter.
	*/
	function setPreFilter($filter) {
		$this->plugIn->setPreFilter($filter);
	}


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Implement the PHPTAL compiling PHP template engine.
	*
	* @public
	* @returns void
	*/
	function PHPTALPlugInDriver() {

		// Build the parent first
		parent::APlugIn();

		$path = False;					// "./WEB-INF/taltpl/myTalPage.html"
		$this->plugIn =& new PHPTAL($path);

	}


	// ----- Public Methods ------------------------------------------------- //

	// See abstract mathods

	/**
	* Receive notification that this application is starting up, and gives the
	* PlugIn driver an opportunity to initialise itself, if required.
	*
	* <p>This method is called from the ActionServer->getPlugIn() method.
	* The init() method returns immediately if the getPlugIn() method has already
	* been called. Eg: PlugIn initialisation is complete.</p>
	*
	* <p>Override this method in the concrete PlugIn wrapper class to provide
	* application specific behavior as required.</p>
	*
	* @param ApplicationConfig		The ApplicationConfig configuration object.
	*  This parameter is optional.
	* @public
	* @returns void
	*/
	function init($config='') {

		// 
		if($this->init) {
			// We have already been initialised
			return;
		}

		// Initialise the PlugIn as required
		// Some once-off initialisations
		$tal =& $this->plugIn; // additional setup

		// Done
		$this->init = True;

	}

}
?>