<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/ClassPathTestCase.php,v 1.4 2006/02/22 07:19:20 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 07:19:20 $
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
* <p>
* .</p>
*
* @author John C. Wildenauer
*
* @version $Revision: 1.4 $
*/
class ClassPathTestCase extends TestCase {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param name String - Name of the test case
	*/
	function ClassPathTestCase($name) {
	
		parent::TestCase($name);	// build the base class
	
	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* Note: Dependent on the host server OS this is run on.
	*       Adjust the path delimiter character accordingly.
	*       Current test server OS is MS Windows.
	*/
	function test_AutoDetectHostPaths() {

		// Set the OS Type [UNIX|WINDOWS|MAC]
		$osType = '';

		$subAppPaths = array();
		$subAppPaths[] = 'aaa/bbb/ccc';
		$subAppPaths[] = 'xxx/yyy/zzz';

		$paths = ClassPath::getClassPath('HOME', $subAppPaths, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the Host OS path delimiter.
		// Note: Current test server OS is MS Windows ';'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad Auto Detect Host OS path (Current test server OS is MS Windows)');
	}  


	/**
	* 
	*/
	function test_UnixPaths() {

		// Set the OS Type [UNIX|WINDOWS|MAC]
		$osType = 'UNIX';

		$subAppPaths = array();
		$subAppPaths[] = 'aaa/bbb/ccc';
		$subAppPaths[] = 'xxx/yyy/zzz';

		$paths = ClassPath::getClassPath('HOME', $subAppPaths, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc:HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the Unix path delimiter ':'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc:HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad Unix path');
	}

	/**
	* 
	*/
	function test_WinPaths() {

		// Set the OS Type [UNIX|WINDOWS|MAC]
		$osType = 'WINDOWS';

		$subAppPaths = array();
		$subAppPaths[] = 'aaa/bbb/ccc';
		$subAppPaths[] = 'xxx/yyy/zzz';

		$paths = ClassPath::getClassPath('HOME', $subAppPaths, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the MS Windows path delimiter ';'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad Windows path');
	}


	/**
	* Note: Check the correct Mac path delimiter
	*/
	function test_MacPaths() {

		// Set the OS Type [UNIX|WINDOWS|MAC]
		$osType = 'MAC';

		$subAppPaths = array();
		$subAppPaths[] = 'aaa/bbb/ccc';
		$subAppPaths[] = 'xxx/yyy/zzz';

		$paths = ClassPath::getClassPath('HOME', $subAppPaths, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the Mac path delimiter ':'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc:HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad Mac path');
	}


	/**
	* Concatenate environment path strings
	*/
	function test_ConcatPaths() {

		$appPath1[] = 'aaa/bbb/ccc';
		$appPath2[] = 'xxx/yyy/zzz';
		
		// OS Type: UNIX
		$osType = 'UNIX';		
		$path1 = ClassPath::getClassPath('HOME', $appPath1, $osType);
		$path2 = ClassPath::getClassPath('HOME', $appPath2, $osType);
		$paths = ClassPath::concatPaths($path1, $path2, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc:HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the UNIX path delimiter ':'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc:HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad UNIX Concatenate path');


		// OS Type: WINDOWS
		$osType = 'WINDOWS';
		$path1 = ClassPath::getClassPath('HOME', $appPath1, $osType);
		$path2 = ClassPath::getClassPath('HOME', $appPath2, $osType);
		$paths = ClassPath::concatPaths($path1, $path2, $osType);

		if( ereg("(HOME/aaa/bbb/ccc.*)$", $paths, $regs) ) {
			// "HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz"
			$pathStr = $regs[1];
		}

		// Look for our last two paths with the UNIX path delimiter ':'
		$this->assert( 
				ereg("^HOME/aaa/bbb/ccc;HOME/xxx/yyy/zzz$", $pathStr) ,
				 'Bad WINDOWS Concatenate path');

	}


}
?>