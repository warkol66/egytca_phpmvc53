<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/SetNextRule.php,v 1.3 2006/02/22 08:48:48 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:48:48 $
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
* <p>Rule implementation that calls a method on the (top-1) (parent)
* object, passing the top object (child) as an argument.  It is
* commonly used to establish parent-child relationships.</p>
*
* <p>Eg: Foreign key object mapping
* <pre><code>    
*     [next-to top-of-stack]           [top-of-stack]
*     ----------------------           --------------
*     |  oDataSources      |           |oDataSource |
*     |~~~~~~~~~~~~~~~~~~~~|1         M|~~~~~~~~~~~~|
*     | oDataSource        |---------->|driver='xxx'|
*     |                    |           |user  ='yyy'|
*     |                    |           |pword ='zzz'|
*     ----------------------           --------------
* </code></pre>
*
* @author John C.Wildenauer<br>
*  Credits:<br>
*  Craig McClanahan 
* @version $Revision: 1.3 $
* @public
*/
class SetNextRule extends Rule {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The method name to call on the parent object.
	* @type string
	*/
	var $methodName = NULL;	// String


	/**
	* The Java class name of the parameter type expected by the method.
	* @type string
	*/
	var $paramType = NULL;	// String [future use !!]


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a "set next" rule with the specified method name.
	*
	* @param string The method name of the parent method to call
	* @param string [future use !!]
	*/
	function SetNextRule($methodName, $paramType=NULL) {

		$this->methodName	= $methodName;
		$this->paramType	= $paramType;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the end of this element.
	*
	* @param Digester A reference to the digester instance
	* @returns void
	* @public
	*/
	function end(&$digester) {

		$this->digester =& $digester;

		// Identify the objects to be used
		$oChild	=& $this->digester->peek(0);	// top-of-stack
		$oParent	=& $this->digester->peek(1);	// next-to-top-of-stack

		$log		= $this->digester->log;
		$debug 	= $log->getLog('isDebugEnabled');

		// Parent-Child object binding. Eg:
		//  [next-to-top-of-stack]      [top-of-stack]
		//     [oDataSources]1<---------M[oDataSource]

		if($debug) {
			if($oParent == NULL) {
				$log->debug('SetNextRule->end(){'.$this->digester->match.
								'} Call [NULL PARENT].'.$this->methodName.
								'('.get_class($oChild).')');
			} else {
				$log->debug('[SetNextRule]{'.$this->digester->match.
								'} Call '.get_class($oParent).'->'.
								$this->methodName .'('.get_class($oChild).')');
			}
		}

		// Call the specified method

		if(($oChild != NULL) && ($oParent != NULL)) {

			// Setup the foreign key reference	
			$methodName = $this->methodName;
			$oParent->$methodName($oChild);
    
		}

	}


	/**
	* Render a printable version of this Rule.
	*
	* @returns string
	* @public
	*/
	function toString() {

		$sb  = 'SetNextRule[';	// StringBuffer
		$sb  .= 'methodName=';
		$sb  .= $this->methodName;
		$sb  .= ', paramType=';
		$sb  .= $this->paramType;
		$sb  .= ']';

		return $sb;   		

    }

}
?>