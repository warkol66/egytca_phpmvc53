<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/ClassPath.php,v 1.5 2006/05/17 07:03:53 who Exp $
* $Revision: 1.5 $
* $Date: 2006/05/17 07:03:53 $
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
* <p>Setup the application class paths for the include files, for the
* duration of the main script</p>
*
* @author John C. Wildenauer
* @version $Revision: 1.5 $
* @public
*/
class ClassPath {

	// ----- Depreciated ---------------------------------------------------- //

	/**
	* <p>Setup the application class paths (PHP 'include_path') for the included
	* class files, for the duration of the main script</p>
	*
	*<p>Returns the class path string for testing purposes
	*
	* @depreciated
	* @param string	The appServerRootDir. eg: 'C:/Www/phpmvc'
	* @param array		An array of sub-application paths,<br>
	*  eg: $subAppPaths[] = 'WEB-INF/classes/example';, ...
	* @param string	The OS [Optional] [UNIX|WINDOWS|MAC|...] if we have
	*  trouble detecting the server OS type. Eg: path errors.
	* @public
	* @returns string
	*/
	function setClassPath($appServerRootDir='', $subAppPaths='', $osType='') {

		// Set AppServer root manually for now
		if($appServerRootDir == '') {
			echo 'Error: ClassPath :- No php.MVC application root directory specified';
			exit;
		}

		#$_ENV;	// PHP Superglobals !!

		// Setup the main phpmvc application include() directories here
		// Note: could be placed in a n xml config file later !!
		$appDirs = array();
		$appDirs[] = ''; // application root directory
		$appDirs[] = 'WEB-INF';
		$appDirs[] = 'WEB-INF/lib/collections';
		$appDirs[] = 'WEB-INF/lib/database';
		$appDirs[] = 'WEB-INF/lib/digester';
		$appDirs[] = 'WEB-INF/lib/logging';
		$appDirs[] = 'WEB-INF/lib/phplib';
		$appDirs[] = 'WEB-INF/lib/utils';
		$appDirs[] = 'WEB-INF/lib/validator';
		$appDirs[] = 'WEB-INF/classes/phpmvc/action';
		$appDirs[] = 'WEB-INF/classes/phpmvc/actions';
		$appDirs[] = 'WEB-INF/classes/phpmvc/appserver';
		$appDirs[] = 'WEB-INF/classes/phpmvc/config';
		$appDirs[] = 'WEB-INF/classes/phpmvc/connector';
		$appDirs[] = 'WEB-INF/classes/phpmvc/dbcp';
		$appDirs[] = 'WEB-INF/classes/phpmvc/utils';


		// Add the sub-application paths, if any
		if(is_array($subAppPaths)) {
			$appDirs = array_merge($appDirs, $subAppPaths);
		}


		// Setup the platform specific path delimiter character
		$delim = NULL;	// path delimiter character. (Windows, Unix, Mac!!)
		$winDir = NULL;
		if( (int)phpversion() > 4 ) {
			// PHP 5
			$winDir = $_ENV["windir"];					// See: PHP v.4.1.0 Superglobals 
		} else {
			// PHP 4
			global $HTTP_ENV_VARS;						// depreciated- 
			if( array_key_exists("windir", $HTTP_ENV_VARS) ) {
				$winDir = $HTTP_ENV_VARS["windir"];	// will be replaced with $_ENV
			}
		}


		if($osType != '') {
			if( preg_match("/WINDOWS/i", $osType) || preg_match("/WIN/i", PHP_OS)) {
				$delim = ';';	// Windows
			} elseif( preg_match("/UNIX/i", $osType) || preg_match("/LINUX/i", PHP_OS) ) {
				$delim = ':';	// Unix
			} elseif( preg_match("/MAC/i", $osType) ) {
				$delim = ':';	// Mac !!!!!
			}
		}

		if($delim == NULL) {
			if( preg_match("/WIN/i", $winDir) ) { // _ENV["C:\\Win2K"]
			    $delim = ';';	// Windows
			} else {
				$delim = ':';	// Unix, Mac !!
			}
		}

		// Get the current working directory
		$path = $appServerRootDir;

		// Strip path directories below 'WEB-INF'
		$pathToWebInf = preg_replace("/WEB-INF.*$/i", '', $path);

		// Replace path backslashes with forward slashes
		// Note: PHP Regular Expressions do not work with backslashes
		$pathToWebInf = str_replace("\\", "/", $pathToWebInf);

		// Drop the trailing slash, if one is present
		$pathToWebInf = preg_replace("/\/$/i", '', $pathToWebInf);

		// Setup the environment path string
		$classPath = NULL;
		foreach($appDirs as $appDir) {	
			$classPath .= $pathToWebInf.'/'.$appDir.$delim;
		}

		// Remove trailing delimiter character
		$classPath = substr($classPath, 0, -1);	

		// Setup the include_path for the duration of the main php.MVC script
		ini_set('include_path', $classPath);

		return $classPath;	// for testing

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* <p>Setup the application class paths (PHP 'include_path') for the included
	* class files, for the duration of the main script</p>
	*
	*<p>Returns the class path string for testing purposes
	*
	* @param string	The appServerRootDir. eg: 'C:/Www/phpmvc'
	* @param array		An array of sub-application paths,<br>
	*  eg: $subAppPaths[] = 'WEB-INF/classes/example';, ...
	* @param string	The OS [Optional] [UNIX|WINDOWS|MAC|...] if we have
	*  trouble detecting the server OS type. Eg: path errors.
	* @public
	* @returns string
	*/
	function getClassPath($appServerRootDir='', $appDirs, $osType='') {

		// Set AppServer root manually for now
		if($appServerRootDir == '') {
			echo 'Error: ClassPath :- No php.MVC application root directory specified';
			exit;
		}

		// Setup the platform specific path delimiter character
		$delim = NULL;	// path delimiter character. (Windows, Unix, Mac!!)
		if($osType == '') {
			// PHP's build in constant "PATH_SEPARATOR" [unix (:) / win (;)]
			$delim = PATH_SEPARATOR;
		} else {
			// It is handy to be able to specift the OS type for testing
			$delim = ClassPath::getPathDelimiter($osType);
		}

		// Get the current working directory
		$path = $appServerRootDir;

		// Strip path directories below 'WEB-INF'
		$pathToWebInf = preg_replace("/WEB-INF.*$/i", '', $path);

		// Replace path backslashes with forward slashes
		// Note: PHP Regular Expressions do not work with backslashes
		$pathToWebInf = str_replace("\\", "/", $pathToWebInf);

		// Drop the trailing slash, if one is present
		$pathToWebInf = preg_replace("/\/$/i", '', $pathToWebInf);

		// Setup the environment path string
		$classPath		= NULL;
		$AbsolutePath	= False;	// Say: "/Some/Unix/Path/" or "D:\Some\Win\Path"
		foreach($appDirs as $appDir) {	

			// Check if the specified system path is an absolute path. Absolute system
			// paths start with a "/" on Unix, and "Ch\:" or "Ch/:" on Win 32.
			// Eg: "/Some/Unix/Path/" or "D:\Some\Win\Path" or "D:/Some/Win/Path".
			$AbsolutePath = ClassPath::absolutePath($appDir);

			if($AbsolutePath == True) {
				$classPath .= $appDir.$delim;
			} else {
				$classPath .= $pathToWebInf.'/'.$appDir.$delim;
			}

		}

		// Remove trailing delimiter character
		$classPath = substr($classPath, 0, -1);	

		return $classPath;	// for testing

	}


	/**
	* Concatenate environment path strings
	* <p>
	* Returns the two path strings joined with the correct environment
	* string delimiter for the host operating system.
	* 
	* @param		string	The path string
	* @param		string	The path string
	* @param		string	The operating type [optional]
	* @public
	* @returns	string	
	*/
	function concatPaths($path1, $path2, $osType='') {

		// Setup the platform specific path delimiter character
		$delim = NULL;	// path delimiter character. (Windows, Unix, Mac!!)
		$delim = PATH_SEPARATOR;

		$path = $path1 . $delim . $path2;
		return $path;

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Get environment path delimiter.
	* <p>
	* Returns the environment string delimiter for the host operating system.
	*
	* @param		string	The operating type [optional]
	* @protected
	* @returns	string	
	*/
	function getPathDelimiter($osType='') {

		// Setup the platform specific path delimiter character
		$delim = NULL;	// path delimiter character. (Windows, Unix, Mac!!)
		$winDir = NULL;
		if( (int)phpversion() > 4 ) {
			// PHP 5
			if(!empty($_ENV["windir"]))
				$winDir = $_ENV["windir"];					// See: PHP v.4.1.0 Superglobals 
		} else {
			// PHP 4
			global $HTTP_ENV_VARS;						// depreciated- 
			if( array_key_exists("windir", $HTTP_ENV_VARS) ) {
				$winDir = $HTTP_ENV_VARS["windir"];	// will be replaced with $_ENV
			}
		}

		if($osType != '') {
			if( preg_match("/WINDOWS/i", $osType) ) {
				$delim = ';';	// Windows
			} elseif( preg_match("/UNIX/i", $osType) ) {
				$delim = ':';	// Unix
			} elseif( preg_match("/MAC/i", $osType) ) {
				$delim = ':';	// Mac !!!!!
			}
		}

		if($delim == NULL) {
			if( preg_match("/WIN/i", $winDir) ) { // _ENV["C:\\Win2K"]
			    $delim = ';';	// Windows
			} else {
				$delim = ':';	// Unix, Mac !!
			}
		}

		return $delim;

	}


	/** 
	* Check if the specified system path is an absolute path. Absolute system
	* paths start with a "/" on Unix, and "Ch\:" or "Ch/:" on Win 32.
	* Eg: "/Some/Unix/Path/" or "D:\Some\Win\Path" or "D:/Some/Win/Path".
	*
	* Returns True if the suppplied path absolute, otherwise returns False
	*
	* @param string	The path to check, like: "/Some/Unix/Path/" or
	*						"D:\Some\Win\Path".
	* @public
	* @returns boolean
	*/
	function absolutePath($systemPath) {

		// Say: "/Some/Unix/Path/" or "D:\Some\Win\Path" or "D:/Some/Win/Path"
		$fAbsolutePath	= False;		// Boolean flag value

		//"[/]Some/Unix/Path/"
		if (preg_match("/^\//", $systemPath)) {
			$fAbsolutePath = True;
		//"[D:\]Some\Win\Path"
		// "i" says "ignore case"
		// Note the extra escape "\" reqd for this to work with  PHP !!!
		} elseif(preg_match("/^[a-z]:\\\/i", $systemPath)) {	
			$fAbsolutePath = True;
		//"[D:/]Some/Win/Path"
		} elseif(preg_match("/^[a-z]:\//i", $systemPath)) {
			$fAbsolutePath = True;
		}

		return $fAbsolutePath;

	}

}
?>