<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/actions/LookupDispatchAction.php,v 1.6 2006/02/22 08:17:08 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 08:17:08 $
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
*  <p>
*  An Abstract <strong>Action</strong> that dispatches to the subclass 
*   mapped perform method. This is useful in cases where an HTML form has
*   multiple submit buttons with the same name. The button name is specified
*   by the <code>parameter</code> property of the corresponding ActionMapping.
*  To configure the use of this action in your <code>struts-config.xml</code>
*   file, create an entry like this:</p>
*   <pre>
*   &lt;action path="/test"
*           type="org.example.MyAction"
*           name="MyForm"
*          scope="request"
*          input="/test.jsp"
*      parameter="action"/&gt;
*   </pre> <p>
*
*  which will use the value of the request parameter named "action" to locate
*  the corresponding key in ApplicationResources. For example, you might have
*  the following ApplicationResources.properties:</p> <pre>
*    button.add=Add Record
*    button.delete=Delete Record
*  </pre><p>
*
*  And your PHP Form would have the following format for submit buttons:</p> 
*   <pre>
*   ***** CHECK THIS *****
*   // Using OOH-Forms Form class - without JavaScript validation.
*   $oohform->start('', 'POST', $doActionPath, $target, $formName);
*
*      <!-- Form contents -->
*
*      $oohform->show_element('submit'	, $label_addItem); 
*      $oohform->show_element('submit'	, $label_DelItem);
*      $oohform->show_element('submit'	, $label_clearCart);			
*   	
*   $oohform->finish(); // Finish form element 				
*
*   </pre> <p>
*
*  Your subclass must implement both getKeyMethodMap and the
*  methods defined in the map. An example of such implementations are:</p>
* <pre>
*  function getKeyMethodMap($mapping, $form, $request) {
*      $map = array();
*      $map["button.add"]    = "addItem";
*      $map["button.delete"] = "deleteItem";
*      return $map;
*  }
*
*  function add($mapping, $form, $request, $response) {
*      // do add
*      return $mapping->findForward("success"); // ActionForward
*  }
*
*  function delete($mapping, $form, $request, $response) {
*      // do delete
*      return $mapping->findForward("success"); // ActionForward
*  }
*  <p>
*
*  <strong>Notes</strong> - If duplicate values exist for the keys returned by
*  getKeys, only the first one found will be returned. If no corresponding key
*  is found then an exception will be thrown.
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Erik Hatcher (original Jakarta Struts class)
* @version $Revision: 1.6 $
* @public
*/
class LookupDispatchAction extends DispatchAction {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Reverse lookup map from resource value to resource key.
	* @private
	* @type array
	*/
	var $lookupMap = NULL;

	/**
	* Resource key to method name lookup
	* @private
	* @type array
	*/
	var $keyMethodMap = NULL;

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
	function LookupDispatchAction($config='') {

		// Build the base class first
		parent::DispatchAction($config);

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	*  Process the specified HTTP request, and create the corresponding HTTP
	*  response (or forward to another web component that will create it).
	*  Return an <code>ActionForward</code> instance describing where and how
	*  control should be forwarded, or <code>null</code> if the response has
	*  already been completed.
	*
	* Note: This method was named perform(). perform() is depreciated
	*        and replaced with execute()
	*
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResponseBase	The HTTP response we are creating
	* @param ActionForm			The optional ActionForm bean for this request, if any
	* @public
	* @returns ActionForward
	*/
	function execute($mapping, $form, &$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: LookupDispatchAction->execute(...)'.
									'['.__LINE__.']');
		}

		// Check the users Locale
		$locale = $request->getAttribute('locale'); 
		if(get_class($locale) != 'locale')
			$locale = NULL;

		// MessageResources message handling
		$msgRes = $this->messages;

		// Identify the request parameter containing the method name
		$parameter = $mapping->getParameter(); // String <action ... parameter="submit">
		if($parameter == NULL) {
			$args = array( $mapping->getPath() );
			$message = $msgRes->getMessage('', 'dispatch.handler', $args);
			return $message;
		}

		// Identify the string to lookup. Eg: 'Add Item'
		$name = $request->getParameter($parameter); // String
		if($name == NULL) {
			$args = array($mapping->getPath(), $parameter);
			$message = $msgRes->getMessage('', 'dispatch.parameter', $args);
			return $message;
		}

		// Build the reverse key lookup map from the resources.
		// Eg: ('Add Item' => 'button.add', 'Remove Item' => 'button.rem')
		if($this->lookupMap == NULL) {
			$this->lookupMap = array();
			// Call our derived class
			// Note: getKeyMethodMap() is a member of the derived class
			$keyMethodMap = $this->getKeyMethodMap(); // array()

			foreach($keyMethodMap as $key => $value) {
				// Something like
				//  'button.add' => 'addToCart'
				//  'button.rem' => 'subtractFromCart'	
				$text = NULL;
				// Eg: button.add => 'Add Item'

				// Patch by Michael Schmitz - http://sourceforge.net/users/mschmitz/
				$htmlenc = $msgRes->getMessage($locale, $key);
				// replace html entities by applicable characters
				$text = $this->unhtmlentities($htmlenc);

				// Add the text string ('Add Item') to the $this->lookupMap
				//  Eg: ('Add Item' => 'button.add', 'Remove Item' => 'button.rem')
				if( ($text != NULL) && (!array_key_exists($text, $this->lookupMap)) ) {
					$this->lookupMap[$text] = $key;
				}

				// And save the keyMethodMap
				$this->keyMethodMap = $keyMethodMap;

			}
		}

		// Now we can use the request parameter name to find the corresponding key
		// Eg: ('Add Item' => 'button.add')
		$key = $this->lookupMap[$name];

		// And now use the key to retrieve the actual method name to call
		$methodName = $this->keyMethodMap[$key]; // Eg: 'button.add => 'addToCart'

		// Change parent::dispatchMethod(...) to $this->dispatchMethod(...) as per
		// suggestion by kazaio
		return $this->dispatchMethod($mapping, $form, $request, 
												$response, $methodName);
	}


	/**
	*  Provides the mapping from resource key to method name
	*
	* @private
	* @returns array
	*/
	function getKeyMethodMap() {

		// Implement this method in your derived class.
		// See example above.

	}


	/**
	* Convert all HTML entities to their applicable characters.
	* Returns the string text with replaced html entities.
	*
	* <p>Note: This method provides the same functionality like
	* 'html_entity_decode()' (PHP 4 >= 4.3.0, PHP 5), but the
	* method 'unhtmlentities()' works even with prior versions of PHP.</p>
	* <p>Author: Michael Schmitz - http://sourceforge.net/users/mschmitz/</p>
	*
	* @see http://www.php.net/html_entity_decode
	* @param string	The text containing html entities
	* @private
	* @returns string
	*/
	function unhtmlentities($string)
	{
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

}
?>