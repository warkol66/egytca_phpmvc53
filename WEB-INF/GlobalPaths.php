<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/GlobalPaths.php,v 1.6 2006/05/17 22:42:07 who Exp $
* $Revision: 1.6 $
* $Date: 2006/05/17 22:42:07 $
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
* The application-wide paths
*
* @author John C Wildenauer
* @version $Revision: 1.6 $
* @public
*/
class GlobalPaths {

	/**
	* Return an array of global paths
	* 
	* @public
	* @returns array	
	*/
	function getGlobalPaths() {

		// Setup the main phpmvc application include() directories here
		// Note: could be placed in a n xml config file later !!
		$appDirs = array();
		$appDirs[] = ''; // application root directory
		$appDirs[] = 'WEB-INF';
		$appDirs[] = 'WEB-INF/lib';
		$appDirs[] = 'WEB-INF/lib/adodb';
		$appDirs[] = 'WEB-INF/lib/collections';
		$appDirs[] = 'WEB-INF/lib/database';
		$appDirs[] = 'WEB-INF/lib/digester';
		$appDirs[] = 'WEB-INF/lib/logging';
		$appDirs[] = 'WEB-INF/lib/pear';
		$appDirs[] = 'WEB-INF/lib/pear/DB';
		$appDirs[] = 'WEB-INF/lib/phplib';
		$appDirs[] = 'WEB-INF/lib/smarty';
		$appDirs[] = 'WEB-INF/lib/utils';
		$appDirs[] = 'WEB-INF/lib/validator';
		$appDirs[] = 'WEB-INF/classes/phpmvc/action';
		$appDirs[] = 'WEB-INF/classes/phpmvc/actions';
		$appDirs[] = 'WEB-INF/classes/phpmvc/appserver';
		$appDirs[] = 'WEB-INF/classes/phpmvc/authenticator';
		$appDirs[] = 'WEB-INF/classes/phpmvc/config';
		$appDirs[] = 'WEB-INF/classes/phpmvc/connector';
		$appDirs[] = 'WEB-INF/classes/phpmvc/dbcp';
		$appDirs[] = 'WEB-INF/classes/phpmvc/plugins';
		$appDirs[] = 'WEB-INF/classes/phpmvc/realm';
		$appDirs[] = 'WEB-INF/classes/phpmvc/upload';
		$appDirs[] = 'WEB-INF/classes/phpmvc/utils';

		// Adding external libraries:
		// Absolute paths are allowed in the GlobalPaths.php and ModulePaths.php 
		// files. Eg: if a path starts with a "char:\" (like: "D:\") or a forward 
		// slash (like: "/some/path/" on unix), then the php.MVC base directory
		// is not prepended to the path. See: ClassPath.php::getClassPath(...).
	#	$appDirs[] = 'D:\Dev\PHP\PEAR';			// Win32 Regular path - No trailing slash
	#	$appDirs[] = 'D:\Dev\PHP\PEAR\\';		// Win32 needs escaped trailing slash now (PHP!!!)
	#	$appDirs[] = 'D:/Dev/PHP/PEAR/DB';		// Win32 path using forward slashes
	#	$appDirs[] = '/lib/dev/php/PEAR/DB';	// *nix paths

		return $appDirs;

	}

}
?>
