<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/ActionChainsTestCase.php,v 1.6 2006/02/22 06:35:08 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 06:35:08 $
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
* <p>Test cases for the Action chaining behaviour.
*
*
* @author John C. Wildenauer

* @version $Revision: 1.6 $
*/
class ActionChainsTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Simple test xml document used in the tests.
	* @access protected static
	*/
	var $xmlFile = 'phpmvc-config-action-chains.xml';

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

		$this->digester =& new Digester();
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
		$actionServer = new ActionServer;
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
		$this->response = new HttpResponseBase; // Setup HTTP Response

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
		$this->processor = $processor;

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
	* Test regular Action processing. 
	* <p>o-Start Action1 -> Page1 -> o-End
	* 
	*/
	function test_RegularAction() { 

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testRegularAction" ...>
		$doPath = 'testRegularAction';
		$request->setAttribute('ACTION_DO_PATH', $doPath);

		///// RequestProcessor->process(..)
		// Retrieve the path from the request
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testRegularAction', $path,
										'Path: Opps, we got a path problem ... ');

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');

		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// Note: No Form used for this test
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(NULL, $form,
										'Form: No Form used for this test ... ');

		// Create or acquire the Action instance to process this request
		// <action  path = "testRegularAction" type = "ActionChainsRegularAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('ActionChainsRegularAction'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');

		// Call ActionChainsRegularAction->execute(...)
		$forward = $processor->processActionPerform($request, $response,
																	$action, $form, $mapping);
		// A regular Action class returns a Forward object and continues program
		// execution. Something like:
		// ForwardConfig[name=success, path=welcomePage.php, redirect=]
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals('regularActionPath', $forward->getName(),
										'Action: Opps, we got an ForwardAction name problem ... ');

		// Check if we have another Action to process
		$shutdown = NULL;
		$shutdown = $processor->processActionChain($request, $forward);
		$this->assertEquals( True, $shutdown,
										'Shutdown: No further Actions to process ... ');

		// Call the static resource (page)
		// Note: this sends output to the browser, but we intercept it here
		$pageBuff = '';
		ob_start();
			$processor->processActionForward($request, $response, $forward);
			$pageBuff = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('Action Chains Test Page One', $pageBuff,
										'Output: Should be "Action Chains Test Page One" ... ');

	}


	/**
	* Test Action to Action processing. 
	* <p>o-Start Action1 -> Action2 -> Page2 -> o-End
	* 
	*/
	function test_Action2Action() { 

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testAct2ActAction01" ...>
		$doPath = 'testAct2ActAction01';
		$request->setAttribute('ACTION_DO_PATH', $doPath);


		/////
		//  First Action in the Action chain

		///// RequestProcessor->process(..)
		// Retrieve the path from the request
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testAct2ActAction01', $path,
										'Path1: Opps, we got a path problem ... ');
										
		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');	
					
		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// Note: No Form used for this test
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(NULL, $form,
										'Form: No Form used for this test ... ');

		// Create or acquire the Action instance to process this request
		// <action  path = "testRegularAction" type = "ActionChainsRegularAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('Act2ActChainAction01'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');

		// Call ActionChainsRegularAction->execute(...)
		$forward = $processor->processActionPerform($request, $response,
																	$action, $form, $mapping);
		// A regular Action class returns a Forward object and continues program
		// execution. Something like:
		// ForwardConfig[name=success, path=welcomePage.php, redirect=]
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals('nextActionPath', $forward->getName(),
										'Action: Opps, we got an ForwardAction name problem ... ');

		// Check if we have another Action to process
		$shutdown = NULL;
		$shutdown = $processor->processActionChain($request, $forward);
		$this->assertEquals( False, $shutdown,
										'Do not shutdown: More Actions to process ... ');

		// No static resource (page) defined  for the first action
		// Note: this sends output to the browser, but we intercept it here
		$pageBuff = '';
		ob_start();
			$processor->processActionForward($request, $response, $forward);
			$pageBuff = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('', $pageBuff,
										'Output: Should be no output for the first Action ... ');

		/////
		//  Second Action in the Action chain

		///// RequestProcessor->process(..)
		// Retrieve the path from the request
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testAct2ActAction02', $path,
										'Path2: Opps, we got a path problem ... ');

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');	

		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// Note: No Form used for this test
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(NULL, $form,
										'Form: No Form used for this test ... ');

		// Create or acquire the Action instance to process this request
		// <action  path = "testRegularAction" type = "ActionChainsRegularAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('Act2ActChainAction02'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');

		// Call ActionChainsRegularAction->execute(...)
		$forward = $processor->processActionPerform($request, $response,
																	$action, $form, $mapping);
		// A regular Action class returns a Forward object and continues program
		// execution. Something like:
		// ForwardConfig[name=success, path=welcomePage.php, redirect=]
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals('lastActionPath', $forward->getName(),
										'Action: Opps, we got an ForwardAction name problem ... ');

		// Check if we have another Action to process
		$shutdown = NULL;
		$shutdown = $processor->processActionChain($request, $forward);
		$this->assertEquals( True, $shutdown,
										'Shutdown: No more Actions to process ... ');

		// No static resource (page) defined  for the first action
		// Note: this sends output to the browser, but we intercept it here
		$pageBuff = '';
		ob_start();
			$processor->processActionForward($request, $response, $forward);
			$pageBuff = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('Action Chains Test Page Two', $pageBuff,
										'Output: Should be "Action Chains Test Page Two" ... ');

	}


	/**
	* Test Action to Page to Action to Page processing. 
	* <p>o-Start Action1 -> Page1 -> Action2 -> Page2 -> o-End

	*/
	function Xtest_ActionChainAction() { 
		;
	}


	/**
	* Test for a dodgy ForwardConfig.
	*/
	function test_BadForwardConfig() { 

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testRegularAction" ...>
		$doPath = 'testRegularAction';
		$request->setAttribute('ACTION_DO_PATH', $doPath);

		///// RequestProcessor->process(..)
		// Retrieve the path from the request
		$path = $processor->processPath($request, $response); // String (eg: '/login')

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);

		// Create or acquire the Action instance to process this request
		// <action  path = "testRegularAction" type = "ActionChainsRegularAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);

		// Call ActionChainsRegularAction->execute(...)
		$forward = NULL; // simulate a dodgy ForwardConfig

		// Check if we have another Action to process
		$shutdown = NULL;
		$shutdown = $processor->processActionChain($request, $forward);
		$this->assertEquals( True, $shutdown,
										'Dodgy ForwardConfig: We should shutdown ... ');

	}

}