<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/PlugInsTestCase.php,v 1.3 2006/02/22 08:31:51 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:31:51 $
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
* <p>Test cases for PlugIns handling.
*
*
* @author John C. Wildenauer

* @version $Revision: 1.3 $
*/
class PlugInsTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Simple test xml document used in the tests.
	* @access protected static
	*/
	var $xmlFile = 'phpmvc-config-plugins.xml';

	/**
	* The digester instance we will be processing.
	* @access protected
	*/
	var $digester = NULL;	// Digester

	/**
	* XML parser case folding - False leaves xml element case unchanged
	* Default is True (uppercase xml elements)
	* @type Boolean
	*/
	var $parserCaseFolding = False;

	/**
	* ActionDispatcher
	* @type ActionDispatcher
	*/
	var $actionDispatcher = 'ActionDispatcher';

	/**
	* Server Request
	* @type HttpRequest
	*/
	var $request = NULL;

	/**
	* Server response
	* @type HttpResponse
	*/
	var $response = NULL;

	/**
	* Request path ('stdLogon')
	* @type String
	*/
	var $doPath = '';

	/**
	* RequestProcessor
	* @type RequestProcessor
	*/
	var $processor = NULL;

	/**
	* Action Server
	* @type ActionServer
	*/
	var $actionServer = NULL;

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param name String - Name of the test case
	*/
	function ActionsTestCase($name) {

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
		// (Struts class RulesBase)
		$this->digester->setRulesManager(new RulesManager());

		// Setup the application specific ActionDispatcher (Java RequestDispatcher)
		$actionDispatcher = $this->actionDispatcher;

		// Startup configuration information for an php.MVC Web app
		$appServerConfig	= new AppServerConfig;
		$appServerContext	= new AppServerContext;
		$appServerContext->setInitParameter('ACTION_DISPATCHER', $actionDispatcher);
		$appServerConfig->setAppServerContext($appServerContext);

		// Setup the php.MVC Web application controller
		$actionServer =& new ActionServer;
		// Initialise the php.MVC Web application ActionServer->init(...)
		$actionServer->appServerConfig = $appServerConfig;

		// Load Application Configuration
		$digester =& $this->digester;
		$digester->setValidating(False);
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, $this->parserCaseFolding);
		$digester->addRuleSet(new ConfigRuleSet());
		// Push the root config object onto the top of the Digester stack
		$appConfig = new ApplicationConfig('', $this);// root app config object
		$digester->push($appConfig);
		$appConfig	= $this->digester->parse($this->xmlFile);

		// Setup HTTP Request and add request attributes
		$request = new HttpRequestBase;
		$request->setAttribute(Action::getKey('APPLICATION_KEY'), $appConfig);
		$request->setRequestURI($_SERVER['PHP_SELF']);
		$this->request =& $request;
		$this->response =& new HttpResponseBase; // Setup HTTP Response

		///// ActionServer
		// In ActionServet->doPost()
		$request->setMethod('POST');
		// In ActionServet->process(..)
		// Select the sub-application to which the request belongs
		$appContext = $appServerConfig->getAppServerContext();
		$reqUtils = new RequestUtils;
		$reqUtils->selectApplication($request, $appContext);

		// Get the RequestProcessor
		$processor = new RequestProcessor;
		$processor->init($actionServer, $appConfig);
		$this->processor		= $processor;
		$this->actionServer	=& $actionServer;
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
	* Test PlugIn drivers and classes.
	*
	* This test simulates loading two or more PlugIn classes simultaneously.
	*
	*/
	function test_PlugIn() {

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testForwardAction" ...>
		$doPath = 'testPlugInOne';
		$request->setAttribute('ACTION_DO_PATH', $doPath);

		$actionServer =& $this->actionServer;
		$actionServer->setConfigPath($this->xmlFile);

		$actionServer->init($actionServer->appServerConfig);

		$plugInKey = 'TEST_PLUGIN_A';
		$plugInA = $actionServer->getPlugIn($plugInKey);
		$this->assertEquals('Class-A-1001', $plugInA->getPropA1(),
										'Error: PlugIn->getPropA1()');
		$this->assertEquals('Class-A-1002', $plugInA->getPropA2(),
										'Error: PlugIn->getPropA2()');

		$plugInKey = 'TEST_PLUGIN_B';
		$plugInB = $actionServer->getPlugIn($plugInKey);
		$this->assertEquals('Class-B-2001', $plugInB->getPropB1(),
										'Error: PlugIn->getPropB1()');
		$this->assertEquals('Class-B-2002', $plugInB->getPropB2(),
										'Error: PlugIn->getPropB2()');

	}

}
?>