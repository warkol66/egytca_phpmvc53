<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/PropertyMessageResourcesTestCase.php,v 1.7 2006/02/22 08:33:03 who Exp $
* $Revision: 1.7 $
* $Date: 2006/02/22 08:33:03 $
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
* <p>PropertyMessageResourcesTestCase
*
* @author John C. Wildenauer
* @version $Revision: 1.7 $
*/
class PropertyMessageResourcesTestCase extends TestCase {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param name String - Name of the test case
	*/
	function PropertyMessageResourcesTestCase($name) {

		parent::TestCase($name);	// build the base class

	}


	// ----- Default AppServer Locale Test Methods -------------------------- //

 	/**
	* Test the default AppServer Locale. (LocalStringsTest.properties)
	* (No users Locale settings)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Four (max) replacement parameters
	* Test 4: Message key not found in any property file.
	*/
	function test_defaultLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// return something like "???message.hello_world???"
									// if we cannot find a message match in any of the 
									// properties files
		$locale = NULL;		// no user locale supplied
		$defaultLocale = new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using no replacement parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					'Hello World', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');	

		// Test 2:
		// Using one replacement parameter
		$args = array('Cruel');	
		$key = 'message.hello_world2';
		$this->assertEquals(
					'Hello Cruel World', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with no replacement parameters');	

		// Test 3:
		// Using four (max) replacement parameters
		// Note: See MessageFormatTestCase for more comprehensive 
		// replacement parameter tests

		//--- J. R. R. Tolkien 
		$message	= 'Perilous to us all are the devices of an art '.
							'deeper than we possess ourselves.';
		$pattern	= '{0} to us all are the devices of {1} '.
							'deeper {2} we possess {3}.';

		$args	= array('Perilous', 'an art', 'than', 'ourselves');
		$key = 'message.tolkien';
		$this->assertEquals(
					$message, 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message string using four replacement parameters');

		// Test 4:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: check non-existant message key');
	}


	/**
	* Test the default AppServer language Locale.
	* (LocalStringsTest_ll.properties)
	* (No users Locale settings)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files
	* Test 4: Message key not found in any property file.
	*/
	function test_lang_defaultLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// "???message.hello_world???"
		$locale = NULL;		// no user locale supplied
		$defaultLocale = new Locale('ll'); // language default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using an appServer language file with no replacement parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');	

		// Test 2:
		// Using an appServer language file with one replacement parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');	

		// Test 3:
		// Note: This message key "message.base.hello" should not exist in the 
		//       specified properties file "LocalStringsTest_ll.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');	

		// Test 4:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: check non-existant message key');

	}


	/**
	* Test the default AppServer language_COUNTRY Locale. 
	* (LocalStringsTest_ll_CC.properties)
	* (No users Locale settings)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files (language file)
	* Test 4: Test fallback to less specific locale files (default base file)
	* Test 5: Message key not found in any property file.
	*/
	function test_country_defaultLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// "???message.hello_world???"
		$locale = NULL;		// no user locale supplied
		$defaultLocale = new Locale('ll_CC'); // language_COUNTRY default Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using an appServer language_COUNTRY file with no replacement parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');	

		// Test 2:
		// Using an appServer language_COUNTRY file with one replacement parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');	

		// Test 3:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified properties file "LocalStringsTest_ll_CC.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.lang.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');

		// Test 4:
		// Note: This message key "message.base.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC.properties"
		//       or "LocalStringsTest_ll.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: parsing message with no replacement parameters');

		// Test 5:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll_cc.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll_cc.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 5 error: check non-existant message key');	

	}


	/**
	* Test the default AppServer language_COUNTRY_VARIANT Locale. 
	* (LocalStringsTest_ll_CC_VV.properties)
	* (No users Locale settings)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files (country file)
	* Test 4: Test fallback to less specific locale files (language file)
	* Test 5: Test fallback to less specific locale files (default base file)
	* Test 6: Message key not found in any property file.
	*/
	function test_variant_defaultLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// "???message.hello_world???"
		$locale = NULL;		// no user locale supplied
		$defaultLocale = new Locale('ll_CC_VV'); // language_COUNTRY_VARIANT
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using an appServer language_COUNTRY_VARIANT file with no replacement 
		// parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC_VV.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');

		// Test 2:
		// Using an appServer language_COUNTRY_VARIANT file with one replacement
		// parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll_CC_VV.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');

		// Test 3:
		// Note: This message key "message.lang.country.hello" should not exist in
		//       the specified properties file "LocalStringsTest_ll_CC_VV.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.lang.country.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');

		// Test 4:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC_VV.properties"
		//       or "LocalStringsTest_ll_CC.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.lang.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: parsing message with no replacement parameters');

		// Test 5:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC_VV.properties"
		//       or "LocalStringsTest_ll_CC.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 5 error: parsing message with no replacement parameters');

		// Test 6:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll_cc_vv.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll_cc_vv.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 6 error: check non-existant message key');	

	}



