<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/BeanPropertySetterRuleTestCase.php,v 1.5 2006/02/22 07:05:27 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 07:05:27 $
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
* <p> Test case for <code>BeanPropertySetterRule</code>.
* This contains tests for the main applications of the rule
* and two more general tests of digester functionality used by this rule.
*
* @author John C.Wildenauer (php.MVC port)<br>
*   Credit Apache Software Foundation (http://www.apache.org/), author unknown.
* @revision $Revision: 1.5 $

*/
class BeanPropertySetterRuleTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Simple test xml document used in the tests.
	*
	* @access protected static
	*/
	var $xmlFile = './simpleTest.xml';

	/**
	* The digester instance we will be processing.
	*
	* @access protected
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
	* @param name String - Name of the test case
	*/
	function BeanPropertySetterRuleTestCase($name) {

		parent::TestCase($name);	// build the base class

	}


	// ----- Overall Test Methods ------------------------------------------- //

	/**
	* Set up instance variables required by this test case.
	*
	* @access public
	* @return void
	*/
	function setUp() {

		$this->digester = new Digester();

	}


	/**
	* Tear down instance variables required by this test case.
	*
	* @access public
	* @return void
	*/
	function tearDown() {

		$this->digester = NULL;

	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* This is a general digester test but it fits into here pretty well.
	* This tests that the rule calling order is properly enforced.
	*
	* @access public
	* @return void
	*/
	function test_DigesterRuleCallOrder() {

		$callOrder = array(); // Stack of TestRule objects
		$digester =& $this->digester;	// ref to digester

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Setup the Rules Manager
		$digester->setRulesManager(new RulesManager());

		// Add first test rule
		$firstRule = new TestRule('first');
		$firstRule->setOrder($callOrder); // $callOrder is a callback ref
		$digester->addRule('root/alpha', $firstRule);

		// Add second test rule
		$secondRule = new TestRule('second');
		$secondRule->setOrder($callOrder);
		$digester->addRule('root/alpha', $secondRule);

		// Add third test rule
		$thirdRule = new TestRule('third');
		$thirdRule->setOrder($callOrder);
		$digester->addRule('root/alpha', $thirdRule);


		// Try
		// Digester returns NULL on failure
		$res = NULL;
		$res = $digester->parse($this->xmlFile);

		// Catch
		if($res == NULL) {
			#echo 'Exception prevented test execution: ...';
		}
		

		// We should have nine entries in our list of calls
		$this->assertEquals(count($callOrder), 9,
									"Nine calls should have been made.");


		// begin() should be called in the order added
		$this->assertEquals("first"	, $callOrder[0]->getIdentifier(),
									"First rule begin not called first.");

		$this->assertEquals("second"	, $callOrder[1]->getIdentifier(), 
									"Second rule begin not called second.");

		$this->assertEquals("third"	, $callOrder[2]->getIdentifier(), 
										"Third rule begin not called third.");


		// body text should be called in the order added
		$this->assertEquals("first"	, $callOrder[3]->getIdentifier(),
										"First rule body text not called first.");

		$this->assertEquals("second"	, $callOrder[4]->getIdentifier(),
										"Second rule body text not called second.");

		$this->assertEquals("third"	, $callOrder[5]->getIdentifier(),
										"Third rule body text not called third.");


		// end() should be called in reverse order
		$this->assertEquals("third"	, $callOrder[6]->getIdentifier(),
										"Third rule end not called first.");

		$this->assertEquals("second"	, $callOrder[7]->getIdentifier(),
										"Second rule end not called second.");

		$this->assertEquals("first"	, $callOrder[8]->getIdentifier(),
										"First rule end not called third.");
	}


	/**
	* This is a general digester test but it fits into here pretty well.
	* This tests that the body text stack is functioning correctly.
	*
	* @access public
	* @return void
	*/
	function test_DigesterBodyTextStack() {

		// Setup the Rules Manager
		$digester =& $this->digester;	// ref to digester

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Setup the Rules Manager
		$digester->setRulesManager(new RulesManager());


		// add test rule to catch body text
		$rootRule = new TestRule("root");
		// Save this element <root> body text in this rules $bodyText variable
		$digester->addRule("root", $rootRule);

		// add test rule to catch body text
		$alphaRule = new TestRule("root/alpha");
		$digester->addRule("root/alpha", $alphaRule);

		// add test rule to catch body text
		$betaRule = new TestRule("root/beta");
		$digester->addRule("root/beta", $betaRule);
		
		// add test rule to catch body text
		$gammaRule = new TestRule("root/gamma");
		$digester->addRule("root/gamma", $gammaRule);
	

		// try
		$res = False;
		$res = $digester->parse($this->xmlFile);

		// catch
		if(!$res) {
			#echo 'Exception prevented test execution: ...';
		}


		#print_r($digester->rulesMan->rules);
		
		// This is how we should get the body text
		// $this->assertEquals("ROOT BODY"	, $rootRule->getBodyText(),
		//							"Root body text not set correct.");

		// This is a hack to get the body text for the rules,
		// as keeping track of the rules by reference does not
		// seem work in PHP as it does to in Java
		$rootBodyText	= $digester->rulesMan->rules[0]->bodyText;
		$this->assertEquals("ROOT BODY"	, $rootBodyText,
									"Root body text not set correct.");

		$alphaBodyText	= $digester->rulesMan->rules[1]->bodyText;
		$this->assertEquals("ALPHA BODY"	, $alphaBodyText,
									"Alpha body text not set correct.");

		$betaBodyText	= $digester->rulesMan->rules[2]->bodyText;
		$this->assertEquals("BETA BODY"	, $betaBodyText,
									"Beta body text not set correct.");
																
		$gammaBodyText	= $digester->rulesMan->rules[3]->bodyText;
		$this->assertEquals("GAMMA BODY"	, $gammaBodyText,
									"Gamma body text not set correct.");

    }


	/**
	* Test that you can successfully set given attributes
	*
	* @access public
	* @return void
	*/
	function test_SetGivenProperty() {

		// Setup the Rules Manager
		$digester =& $this->digester;	// ref to digester
		
		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Setup the Rules Manager
		$digester->setRulesManager(new RulesManager());
        

		// going to be setting properties on a SimpleTestBean
		$digester->addObjectCreate("root", "SimpleTestBean");


		//////////
		// Setup some bean properties individually

		// root: we'll set properties from the <root ..> element attributes
		$digester->addRule("root", new SetPropertyRule('root_attrib1'));
		$digester->addRule("root", new SetPropertyRule('root_attrib2'));

		// alpha: we'll set properties from the <root/alpha> element attributes
		$digester->addRule("root/alpha", new SetPropertyRule('alpha_attrib1'));
		$digester->addRule("root/alpha", new SetPropertyRule('alpha_attrib2'));
        
		// beta: we'll set properties from the <root/beta> element attributes
		$digester->addRule("root/beta", new SetPropertyRule('beta_attrib1'));
		$digester->addRule("root/beta", new SetPropertyRule('beta_attrib2'));


		//////////
		// Setup some automatic bean properties

		// gamma: we'll set properties from the <root/beta> element attributes
		$digester->addRule("root/gamma", new SetPropertiesRule());
		


		// Try
		$bean = NULL;	// SimpleTestBean
		$bean = $digester->parse($this->xmlFile);
		
		// Catch
		if($bean == NULL) {
			echo 'Exception prevented test execution: ...';
		}


		//////////
		// Test some bean properties individually
		
		// root element attributes
		$this->assertEquals('xyz'	, $bean->getProperty('root_attrib1'),
        						'Property "root_attrib1" not set correctly');
		$this->assertEquals('dummy'	, $bean->getProperty('root_attrib2'),
        						'Property "root_attrib2" not set correctly');

		// alpha element attributes
		$this->assertEquals('my-alpha01'	, $bean->getProperty('alpha_attrib1'),
        						'Property "alpha_attrib1" not set correctly');
		$this->assertEquals('my-alpha02'	, $bean->getProperty('alpha_attrib2'),
        						'Property "alpha_attrib2" not set correctly');

		// beta element attributes
		$this->assertEquals('my-beta01'	, $bean->getProperty('beta_attrib1'),
        						'Property "beta_attrib1" not set correctly');
		$this->assertEquals('my-beta02'	, $bean->getProperty('beta_attrib2'),
        						'Property "beta_attrib2" not set correctly');


		//////////
		// Test some automatic bean properties
		$this->assertEquals('my-gamma01'	, $bean->getProperty('gamma_attrib1'),
        						'Property "beta_attrib1" not set correctly');
		$this->assertEquals('my-gamma02'	, $bean->getProperty('gamma_attrib2'),
        						'Property "beta_attrib2" not set correctly');

    }


	/**
	* Test that you can successfully set named key/value attributes
	*
	* @access public
	* @return void
	*/
	function test_SetGivenKeyValueProperty() {

		// Setup the Rules Manager
		$digester =& $this->digester;	// ref to digester
		
		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, 
												$this->parserCaseFolding);

		// Setup the Rules Manager
		$digester->setRulesManager(new RulesManager());

		// going to be setting properties on a SimpleTestBean
		$digester->addObjectCreate("root", "SimpleTestBean");


		//////////
		// Setup some bean properties individually

		// Named attribute key/value pairs
		//<star serverName="Darkstar">
		//	<set-property property="host" value="darkstar1"/>
		//	<set-property property="port" value="2010"/>
		//</star>
		$digester->addRule("root/star/set-property" , new SetPropertyRule('property', 'value'));

		// Try
		$bean = NULL;	// SimpleTestBean
		$bean = $digester->parse($this->xmlFile);
		
		// Catch
		if($bean == NULL) {
			echo 'Exception prevented test execution: ...';
		}


		//////////
		// Test some bean properties individually
		
		// root element attributes
		$this->assertEquals('darkstar1'	, $bean->getProperty('host'),
        						'Property "host" not set correctly');
		$this->assertEquals('2010'	, $bean->getProperty('port'),
        						'Property "port" not set correctly');

	}


	/**
	* Test that you can successfully automatically set properties.
	*
	* @access public
	* @return void
	*/
	function Xtest_AutomaticallySetProperties() {
	
		// ExtendedBaseRules
		// LATER DATE
		// See: "struts-digester/test/BeanPropertySetterRuleTestCase.java"
	}

}
?>