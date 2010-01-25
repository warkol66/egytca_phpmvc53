<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/actions/DispatchAction.php,v 1.7 2006/02/22 07:25:51 who Exp $
* $Revision: 1.7 $
* $Date: 2006/02/22 07:25:51 $
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
* An Abstract <strong>Action</strong> that dispatches to a public
* method that is named by the request parameter whose name is specified
* by the <code>parameter</code> property of the corresponding
* ActionMapping.  This Action is useful for developers who prefer to
* combine many similar actions into a single Action class, in order to
* simplify their application design.</p>
*
* <p>To configure the use of this action in your
* <code>phpmvc-config.xml</code> file, create an entry like this:</p>
*
* <code>
*   &lt;action path="/saveSubscription"
*           type="DispatchAction"
*           name="subscriptionForm"
*          scope="request"
*          input="/subscription.tpl"
*      parameter="method"/&gt;
* </code>
*
* <p>which will use the value of the request parameter named "method"
* to pick the appropriate "perform" method, which must have the same
* signature (other than method name) of the standard Action->execute()
* method.  For example, you might have the following three methods in the
* same action:</p>
* <ul>
* <li>function delete($mapping, $form, $request, $response)</li>
* <li>function insert($mapping, $form, $request, $response)</li>
* <li>function update($mapping, $form, $request, $response)</li>
* </ul>
* <p>and call one of the methods with a URL like this:</p>
* <code>
*   http://www.myhost.com/phpmvc/Main.php?do=saveSubscription&method=update
* </code>
*
* <p><strong>NOTE</strong> - All of the other mapping characteristics of
* this action must be shared by the various handlers.  This places some
* constraints over what types of handlers may reasonably be packaged into
* the same <code>DispatchAction</code> subclass.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Niall Pemberton <niall.pemberton@btInternet.com> 
*   (original Jakarta Struts class)<br>
*  Craig R. McClanahan (original Jakarta Struts class)<br>
*  Ted Husted (original Jakarta Struts class).
* @version $Revision: 1.7 $
* @public
*/
class DispatchAction extends Action {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The message resources for this package.
	* @private
	* @type MessageResources
	*/
	var $messages = NULL;

	/**
	* The set of Method objects we have introspected for this class,
	* keyed by method name. This collection is populated as different
	* methods are called, so that introspection needs to occur only
	* once per method name.
	* @private
	* @type array
	*/
	var $methods = array();

	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL;


	// ----- Constructors --------------------------------------------------- //

