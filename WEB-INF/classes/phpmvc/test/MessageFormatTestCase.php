<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/MessageFormatTestCase.php,v 1.5 2006/02/22 08:17:55 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 08:17:55 $
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
*
* @author John C. Wildenauer

* @version $Revision: 1.5 $
*/
class MessageFormatTestCase extends TestCase {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param string	Name of the test case
	*/
	function MessageFormatTestCase($name) {
	
		parent::TestCase($name);	// build the base class
	
	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* Parsing message string using one array replacement parameter
	*/
	function test_pattern1() {
		
		$pattern		= 'This is my {0} message post';
		$charArgs	= array(0 => 'first');
		$intArgs		= array(1);
		$floatArgs	= array(2.50);
		$format = new MessageFormat($pattern);

		//Using character replacement parameters
		$this->assertEquals( 'This is my first message post', 
					$format->formatMsg($charArgs), 
				 	'Error parsing message string using character replacement 
				 	parameters');

		//Using integer replacement parameters
		$this->assertEquals( 'This is my 1 message post', 
					$format->formatMsg($intArgs), 
				 	'Error parsing message string using integer replacement 
				 	parameters');

		//Using float replacement parameters
		$this->assertEquals( 'This is my 2.5 message post', 
					$format->formatMsg($floatArgs), 
				 	'Error parsing message string using float replacement 
				 	parameters');

	}

	/**
	* Parsing message string using two array replacement parameter
	*/
	function test_pattern2() {

		$pattern		= 'This is my {0} message post for week: {1}';
		$charArgs	= array(0 => 'second', 1 => 'twenty four');
		$format = new MessageFormat($pattern);

		//Using character replacement parameters
		$this->assertEquals( 'This is my second message post for week: twenty four', 
					$format->formatMsg($charArgs), 
				 	'Error parsing message string using character replacement 
				 	parameters');

	}

	/**
	* Parsing message string using three array replacement parameter
	*/
	function test_pattern3() {

		$pattern		= 'This is my {0} message post for week: {1} of year: {2}';
		$charArgs	= array(0 => 'third', 1 => 'fifty three', 2 => '2010');
		$format = new MessageFormat($pattern);

		//Using character replacement parameters
		$this->assertEquals( 
					'This is my third message post for week: fifty three of year: 2010', 
					$format->formatMsg($charArgs), 
				 	'Error parsing message string using character replacement 
				 	parameters');

	}

	/**
	* Parsing message string using four array replacement parameter
	*/
	function test_pattern4() {

		//--- J. R. R. Tolkien 
		$message1	= 'Perilous to us all are the devices of an art '.
							'deeper than we possess ourselves.';
		//--- Shakespere
		$message2	= 'How far that little candle throws his beams! '.
							'So shines a good deed in a weary world.';

		$pattern1	= '{0} to us all are the devices of {1} '.
							'deeper {2} we possess {3}.';
		$pattern2	= 'How far that {0} candle throws {1} beams! '.
							'So shines a {2} deed in a weary world{3}';

		$charArgs1	= array('Perilous', 'an art', 'than', 'ourselves');
		$charArgs2	= array('little', 'his', 'good', '.');
		
		
		$format = new MessageFormat($pattern1);
		//Using character replacement parameters
		$this->assertEquals($message1, $format->formatMsg($charArgs1), 
				 	'Error parsing message string using character replacement 
				 	parameters');
		$format = new MessageFormat($pattern2);
		//Using character replacement parameters
		$this->assertEquals($message2, $format->formatMsg($charArgs2), 
				 	'Error parsing message string using character replacement 
				 	parameters');

	}

	/**
	* Parsing message string using one string replacement parameter
	*/
	function test_pattern5() {
	
		//--- Plato 427-347 B.C.
		$message1	= 'And from the deep-ploughed furrow of his heart '.
							'Reaps harvest rich of goodly purpose.';
		$pattern1	= 'And from the deep-{0} furrow of his heart '.
							'Reaps harvest rich of goodly purpose.';
	
		$format = new MessageFormat($pattern1);
		//Using character replacement parameters
		$argsArray = '';
		$this->assertEquals($message1, $format->formatMsg($argsArray, 'ploughed'), 
				 	'Pattern 5: parsing message string using one string character 
				 			replacement parameter');

	}

	/**
	* Parsing message string using two string replacement parameters
	*/
	function test_pattern6() {
	
		//--- Plato 427-347 B.C.
		$message1	= 'Then would that which does no evil cause any evil?';
		$pattern1	= 'Then {0} that which does no {1} cause any evil?';
	
		$format = new MessageFormat($pattern1);
		//Using character replacement parameters
		$argsArray = '';
		$this->assertEquals($message1, $format->formatMsg($argsArray, 'would', 'evil'), 
				 	'Pattern 6: parsing message string using two string character 
				 			replacement parameters');

	}

	/**
	* Parsing message string using three string replacement parameters
	*/
	function test_pattern7() {
	
		//--- Plato 427-347 B.C.
		$message1	= "Supress thine anger's powers,".
							'Good friend, and hear why I refrained,';
		$pattern1	= 'Supress thine {0} powers,'.
							'Good {1}, and hear {2} I refrained,';
	
		$format = new MessageFormat($pattern1);
		//Using character replacement parameters
		$argsArray = '';
		$this->assertEquals($message1, 
								$format->formatMsg($argsArray, "anger's", 'friend', 'why'), 
				 	'Pattern 7: parsing message string using three string character 
				 			replacement parameters');

	}
	
	/**
	* Parsing message string using four string replacement parameters
	*/
	function test_pattern8() {
	
		//--- Plato 427-347 B.C.
		$message1	= "The gods' own offspring ".
							"Near kin to Zeus, who high on Ida's mount ".
							'To Zeus, their father, feed the altar flame, '.
							'And still within their veins runs blood devine.';
							
		$pattern1	= "The {0} own offspring ".
							"Near kin to {1} who high on Ida's mount ".
							'To Zeus, their father, feed the altar {2} '.
							'And still within {3} veins runs blood devine.';
	
		$format = new MessageFormat($pattern1);
		//Using character replacement parameters
		$argsArray = '';
		$this->assertEquals($message1, 
						$format->formatMsg($argsArray, "gods'", 'Zeus,', 'flame,', 'their'), 
				 	'Pattern 8: parsing message string using four string character 
				 			replacement parameters');

	}
	
}
?>