<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/DigesterTestSuite.php,v 1.4 2006/02/22 07:25:23 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 07:25:23 $
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
* Main test suite for the Digester utility
*/

echo '<center><h3>Php.MVC::DigesterTestSuite</h3></center><br>';

// Get the path to this instance of the phpmvc base
$path = getcwd();
eregi("^(.*)web-inf.*$", $path, $regs);
$phpmvcRoot  = $regs[1];														// 'C:/Www/phpmvc-base/'
$phpmvcRoot = substr($phpmvcRoot, 0, -1); 								// 'C:/Www/phpmvc-base'

include $phpmvcRoot.'/WEB-INF/classes/phpmvc/utils/ClassPath.php';// GlobalPrependXMLEx.php
include $phpmvcRoot.'/WEB-INF/lib/logging/PhpMVC_Log.php';			// Logging

$appServerRootDir = $phpmvcRoot;												// <<< GlobalPrependXMLEx.php
include $phpmvcRoot.'/WEB-INF/GlobalPrependXMLEx.php';				// Digester and friends
include $phpmvcRoot.'/WEB-INF/lib/phpunit/phpunit.php';

$tp = $phpmvcRoot.'/WEB-INF/lib/digester/test';							// Path to digester tests

// Include the test cases and any required test classes:
include $tp.'/TestRule.php';
include $tp.'/TestRuleSet.php';
include $tp.'/BeanPropertySetterRuleTestCase.php';
include $tp.'/DigesterTestCase.php';
include $tp.'/RuleTestCase.php';
include $tp.'/RulesBaseTestCase.php';

$suite1 = new TestSuite( 'BeanPropertySetterRuleTestCase' );
$suite2 = new TestSuite( 'DigesterTestCase' );
$suite3 = new TestSuite( 'RuleTestCase' );
$suite4 = new TestSuite( 'RulesBaseTestCase' );

$suite = new TestSuite();
$suite->addTest( $suite1 );
$suite->addTest( $suite2 );
$suite->addTest( $suite3 );
$suite->addTest( $suite4 );

echo '<STYLE TYPE="text/css">';
include_once $appServerRootDir.'/WEB-INF/lib/phpunit/stylesheet.css';
echo ' </STYLE>';
$result = new PrettyTestResult;
$suite->run($result);
$result->report();	// Dump the array

?>