	/**
	* @param	string	 Base name of the properties file. Eg: 'LocalStringsMyApp'
	*							[Optional - default is 'LocalStrings']
	*/
	function DispatchAction($config='LocalStrings') {

		//////////
		// Setup the MessageResources handling

		// Call the factory method - Later
		#$this->messages = 
		#			MessageResources::getMessageResources("LocalStrings");

		// Call the PropertyMessageResources class directly for now
		#$config = 'LocalStringsTestActions';	// base name of the properties file
		$returnNull = False;	// return something like "???message.hello_world???"
									// if we cannot find a message match in any of the 
									// properties files
		$defaultLocale = new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr = NULL;
		$this->messages = new PropertyMessageResources($factory, $config, $returnNull);
		$this->messages->setDefaultLocale($defaultLocale);

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isTraceEnabled'	, False);
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isErrorEnabled'	, False);

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Dispatch to the specified method.
	*
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param ActionForm			The optional ActionForm bean for this request
	*									(if any)
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResponseBase	The HTTP response we are creating
	* @param string				The method name to call 
	* @private
	* @returns ActionForward
	*/
	function dispatchMethod($mapping, $form, &$request, &$response, $name) {

		$trace = $this->log->getLog('isTraceEnabled');
		$debug = $this->log->getLog('isDebugEnabled');
		$error = $this->log->getLog('isErrorEnabled');

		if($trace) {
			$this->log->trace('Start: DispatchAction->dispatchMethod(...)'.
									'['.__LINE__.']');
		}

		// Check the users Locale
		$locale = $request->getAttribute('locale'); 
		if(get_class($locale) != 'locale')
			$locale = NULL;

		// Identify the method object to be dispatched to
		$method = NULL; // Method

		// Try 
		$method = $this->getMethod($name);

		// Catch
		if($method == NULL) {
			// NoSuchMethodException
			// getMessage($locale, $key, $args=NULL)
			// dispatch.method=Action[{0}] does not contain method named {1}
			$args = array($mapping->getPath(), $name);
			$message = $this->messages->getMessage($locale, 'dispatch.method', $args);									
														
			if($error) {
				$this->log->error('DispatchAction->dispatchMethod(...)'.
									'['.__LINE__.'] ' . $message);
			}

			return NULL; // dodgy method call ... dump the request
		}

		$forward = NULL; // ActionForward
		// Try to call the method and receive an ActionForward (or an error code)
		// Eg: CartDispatchAction->addToCart(...)
		// Where: class CartDispatchAction extends DispatchAction
		// Note: call_user_func( ...) does NOT preserve the object references. Eg: (&$request, ...)
		$forward = $this->$name($mapping, $form, $request, $response);

      // Catch processing errors
      if($forward == NULL) {
			$args = array($mapping->getPath(), $name);
			$message = $this->messages->getMessage($locale, "dispatch.error", $args);
			if($error) {
				$this->log->error('DispatchAction->dispatchMethod(...)'.
									'['.__LINE__.'] ' . $message);
			}

			return NULL;
		}

        // Return the returned ActionForward instance
        return $forward;
    }


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the specified HTTP request, and create the corresponding HTTP
	* response (or forward to another web component that will create it).
	* Return an <code>ActionForward</code> instance describing where and how
	* control should be forwarded, or <code>null</code> if the response has
	* already been completed.
	*
	* Note: This method was named perform(). perform() is depreciated
	*        and replaced with execute()
	*
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param ActionForm			The optional ActionForm bean for this request (if any)
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResponseBase	The HTTP response we are creating
	* @public
	* @returns ActionForward
	*/
	function execute($mapping, $form, &$request, &$response) {

		$trace = $this->log->getLog('isTraceEnabled');
		$debug = $this->log->getLog('isDebugEnabled');
		$error = $this->log->getLog('isErrorEnabled');

		if($trace) {
			$this->log->trace('Start: DispatchAction->execute(...)'.
									'['.__LINE__.']');
		}

		// Check the users Locale
		$locale = $request->getAttribute('locale'); 
		if(get_class($locale) != 'locale')
			$locale = NULL;

		// Identify the request parameter containing the method name
		$parameter = $mapping->getParameter(); // String
		if($parameter == NULL) {
			$args = array($mapping->getPath());
			$message = $this->messages->getMessage($locale, "dispatch.handler", $args);
			if($error) {
				$this->log->error('DispatchAction->execute(...)'.
									'['.__LINE__.'] ' . $message);
			}
			return NULL;
		}

		// Identify the method name to be dispatched to.
		$name = $request->getParameter($parameter); // String
		if($name == NULL) {
			// Bad Request
			$args = array($mapping->getPath(), $parameter);
			$message = $this->messages->getMessage(
								$locale, "dispatch.parameter", $args);
			if($error) {
				$this->log->error('DispatchAction->execute(...)'.
									'['.__LINE__.'] ' . $message);
			}

			return NULL;

		}

		// Invoke the named method, and return the result
		return $this->dispatchMethod($mapping, $form, $request, $response, $name);

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Introspect the current class to identify a method of the specified
	* name that accepts the same parameter types as the <code>execute()</code>
	* method does. ($mapping, $form, $request, $response)
	*
	* <p>Returns a method name or NULL if no matching Method found.
	*
	* <p>Note: Do we really need to save the method to $methods, like 
	*    Struts does!!. Perhaps we may call multiple methods per request ?
	*
	* @param string	Name of the method to be introspected (eg: "addItem")
	* @private
	* @returns string
	*/
	function getMethod($name) {

		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: DispatchAction->getMethod(...)'.
									'['.__LINE__.']');
		}

		$method = NULL;
		if( array_key_exists($name, $this->methods) )
			$method = $this->methods[$name]; // Method

		if($method == NULL) {	
			// Retrieve the class methods for this class (and derived class)
			$class_methods = get_class_methods(get_class($this));

			foreach($class_methods as $method_name) {	
    			// Have to lowercase both method names to maintain php4 compatibility
    			if(strtolower($name) == strtolower($method_name)) {
    				$this->methods[$name] = $name;
    				$method = $name;
    				break;
    			}
			}
		}

		return $method;
	}
}
?>