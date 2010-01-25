<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/@PhpMVCTestSuite.php,v 1.6 2006/02/22 06:28:10 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 06:28:10 $
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
* Main test suite for the php.MVC framework
*/

error_reporting(E_ALL);

echo '<center><h3>Php.MVC::PhpMVCTestSuite</h3></center><br>';

// Get the path to this instance of the phpmvc base
$path = getcwd();
eregi("^(.*)web-inf.*$", $path, $regs);
$phpmvcRoot  = $regs[1];														// Like: 'C:/Www/phpmvc-base/'
$phpmvcRoot = substr($phpmvcRoot, 0, -1); 								// Drop the trailing slash
$appServerRootDir = $phpmvcRoot;												// GlobalPrependEx.php

// Setup application class paths first
include $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/ClassPath.php';

// Setup the app server paths
include $phpmvcRoot.'/WEB-INF/GlobalPaths.php';
$globalPaths = GlobalPaths::getGlobalPaths();
$gPath = ClassPath::getClassPath($phpmvcRoot, $globalPaths, '');	// Auto-detect the OS

// Add the "/WEB-INF/classes/phpmvc/test" path
$gPath = PATH_SEPARATOR.$gPath.PATH_SEPARATOR.$phpmvcRoot.'/WEB-INF/classes/phpmvc/test';

// Set the 'include_path' variables, as used by the file functions
ini_set('include_path', $gPath);

include $phpmvcRoot.'/WEB-INF/GlobalPrependEx.php';					// Base classes
include $phpmvcRoot.'/WEB-INF/GlobalPrependXMLEx.php';				// Digester and friends
include $phpmvcRoot.'/WEB-INF/lib/phpunit/phpunit.php'; 

$tp = $phpmvcRoot.'/WEB-INF/classes/phpmvc/test';						// Path to tests

// Include the test cases and any required test classes:
include $tp.'/PlugInDriverTestClass.php';

include $tp.'/ActionsTestCase.php';
include $tp.'/MessageFormatTestCase.php';
include $tp.'/PropertyMessageResourcesTestCase.php';
include $tp.'/ActionChainsTestCase.php';
include $tp.'/PlugInsTestCase.php';

include $tp.'/ClassPathTestCase.php';

$suite1 = new TestSuite( 'ActionsTestCase' );
$suite2 = new TestSuite( 'MessageFormatTestCase' );
$suite3 = new TestSuite( 'PropertyMessageResourcesTestCase' );
$suite4 = new TestSuite( 'ActionChainsTestCase' ); 
$suite5 = new TestSuite( 'PlugInsTestCase' );

// Run ClassPathTestCase last as it alters the environment paths !
$suite100 = new TestSuite( 'ClassPathTestCase' );	


$suite = new TestSuite();
$suite->addTest( $suite1 );
$suite->addTest( $suite2 );
$suite->addTest( $suite3 );
$suite->addTest( $suite4 );
$suite->addTest( $suite5 );

$suite->addTest( $suite100 );


echo '<STYLE TYPE="text/css">';
include_once $appServerRootDir.'/WEB-INF/lib/phpunit/stylesheet.css';
echo ' </STYLE>';

$result = new PrettyTestResult;
$suite->run($result);
$result->report();	// Dump the array


?>