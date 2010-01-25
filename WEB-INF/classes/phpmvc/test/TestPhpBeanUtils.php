<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/TestPhpBeanUtils.php,v 1.4 2006/02/22 08:56:10 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:56:10 $
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


// Setup application environment paths first
if(!defined('CLASSPATH')) {
	$path = getcwd();
	eregi("^(.*)web-inf.*$", $path, $regs);
	$phpmvcRoot = $regs[1];					// 'C:/Www/phpmvc-base'
	eregi("^(.*web-inf)", $path, $regs);
	$classPath = $regs[1].'/classes/phpmvc/utils/ClassPath.php';
	include $classPath;
	$morePaths = array();
	ClassPath::setClassPath($phpmvcRoot, $morePaths);
	define('CLASSPATH', True);
}


include 'PhpBeanUtils.php';
include 'TestPhpBean.php';

$butils	= new PhpBeanUtils;
$ot		= new TestPhpBean;

$name1		= 'StringProperty';
$value1		= 'xxxxxxxx';
$name2		= 'WriteOnlyProperty';
$value2		= 'yyyyyyyy';

$properties = array($name1 => $value1, $name2 => $value2);
$butils->populate($ot, $properties);

print_r($ot);


?>