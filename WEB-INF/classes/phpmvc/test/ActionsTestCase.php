<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/ActionsTestCase.php,v 1.6 2006/02/22 06:57:56 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 06:57:56 $
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
* <p>Test cases for the actions classes.
*
* <p>ForwardAction.
* <p>DispatchAction.
* <p>LookupDispatchAction.
*
* @author John C. Wildenauer

* @version $Revision: 1.6 $
*/
class ActionsTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Simple test xml document used in the tests.
	* @access protected static
	*/
	var $xmlFile = 'phpmvc-config-test.xml';

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
	* <p>ForwardAction
	*
	* <p>A php.MVC framework class we can use when we don't require any
	* business logic processing. This class provides a generic execute() method.
	*
	* <p>This will forward control to the context-relative URI specified by the
	* 'parameter' attribute of the <action ... parameter = "myPage.php">.
	* <p>Program execution will terminate in RequestProcessor->process(...)
	*
	* <p>Note: A regular Action class would return a Forward object and
	* continue program execution.
	*/
	function test_ForwardAction() {

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testForwardAction" ...>
		$doPath = 'testForwardAction';
		$request->setAttribute('ACTION_DO_PATH', $doPath);

		///// RequestProcessor->process(..)
		// Mapping: As set in the phpmvc-config.xml (<action path = "testForwardAction" ...>)
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testForwardAction', $path,
										'Path: Opps, we got a path problem ... ');

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');

		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(	strtolower('TestForm'), strtolower(get_class($form)),
										'Form: Opps, we got a form problem ... ');

		// Create or acquire the Action instance to process this request
		// Note the use of the "ForwardAction" forwarding Action
		// provided by the php.MVC framework. Eg: we do not need any business logic.
		// <action  path = "stdLogon" type = "ForwardAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('ForwardAction'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');

		// Call the Action->execute(...) method [ForwardAction->execute(...)]
		// and verify the page contents
		$pageBuff = '';
		ob_start();
			$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
			$pageBuff = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('Hello MyPage' , trim($pageBuff),
					'Page Contents: Opps, we got a problem with the page contents ');

		// ForwardAction->execute(...) should call ActionDispatcher->forward(...)
		// to output the resource, something like 'myPage.php' and return NULL.
		// This will terminate program execution in RequestProcessor->process(...)
		// Note: A regular Action class would return a Forward object and
		// continue program execution.
		$this->assertEquals(	NULL , $forward,
										'Action: Opps, we got an ForwardAction problem ... ');

	}


	/**
	* <p>DispatchAction
	* <p>This class maps a form button value to the required method name.
	* Eg: submit button [addToCart] maps to TestDispatchAction->addToCart(...)
	* <p>To use descriptive button names ([Add Item]), see class
	* LookupDispatchAction
	*/
	function test_DispatchAction() {

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testDispatchAction" ...>
		$doPath = 'testDispatchAction';
		$request->setAttribute('ACTION_DO_PATH', $doPath);

		///// RequestProcessor->process(..)
		// Mapping: As set in the phpmvc-config.xml (<action path = "testDispatchAction" ...>)
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testDispatchAction', $path,
										'Path: Opps, we got a path problem ... ');

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');

		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(	strtolower('TestForm'), strtolower(get_class($form)),
										'Form: Opps, we got a form problem ... ');

		// Create or acquire the Action instance to process this request
		// Note the use of the "DispatchAction" action class provided by
		// the php.MVC framework.
		// <action  path = "testDispatchAction" type = "DispatchAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('TestDispatchAction'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');


		// Test an Add operation
		$request->addParameter('submit', 'addToCart'); // the submit button value
		$cart['hammer.small'] = 12;
		$request->setAttribute('cart', $cart);
		$request->setAttribute('itemID', 'hammer.small');
		$request->setAttribute('qnty'  , 10);

		// Call TestDispatchAction->addToCart(...) via DispatchAction->execute(...)
		$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
		// A regular Action class returns a Forward object and continue program
		// execution. Something like:
		// ForwardConfig[name=success, path=welcomePage.php, redirect=]
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals(	strtolower('success') , $forward->getName(),
										'Action: Opps, we got an ForwardAction problem ... ');
		$cart = $request->getAttribute('cart');
		$this->assertEquals(	22 , $cart['hammer.small'],
										'Action: Opps, we got a Cart addition problem ... ');


		// Test a Subtraction operation
		$request->addParameter('submit', 'subtractFromCart'); // the submit button value
		$request->setAttribute('itemID', 'hammer.small');
		$request->setAttribute('qnty'  , 5);

		$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals(	strtolower('success') , $forward->getName(),
										'Action: Opps, we got an ForwardAction problem ... ');
		$cart = $request->getAttribute('cart');
		$this->assertEquals(	17 , $cart['hammer.small'],
										'Action: Opps, we got a Cart subtraction problem ... ');

	}


	/**
	* <p>LookupDispatchAction
	* <p>Extends DispatchAction to allow descriptive names on the submit buttons.
	* This class maps the descriptive name to the required method name.
	* Eg: submit button [Add Item] maps to TestLookupDispatchAction->addToCart(...)
	*/
	function test_LookupDispatchAction() {

		$processor	=& $this->processor;
		$request		=& $this->request;
		$response	=& $this->response;

		// Manually set the request path. <action path = "testDispatchAction" ...>
		$doPath = 'testLookupDispatchAction';
		$request->setAttribute('ACTION_DO_PATH', $doPath);
		#$request->addParameter('method', 'addToCart');

		///// RequestProcessor->process(..)
		// Mapping: As set in the phpmvc-config.xml (<action path = "testDispatchAction" ...>)
		$path = $processor->processPath($request, $response); // String (eg: '/login')
		$this->assertEquals('testLookupDispatchAction', $path,
										'Path: Opps, we got a path problem ... ');

		// Identify the mapping for this request (ActionConfig configuration object)
		$mapping = $processor->processMapping($request, $response, $path);
		$this->assertEquals(	strtolower('ActionConfig'), strtolower(get_class($mapping)),
										'Mapping: Opps, we got a mapping problem ... ');

		// Retrieve the ActionForm bean associated with this mapping (ActionForm)
		// <action ... name="testForm" ... >
		// references <form-bean name="testForm" type="TestForm"/>
		$form = $processor->processActionForm($request, $response, $mapping);
		$this->assertEquals(	strtolower('TestForm'), strtolower(get_class($form)),
										'Form: Opps, we got a form problem ... ');

		// Create or acquire the Action instance to process this request
		// <action  path = "testLookupDispatchAction" type = "LookupDispatchAction" ... >
		$action = $processor->processActionCreate($request, $response, $mapping);
		$this->assertEquals(	strtolower('TestLookupDispatchAction'), strtolower(get_class($action)),
										'Action: Opps, we got an Action problem ... ');


		// Test an Add operation
		$request->addParameter('submit', 'Add Item'); // the submit button value
		$cart['hammer.small'] = 12;
		$request->setAttribute('cart', $cart);
		$request->setAttribute('itemID', 'hammer.small');
		$request->setAttribute('qnty'  , 10);

		// Call TestDispatchAction->addToCart(...) via DispatchAction->execute(...)
		$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
		// A regular Action class returns a Forward object and continue program
		// execution. Something like:
		// ForwardConfig[name=success, path=welcomePage.php, redirect=]
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals(	strtolower('success') , $forward->getName(),
										'Action: Opps, we got an ForwardAction problem ... ');

		$cart = $request->getAttribute('cart');
		$this->assertEquals(	22 , $cart['hammer.small'],
										'Action: Opps, we got a Cart addition problem ... ');


		// Test a Subtraction operation
		$request->addParameter('submit', 'Remove Item'); // the submit button value
		$request->setAttribute('itemID', 'hammer.small');
		$request->setAttribute('qnty'  , 5);

		$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals(	strtolower('success') , $forward->getName(),
										'Action: Opps, we got an ForwardAction problem ... ');
		$cart = $request->getAttribute('cart');
		$this->assertEquals(	17 , $cart['hammer.small'],
										'Action: Opps, we got a Cart subtraction problem ... ');


		// Test handling special characters (German umlauts)
		$request->addParameter('submit', 'Ändern'); // the submit button value ("Modify")
		$forward = $processor->processActionPerform($request, $response,
															$action, $form, $mapping);
		$this->assertEquals(	strtolower('ForwardConfig') , strtolower(get_class($forward)),
										'Action: Opps, we got an ForwardAction problem ... ');
		$this->assertEquals(	strtolower('success') , $forward->getName(),
										'Action: Opps, we got an ForwardAction problem ... ');

	}
}
?>