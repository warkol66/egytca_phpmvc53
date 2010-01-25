<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/MessageResourcesTestSuite.php,v 1.4 2006/02/22 08:19:03 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:19:03 $
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

error_reporting(E_ALL);


// Setup application environment paths first
if(!defined('CLASSPATH')) {
	$path = getcwd();
	eregi("^(.*)web-inf.*$", $path, $regs);
	$phpmvcRoot = $regs[1];					// 'C:/Www/phpmvc-base'
	eregi("^(.*web-inf)", $path, $regs);
	$classPath = $regs[1].'/classes/phpmvc/utils/ClassPath.php';
	include $classPath;
	$morePaths = array();
	$morePaths[] = 'WEB-INF/classes/phpmvc/action';
	$morePaths[] = 'WEB-INF/classes/phpmvc/actions';
	$morePaths[] = 'WEB-INF/classes/phpmvc/appserver';
	$morePaths[] = 'WEB-INF/classes/phpmvc/config';
	$morePaths[] = 'WEB-INF/classes/phpmvc/connector';
	$morePaths[] = 'WEB-INF/classes/phpmvc/dbcp';
	$morePaths[] = 'WEB-INF/classes/phpmvc/plugins';
	$morePaths[] = 'WEB-INF/classes/phpmvc/test';
	$morePaths[] = 'WEB-INF/classes/phpmvc/uploads';
	$morePaths[] = 'WEB-INF/classes/phpmvc/utils';
	$morePaths[] = 'WEB-INF/lib/adodb';
	$morePaths[] = 'WEB-INF/lib/collections';
	$morePaths[] = 'WEB-INF/lib/digester';
	$morePaths[] = 'WEB-INF/lib/logging';
	$morePaths[] = 'WEB-INF/lib/pear';
	$morePaths[] = 'WEB-INF/lib/phplib';
	$morePaths[] = 'WEB-INF/lib/smarty';
	$morePaths[] = 'WEB-INF/lib/utils';
	ClassPath::setClassPath($phpmvcRoot, $morePaths);
	define('CLASSPATH', True);
}


include_once 'WEB-INF/lib/phpunit/phpunit.php';
include_once 'MessageFormatTestCase.php';
include_once 'PropertyMessageResourcesTestCase.php';


/**
* Message Resources and Format Test Suite for the php.MVC framework
*/

$suite1 = new TestSuite( 'MessageFormatTestCase' );
$suite2 = new TestSuite( 'PropertyMessageResourcesTestCase' );

$suite = new TestSuite();
$suite->addTest( $suite1 );
$suite->addTest( $suite2 );

echo '<STYLE TYPE="text/css">';
include_once 'WEB-INF/lib/phpunit/stylesheet.css';
echo ' </STYLE>';

$result = new PrettyTestResult;
$suite->run($result);
$result->report();	// Dump the array


?>