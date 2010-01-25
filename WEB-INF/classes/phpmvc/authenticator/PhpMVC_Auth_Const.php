<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/authenticator/PhpMVC_Auth_Const.php,v 1.1 2006/05/17 07:50:06 who Exp $
* $Revision: 1.1 $
* $Date: 2006/05/17 07:50:06 $
*
* ====================================================================
*
* License:	GNU General Public License
*
* Copyright (c) 2006 John C.Wildenauer.  All rights reserved.
* Note: Original work copyright to respective authors
*
* This file is part of php.MVC.
*
* php.MVC is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version. 
* 
* php.MVC is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details. 
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, 
* USA.
*/

class PhpMVC_Auth_Const {

	/**
	* PhpMVC Authorisation Constants
	*
	* @param string
	* @public	
	* @returns string
	*/
	function getKey($key) {

		// Simulate Java static final constants
		switch($key) {

			// ----- Public Auth Constants ------------------------------------ //

			// Authentication methods for login configuration
			case 'BASIC_METHOD': 
				$keyVal = 'BASIC';
				break;

			case 'CERT_METHOD': 
				$keyVal = 'CLIENT-CERT';
				break;

			case 'DIGEST_METHOD': 
				$keyVal = 'DIGEST';
				break;

			case 'FORM_METHOD': 
				$keyVal = 'FORM';
				break;
	
			case 'SERVICE_METHOD': 		// +JCW
				$keyVal = 'SERVICE';
				break;

			// User data constraints for transport guarantee
			#public static final String NONE_TRANSPORT = "NONE";
			#public static final String INTEGRAL_TRANSPORT = "INTEGRAL";
			#public static final String CONFIDENTIAL_TRANSPORT = "CONFIDENTIAL";

			// Form based authentication constants
			case 'FORM_ACTION': 
				$keyVal = '/p_security_check';
				break;

			case 'FORM_PASSWORD': 
				$keyVal = 'p_password';
				break;

			case 'FORM_USERNAME': 
				$keyVal = 'p_username';
				break;

			// Cookie name for single sign on support
			#var $SINGLE_SIGN_ON_COOKIE = "PSESSIONIDSSO";

			// ----- Request Notes -------------------------------------------- //

			// The previously authenticated principal. [JCW]
			case 'REQ_PRINCIPAL_NOTE': 
				$keyVal = 'php-mvc.authenticator.PRINCIPAL';
				break;

			// ----- Session Notes -------------------------------------------- //

		}

		return $keyVal;
	}

}
?>