// ----- Users Prefered Locale Test Methods -------------------------------- //

 	/**
	* Test the users prefered Locale. (LocalStringsTest_ll.properties)
	* (LocalStringsTest_ll.properties)
	* (No users Locale settings)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files
	* Test 4: Message key not found in any property file.
	*/
	function test_lang_userLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// "???message.hello_world???"
		$locale = new Locale('ll'); // user prefered locale language
		$defaultLocale = new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using an appServer language file with no replacement parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');	

		// Test 2:
		// Using an appServer language file with one replacement parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');	

		// Test 3:
		// Note: This message key "message.base.hello" should not exist in the 
		//       specified properties file "LocalStringsTest_ll.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');	

		// Test 4:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: check non-existant message key');

	}


	/**
	* Test the users prefered Locale. (LocalStringsTest_ii_CC.properties)
	* (LocalStringsTest_ll_CC.properties)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files (language file)
	* Test 4: Test fallback to less specific locale files (default base file)
	* Test 5: Message key not found in any property file.
	*/
	function test_country_userLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;				// "???message.hello_world???"
		$locale = new Locale('ll_CC'); // user prefered locale language/Country
		$defaultLocale = new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// Using an appServer language_COUNTRY file with no replacement parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');	

		// Test 2:
		// Using an appServer language_COUNTRY file with one replacement parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');	

		// Test 3:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified properties file "LocalStringsTest_ll_CC.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.lang.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');

		// Test 4:
		// Note: This message key "message.base.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC.properties"
		//       or "LocalStringsTest_ll.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: parsing message with no replacement parameters');

		// Test 5:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll_cc.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll_cc.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 5 error: check non-existant message key');	

	}


	/**
	* Test the users prefered language_COUNTRY_VARIANT Locale.
	* (LocalStringsTest_ll_CC_VV.properties)
	* Test 1: No replacement parameters
	* Test 2: One replacement parameter
	* Test 3: Test fallback to less specific locale files (country file)
	* Test 4: Test fallback to less specific locale files (language file)
	* Test 5: Test fallback to less specific locale files (default base file)
	* Test 6: Message key not found in any property file.
	*/
	function test_variant_userLocale() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;				// "???message.hello_world???"
		$locale = new Locale('ll_CC_VV');	// no user locale supplied
		$defaultLocale = new Locale();// language_COUNTRY_VARIANT
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale); 

		// Test 1:
		// Using an appServer language_COUNTRY_VARIANT file with no replacement 
		// parameters
		$args = NULL;		
		$key = 'message.hello_world1';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC_VV.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: parsing message with no replacement parameters');

		// Test 2:
		// Using an appServer language_COUNTRY_VARIANT file with one replacement
		// parameter
		$args = array('Cruel');		
		$key = 'message.hello_world2';
		$this->assertEquals(
					"Hello Cruel World (in 'LocalStringsTest_ll_CC_VV.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: parsing message with one replacement parameter');

		// Test 3:
		// Note: This message key "message.lang.country.hello" should not exist in
		//       the specified properties file "LocalStringsTest_ll_CC_VV.properties",
		//       so we can ensure that the PropertyMessageResources class
		//       will search less specific property files for this
		//       message key.
		$args = NULL;		
		$key = 'message.lang.country.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll_CC.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: parsing message with no replacement parameters');

		// Test 4:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC_VV.properties"
		//       or "LocalStringsTest_ll_CC.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.lang.hello';
		$this->assertEquals(
					"Hello World (in 'LocalStringsTest_ll.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 4 error: parsing message with no replacement parameters');

		// Test 5:
		// Note: This message key "message.lang.hello" should not exist in the 
		//       specified property files "LocalStringsTest_ll_CC_VV.properties"
		//       or "LocalStringsTest_ll_CC.properties", so we can ensure that the
		//       PropertyMessageResources class will search less specific property
		//       files for this message key.
		// Note: This will also test MessageFormat caching, as the above
		//       test has an identical message/locale key
		$args = NULL;		
		$key = 'message.base.hello';
		$this->assertEquals(
					"Hello (in 'LocalStringsTest.properties')", 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 5 error: parsing message with no replacement parameters');

		// Test 6:
		// Message key not found in any property file.
		// If we set $returnNull = False, we should get something like
		// "???ll_cc_vv.message.base.hello???" returned.
		$args = NULL;		
		$key = 'message.non-existant_message_key';
		$this->assertEquals(
					'???ll_cc_vv.message.non-existant_message_key???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 6 error: check non-existant message key');	

	}


// ----- Check Message Format Caching -------------------------------------- //

	/**
	* Test message caching (two identical messages !!!!!!!)
	* Test 1: Not really a test, just a reminder to check the MessageFormat
	*         caching
	*/
	function test_messageCaching() {

		// Common setup variables
		$config = 'LocalStringsTest';	// base name of the properties file
		$returnNull = False;	// "???message.hello_world???"
		$locale = NULL;		// no user locale supplied
		$defaultLocale = new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		$args = NULL;		
		$key = 'message.hello_world1';

		// First call should cache this message string in a MessageFormat object
		$pmr->getMessage($locale, $key, $args);

		// Second call should reuse the cached MessageFormat object
		// See: MessageResources->getMessage(...)
		$pmr->getMessage($locale, $key, $args);	

	}


 	/**
	* Test (ignore) comments in property files as per the java.util.Properties.load() 
	* documentation.
	* Test 1: A valid comment line
	* Test 2: A comment line using the "#" character
	* Test 3: A comment line using the "!" character
	*/
	function test_propertyComments() {

		// Common setup variables
		$config = 'LocalStringsTestComments';		// properties file with comments
		$returnNull = False;
		$locale = NULL;						// no user locale supplied
		$defaultLocale = new Locale();	// default appServer Locale
		$factory = NULL;						// MessageResources factory
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// This is a valid prorerty message line
		$args = NULL;		
		$key = 'message.comment1'; // message.comment1 = A valid comment line
		$this->assertEquals(
					'A valid comment line', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: A valid comment line');

		// Test 2:
		// A comment line using the "#" character
		$args = NULL;		
		$key = '#message.comment2'; //#message.comment2 = A comment line using the # character
		// Note: This should fail, Eg. no message found ('???#message.comment2???')
		$this->assertEquals(
					'???#message.comment2???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 2 error: A comment line using the "#" character');	

		// Test 3:
		// A comment line using the "!" character
		$args = NULL;		
		$key = '!message.comment3'; //!message.comment3 = A comment line using the ! character
		// Note: This should fail, Eg. no message found ('???!message.comment3???')
		$this->assertEquals(
					'???!message.comment3???', 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 3 error: A comment line using the "!" character');	

	}


 	/**
	* Test loading a property file from a domain directory path.
	* For example: 'com.phpmvc.MyAppResources' => "com/phpmvc/MyAppResources.properties".
	* Note: The "." characters will be transformed to "/"
	* 
	* Note:<br>
	* If the property file path contains any slash characters, we assume this 
	*   is a fully qualified domain name path and leave the path as-is.
	*   Eg: './phpmvc.com\MyAppResources'<br>
	* If the property file path does not contains any slash characters, we 
	*   assume this is a Java/Struts type domain name scheme and replace all
	*   '.' characters with '/' characters.
	*   Eg: 'com.phpmvc.MyAppResources'
	* 
	*/
	function test_LoadPropertyFilePath() {

		// Common setup variables
		$config = 'com.phpmvc.MyAppResources';	// properties file
		$returnNull = False;
		$locale = NULL;						// no user locale supplied
		$defaultLocale = new Locale();	// default appServer Locale
		$factory = NULL;						// MessageResources factory
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// This is a valid prorerty message line
		$args = NULL;	
		$key = 'app.mystring'; // app.mystring=This is a property message string ...
		$message = 'This is a property message string ("com/phpmvc/MyAppResources.properties")';

		$this->assertEquals(
					$message, 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: Test loading a property file from a domain directory path.');	
	}


 	/**
	* Test loading a property file from a FQDN domain directory path.
	* For example: 'phpmvc.com/MyAppResources' => "phpmvc.com/MyAppResources.properties".
	* Note: The "." characters will NOT be transformed to "/"
	* 
	*/
	function test_LoadPropertyFileFQDNPath() {

		// Common setup variables
		$config = './phpmvc.com\MyAppResources';	// properties file
		$returnNull = False;
		$locale = NULL;						// no user locale supplied
		$defaultLocale = new Locale();	// default appServer Locale
		$factory = NULL;						// MessageResources factory
		$pmr = new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);

		// Test 1:
		// This is a valid prorerty message line
		$args = NULL;	
		$key = 'app.mystring'; // app.mystring=This is a property message string ...

		$message = 'This is a property message string ("phpmvc.com/MyAppResources.properties")';

		$this->assertEquals(
					$message, 
					$pmr->getMessage($locale, $key, $args), 
				 	'Test 1 error: Test loading a property file from a FQDN domain directory path.');
	}

}
?>