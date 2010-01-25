<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/RuleTestCase.php,v 1.4 2006/02/22 08:47:52 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:47:52 $
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
* <p>Test Case for the Digester class.  These tests perform parsing of
* XML documents to exercise the built-in rules.</p>
*
* @author John C. Wildenauer (php port)<br>
*  Credits:<br>
*    Craig R. McClanahan (Jakata Struts)<br>
*    Janek Bogucki (Jakata Struts)
* @version $Revision: 1.4 $
*/
class RuleTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Simple test xml document used in the tests.
	*
	* @private
	*/
	var $xmlFile = '';

	/**
	* The digester instance we will be processing.
	*
	* @private
	*/
	var $digester = NULL;	// Digester

	/**
	* XML parser case folding - False leaves xml element case unchanged
	* Default is True (uppercase xml elements)
	* @type Boolean
	*/
	var $parserCaseFolding = False;


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param string	Name of the test case
	*/
	function RuleTestCase($name) {
	
		parent::TestCase($name);	// build the base class
	
	}


	// ----- Overall Test Methods ------------------------------------------- //

	/**
	* Set up instance variables required by this test case.
	*
	* @public
	* @returns void
	*/
	function setUp() {
	
		$this->digester = new Digester();
		// (Strute class RulesBase)
		$this->digester->setRulesManager(new RulesManager());
	
	}


	/**
	* Tear down instance variables required by this test case.
	*
	* @public
	* @returns void
	*/
	function tearDown() {

		$this->digester = NULL;

	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* Test object creation (and associated property setting) with nothing on
	* the stack, which should cause an appropriate Employee object to be
	* returned.
	*/
	function test_ObjectCreate1() {

		$digester		=& $this->digester;
		$this->xmlFile	= 'Test1.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Configure the digester as required
		$digester->addObjectCreate("employee", "Employee");
		$digester->addSetProperties("employee");


		// Parse our test input.
		$root	= NULL;	// Object
		// Try
		$root = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester threw IOException: " + t);


		$this->assert(NULL != $root, "Digester returned an object");
        
		// string get_class ( object obj)
		$this->assert( strtolower(get_class($root)) == strtolower('Employee'), 
								"Digester returned an Employee");

		$employee = $root;
		$this->assertEquals("First Name", $employee->getFirstName(),
								"First name is correct");
      
		$this->assertEquals("Last Name", $employee->getLastName(), 
								"Last name is correct");

	}


	/**
	* Test object creation (and associated property setting) with nothing on
	* the stack, which should cause an appropriate Employee object to be
	* returned.  The processing rules will process the nested Address elements
	* as well, but will not attempt to add them to the Employee.
	*/
	function test_ObjectCreate2() {

		$digester		=& $this->digester;
		$this->xmlFile	= 'Test1.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);


		// Configure the digester as required
		$digester->addObjectCreate("employee", "Employee");
		$digester->addSetProperties("employee");
		$digester->addObjectCreate("employee/address", "Address");
		$digester->addSetProperties("employee/address");


		// Parse our test input.
		$root	= NULL;	// Object
		// Try
		$root = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester threw IOException: " + t);

		$this->assert(NULL != $root, "Digester returned an object");
		$this->assert( strtolower(get_class($root))  == strtolower('Employee'), 
								"Digester returned an Employee");

		$employee = $root;
		$this->assertEquals("First Name", $employee->getFirstName(), 
								"First name is correct");
              
		$this->assertEquals("Last Name", $employee->getLastName(), 
								"Last name is correct");

    }


	/**
	* Test object creation (and associated property setting) with nothing on
	* the stack, which should cause an appropriate Employee object to be
	* returned.  The processing rules will process the nested Address elements
	* as well, and will add them to the owning Employee.
	*/
	function test_ObjectCreate3() {

		$digester		= $this->digester;
		$this->xmlFile	= 'Test1.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);


		// Configure the digester as required
		$digester->addObjectCreate("employee", "Employee");
		$digester->addSetProperties("employee");
		$digester->addObjectCreate("employee/address", "Address");
		$digester->addSetProperties("employee/address");
		$digester->addSetNext("employee/address", "addAddress");


		// Parse our test input.
		$root	= NULL;	// Object
		// Try
		$root = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester threw IOException: " + t);

		$this->validateObjectCreate3($root);


		// Parse the same input again !!!!!!!!!!!!!!!!!!
		// Try
		# Error:  on line !!!!!
		#$root = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester threw IOException: " + t);

		#$this->validateObjectCreate3($root);

	}


	/**
	* Same as testObjectCreate1(), except use individual call method rules
	* to set the properties of the Employee.
	*/
	function Xtest_ObjectCreate4() {

		// Later date. addCallMethod(...) not yet implemented
		// $digester->addObjectCreate("employee", "Employee");
		// $digester->addCallMethod("employee", "setFirstName", 1);
		// $digester->addCallParam("employee", 0, "firstName");
		
		// ..........
		
		// ref RuleTestCase.java

	}


	/**
	* Same as testObjectCreate1(), except use individual call method rules
	* to set the properties of the Employee. Bean data are defined using 
	* elements instead of attributes. The purpose is to test CallMethod with
	* a paramCount=0 (ie the body of the element is the argument of the 
	* method).
	*/
	function Xtest_ObjectCreate5() {

		// Later date. addCallMethod(...) not yet implemented
		// Configure the digester as required
		// $digester->addObjectCreate("employee", "Employee");
		// $digester->addCallMethod("employee/firstName", "setFirstName", 0);
		// $digester->addCallMethod("employee/lastName", "setLastName", 0);
		
		// ..........
		
		// ref RuleTestCase.java

	}


	/**
	* It should be possible to parse the same input twice, and get trees
	* of objects that are isomorphic but not be identical object instances.
	*/
	function Xtest_RepeatedParse() {

		$digester		= $this->digester;
		$this->xmlFile	= 'Test1.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Configure the digester as required
		$digester->addObjectCreate("employee", "Employee");
		$digester->addSetProperties("employee");
		$digester->addObjectCreate("employee/address", "Address");
		$digester->addSetProperties("employee/address");
		$digester->addSetNext("employee/address", "addAddress");


		// Parse our test input the first time
		$root1	= NULL;	// Object
		// Try
		$root1 = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester #1 threw Exception: " + t);

		$this->validateObjectCreate3($root1);


		// Parse our test input the second time
		$root2	= NULL;	// Object
		// Try
		# Error:  on line !!!!!
		#$root2 = $digester->parse($this->xmlFile);
		//Catch
		#fail("Digester #1 threw Exception: " + t);

		#$this->validateObjectCreate3($root2);


		// Make sure that it was a different root
		#$this->assert($root1 != $root2, "Different tree instances were returned");

	}


	/**
	* Test object creation (and associated property setting) with nothing on
	* the stack, which should cause an appropriate Employee object to be
	* returned.  The processing rules will process the nested Address elements
	* as well, but will not attempt to add them to the Employee.
	*/
	function test_RuleSet1() {

		$digester		= $this->digester;
		$this->xmlFile	= 'Test1.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Configure the digester as required
		$rs = new TestRuleSet();
		$digester->addRuleSet($rs);


		// Parse our test input.
		$root = NULL; // Object
		// Try
		$root = $digester->parse($this->xmlFile);
		// Catch
		#fail("Digester threw IOException: " + t);


		$this->assert(NULL != $root, "Digester returned an object");
		$this->assert( strtolower(get_class($root)) == strtolower('Employee'), 
									"Digester returned an Employee");

		$employee = $root; // Employee
		$this->assertEquals("First Name", $employee->getFirstName(), 
									"First name is correct");
		
		$this->assertEquals("Last Name", $employee->getLastName(), 
									"Last name is correct");
		
		$this->assert(NULL != $employee->getAddress("home"), 
									"Can retrieve home address");
		
		$this->assert(NULL != $employee->getAddress("office"), 
									"Can retrieve office address");

	}


	/**
	* Same as <code>testRuleSet1</code> except using a single namespace.
	*/
	function Xtest_RuleSet2() {

		// Later date. NamespaceAware not yet implemented

		#$digester		= $this->digester;
		#$this->xmlFile	= 'Test1.xml';
		#
		#// XML parser case folding - False leaves case unchanged
		#$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
		#										$this->parserCaseFolding);
		#
		#// Configure the digester as required
		#$digester->setNamespaceAware(True);
		#$rs = new TestRuleSet(NULL, "http://jakarta.apache.org/digester/Foo");
		#$digester->addRuleSet($rs);

	}


	/**
	* Same as <code>testRuleSet2</code> except using a namespace
	* for employee that we should recognize, and a namespace for
	* address that we should skip.
	*/
	function Xtest_RuleSet3() {

		// Later date. NamespaceAware not yet implemented
	
		// ...........

	}


	/**
	* Test the two argument version of the SetTopRule rule. This test is
	* based on testObjectCreate3 and should result in the same tree of
	* objects.  Instead of using the SetNextRule rule which results in
	* a method invocation on the (top-1) (parent) object with the top
	* object (child) as an argument, this test uses the SetTopRule rule
	* which results in a method invocation on the top object (child)
	* with the top-1 (parent) object as an argument.  The three argument
	* form is tested in <code>testSetTopRule2</code>.
	*/
	function Xtest_SetTopRule1() {

		// Later date. SetTopRule not yet implemented
		// ...........

	}


	/**
	* Same as <code>testSetTopRule1</code> except using the three argument
	* form of the SetTopRule rule.
	*/
	function Xtest_SetTopRule2() {

		// Later date. SetTopRule not yet implemented
		// ...........

	}


	/**
	* Test rule addition - this boils down to making sure that 
	* digester is set properly on rule addition.
	*
	* JCW
	* Note: setting a reference to the Digester instance does
	* not work as it should. See Digester->addRule()
	* Note:  Throws error: Nesting level too deep - recursive dependency?
	*/
	function Xtest_AddRule() {

		$digester = new Digester();
		$rule =  new TestRule("Test");
		$digester->addRule("/root", $rule);		
		$this->assertEquals($digester, $rule->getDigester(), 
									"Digester is not properly on rule addition.");

	}
    

	/**
	* Test SetNext
	* Extension of {@link RulesBase} for complex schema
	*/
	function Xtest_SetNext() {

		// Later date. ExtendedBaseRules not yet implemented
		// ...........        

	}
    
  
	/**
	* Test SetTop
	* Extension of {@link RulesBase} for complex schema
	*/ 
	function Xtest_SetTop() {

		// Later date. ExtendedBaseRules not yet implemented
		// ...........    

	}


	/**
	* Test method calls with the CallMethodRule rule. It should be possible
	* to call any accessible method of the object on the top of the stack,
	* even methods with no arguments.
	*/
	function Xtest_CallMethod() {

		// Later date. CallMethodRule not yet implemented
		// ...........    

	}

	/**
	* Test method calls with the CallMethodRule rule. It should be possible
	* to call any accessible method of the object on the top of the stack,
	* even methods with no arguments.
	*/
	function Xtest_CallMethod2() {

		// Later date. CallMethodRule not yet implemented
		// ...........    

	}


	/**
	* Test SetCustomProperties
	*
	*/
	function test_SetCustomProperties() {

		$digester		= $this->digester;
		$digester->clear();
		$this->xmlFile	= 'Test7.xml';

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Build the root object first
		$digester->addObjectCreate("toplevel", 'Employee');

		// Now add some composite objects 
		$digester->addObjectCreate("toplevel/one", 'Address');
		// And the foreign key binding
		//  [next-to-top-of-stack]                      [top-of-stack]		
		//  [Employee->addAddress(oAddress)]1<---------M[Address]
		$digester->addSetNext("toplevel/one", "addAddress");
		
		$digester->addObjectCreate("toplevel/two", 'Address');
		$digester->addSetNext("toplevel/two", "addAddress");

		$digester->addObjectCreate("toplevel/three", 'Address');
		$digester->addSetNext("toplevel/three", "addAddress");
		
		$digester->addObjectCreate("toplevel/four", 'Address');
		$digester->addSetNext("toplevel/four", "addAddress");

		
		// (1)
		// Now we can populate some properties in the matching objects.
		// Eg: The xml parser will find and run all rules matching "toplevel/one" 
		// as it enters an xml element ( <toplevel><one .... /> )
		
		// All attributes are mapped as usual using exact property name matching.
		// Eg: 	< .. street="My Street" .. >	=> 	Address->setStreet(...)
		// etc ...
		$digester->addSetProperties("toplevel/one");
		
		// (2)
		// Maps the xml "toplevel/two" element attribute "alt-city" to the "city" 
		// property and the "alt-state" attribute to the "state" property, etc. 
		// All other attributes are mapped as usual using exact name matching.
		// Eg: 	< .. alt-street="My Street" .. >	=> 	Address->setStreet(...)
		//			< .. alt-city="My City" .. >		=> 	Address->setCity(...)
		//			< .. alt-state="My State" .. >	=> 	Address->setState(...)
		$digester->addSetProperties("toplevel/two", 
						array("alt-street", "alt-city", "alt-state"),// attributeNames
						array("street"    , "city"    , "state") );	// propertyNames

		// (3)
		// The xml "toplevel/three" element attribute "aCity" attribute is mapped 
		// to the "city" property and the  "state" is not mapped
		// All other attributes are mapped as usual using exact name matching.
		// Eg: 	< .. street="My Street" .. >     => 	Address->setStreet(...)
		//			< .. aCity="My City" .. >        => 	Address->setCity(...)
		//			< .. state="My State" .. >	      => 	Ignore this property
		$digester->addSetProperties("toplevel/three", 
											array("aCity", "state"), 
											array("city"          ) );

		// (4)
		// Maps the xml "toplevel/four" element attribute "alt-city" to  
		// the "city" property. 
		// All other attributes are mapped as usual using exact property
		// name matching.
		$digester->addSetProperties("toplevel/four", 
							array("alt-city"), 
							array("city"    ) );		
	
		
		// Parse the xml configuration
		$root = array();
		$root = $digester->parse($this->xmlFile);
		$addresses = $root->addresses;


		$this->assertEquals(4, count($addresses), "Wrong array size");
   
		// Note that the array is in popped order (rather than pushed)

		// (1)
		$obj = $addresses[0];
		$this->assert( strtolower(get_class($obj)) == strtolower('Address'),
										"(1) Should be an Address ");
		$addressOne = $obj;
		$this->assertEquals("New Street", $addressOne->getStreet(), 
										"(1) Street attribute");
		$this->assertEquals("Las Vegas", $addressOne->getCity(), 
										"(1) City attribute");
		$this->assertEquals("Nevada", $addressOne->getState(), 
										"(1) State attribute");

		// (2)
		$obj = $addresses[1];
		$this->assert( strtolower(get_class($obj)) == strtolower('Address'), 
        								"(2) Should be an Address ");
		$addressTwo = $obj;
		$this->assertEquals("Old Street", $addressTwo->getStreet(), 
        								"(2) Street attribute");
		$this->assertEquals("Portland", $addressTwo->getCity(), 
        								"(2) City attribute");
		$this->assertEquals("Oregon", $addressTwo->getState(), 
        								"(2) State attribute");

		// (3)
		$obj = $addresses[2];
		$this->assert( strtolower(get_class($obj)) == strtolower('Address'),
										"(3) Should be an Address ");
		$addressThree = $obj;
		$this->assertEquals("4th Street", $addressThree->getStreet(), 
										"(3) Street attribute");
		$this->assertEquals("Dayton", $addressThree->getCity(), 
										"(3) City attribute");
		$this->assertEquals("Down Under" , $addressThree->getState(), 
										"(3) State attribute [Not configures, just set
										per the Address class default]");

		// (4)
		$obj = $addresses[3];
		$this->assert( strtolower(get_class($obj)) == strtolower('Address'), 
										"(4) Should be an Address ");
		$addressFour = $obj;
		$this->assertEquals("6th Street", $addressFour->getStreet(), 
										"(4) Street attribute");
		$this->assertEquals("Cleveland", $addressFour->getCity(), 
										"(4) City attribute");
		$this->assertEquals("Ohio", $addressFour->getState(), 
										"(4) State attribute");
    
	}
    

	// ----- Utility Support Methods ---------------------------------------- //

	/**
	* Validate the assertions for ObjectCreateRule3.
	*
	* @param object Object. Root object returned by <code>digester.parse()</code>
	* @access protected
	*/
	function validateObjectCreate3($root) {


		// Validate the retrieved Employee
		$this->assert(NULL != $root , "Digester returned an object");
		$this->assert( strtolower(get_class($root)) == strtolower("Employee"), 
									"Digester returned an Employee");
		$employee = $root;
		$this->assertEquals("First Name"	, $employee->getFirstName(),
									"First name is correct");
		$this->assertEquals("Last Name"  , $employee->getLastName(), 
									"Last name is correct");

		// Validate the corresponding "home" Address
		$home = $employee->getAddress("home");
		$this->assert(NULL != $home, "Retrieved home address");
		$this->assertEquals("Home Street"	, $home->getStreet()	, "Mill street");
		$this->assertEquals("Home City"		, $home->getCity()	, "Home city");
		$this->assertEquals("VIC"				, $home->getState()	, "Home state");
		$this->assertEquals("VicZip"			, $home->getZipCode(), "Home zip");


		// Validate the corresponding "office" Address
		$office = $employee->getAddress("office");
		$this->assert(NULL != $office, "Retrieved office address");
		$this->assertEquals("Office Street"	, $office->getStreet(), "Office street");
		$this->assertEquals("Office City"	, $office->getCity()	, "Office city");
		$this->assertEquals("QLD"		, $office->getState(), "Office state");
		$this->assertEquals("QldZip"	, $office->getZipCode(), "Office zip");

	}
    
}
?>