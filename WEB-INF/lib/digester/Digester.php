<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/Digester.php,v 1.8 2006/02/22 07:23:59 who Exp $
* $Revision: 1.8 $
* $Date: 2006/02/22 07:23:59 $
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
* <p>A <strong>Digester</strong> processes an XML input stream by matching a
* series of element nesting patterns to execute Rules that have been added
* prior to the start of parsing.  This package was inspired by the
* <code>Jakarta Struts</code> MVC framework.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig McClanahan (original Jakarta Struts Digester)<br>
*  Scott Sanders (original Jakarta Struts Digester)<br>
*  Jean-Francois Arcand (original Jakarta Struts Digester)<br>
*  See: jakarta.apache.org
* @version: $Revision: 1.8 $
*/
class Digester extends ExpatParser {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Loging class
	* @type Log
	*/
	var $log			= NULL;		// ref: Most common logging calls

	/**
	* SAX event related logging calls
	* @type Log
	*/
	var $saxLog		= NULL;		// ref: SAX event related logging calls

	/**
	* The RulesManager rules handler
	* @type RulesManager
	*/
	var $rulesMan	= NULL;		// ref: The RulesManager handler

	/**
	* The XML pattern match. Eg:  "xxx/yyy"
	* @type string
	*/
	var $match		= NULL;		//

	/**
	* The xml element body text
	* @type string
	*/
	var $bodyText	= NULL;		//

	/**
	* An array of body texts
	* @type array
	*/
	var $bodyTexts	= array();

	/**
	* Has this Digester been configured yet?
	* @type boolean
	*/
	var $configured= NULL;		// Has this Digester been configured yet?

	/**
	* The root stack element (last popped)
	* @type object
	*/
	var $root		= NULL;		// ref: The root stack element (last popped)

	/**
	* The object stack being constructed
	* @type array
	*/
	var $stack		= array();	// The object stack being constructed

	/**
	* Namespace, not currently supported
	* @type boolean
	*/
	var $namespaceAware	= False; 	// namespace not cirrently supported

	/**
	* Are we using a validating parser? (not currently supported)
	* @type boolean
	*/
	var $validating	= False;		// not currently supported


	/**
	* Return the validating parser flag.
	*
	* @public
	* @returns boolean
	*/
	function getValidating() {

		return $this->validating;

	}

	/**
	* Set the validating parser flag.  This must be called before
	* <code>parse()</code> is called the first time.
	*
	* @param boolean The new validating parser flag.
	* @public
	* @returns void
	*/
	function setValidating($validating) {

		$this->validating = $validating;

	}


	/**
	* Set a generic property.
	*
	* @param string The property to set
	* @param object The actual property object to set
	* @public
	* @returns void
	*/
	function setProperty($property, &$ref) {
		$this->$property = &$ref;
	}

	/**
	* Get a generic property.
	*
	* @param string The property to retrieve
	* @public
	* @returns object
	*/
	function getProperty($property) {
		return $this->$property;
	}


	// ----- Constructor ------------------------------------------------- //

	/**
	* Digester constructor.
	*
	* @param string The XML file name to process
	* @public
	* @returns void
	*/
	function Digester($file=NULL) {

		// Call the ExpatParser constructor first
		parent::ExpatParser($file);

		// Setup the loggers
		// Note: Client classes may override these logging settings when
		//       seting up and initialising a Digester instance .
		//       Eg: ActionServer::initConfigDigester()
		// Note: Be sure to set the Logs to False if logging is not required.
		//       Just commenting out the setLog(... ) causes unpredictable
		//       behavour in the unit test harness
		$this->log		= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);

		$this->saxLog	= new PhpMVC_Log();
		$this->saxLog->setLog('isDebugEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Parse the content of the specified file using this Digester.  Returns
	* the root element from the object stack (if any).
	*
	* @param string File name, or string containing the XML data to be parsed
	* @public
	* @returns object
	*/
	function parse($file) {

		// We need the xml parser to operate in this (digester) class
		xml_set_object($this->parser, $this);

		$this->configure();		// do not configure more than once
		parent::parse($file);	// ExpatParser->parse()

		if(isset($this->root)) {
			return $this->root;
		} else {
			return NULL;
		}

   }


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Provide a hook for lazy configuration of this <code>Digester</code>
	* instance.  The default implementation does nothing, but subclasses
	* can override as needed.
	*
	* @privare
	* @returns void
	*/
	function configure() {

		// Do not configure more than once
		if($this->configured) {
			return;
		}

		// Set the configuration flag to avoid repeating
		$this->configured = True;

	}


	// ----- ContentHandler Methods ----------------------------------------- //

	/**
	* Process notification of the start of an XML element being reached.
	*
	* @param string The xml_parser object
	* @param string Name of the current xml element being processed
	* @param array An array of attributes (name=>value) for the
	*  current xml element being processed.
	*  Eg: <action .. name1="val1" name2="val2" ..>
	* @returns void
	*/
	function startElementHandler($parser, $elemName, $attrList) {

  		$debug = $this->log->getLog('isDebugEnabled');

  		// Save the body text accumulated for the surronding element
  		array_push($this->bodyTexts, $this->bodyText);

  		if($debug) {
  			$this->log->debug("Start Element handler - Pushing body text '".
  										$this->bodyText."'");
  		}

		$this->bodyText = ''; // and clear the body text for next element


  		// Compute the current matching rule
  		$sb = $this->match;	// save the current match to a string buffer

  		if(strlen($this->match) > 0) {
  			$sb .= '/';			// "xxx/yyy[/]"
  		}

  		$sb .= $elemName;		// "xxx-xxx/yyy-yyy/[elem-name]"

  		$this->match = $sb;	// save the match string

  		if($debug) {
  			$this->log->debug(" New match '".$this->match."'");
  		}

  		// Call the "begin()" events for all relevant rules
  		$bodyText= NULL;		// String
  		$rulesManager	= $this->getRulesManager();

  		// List of rule objects. {array([0]=>oRuleA, [1]=>oRuleB, ...}
  		//   that match this element
		$rulesSet	= NULL;  // list of rule objects. {oRuleA, oRuleB, ...}
		$namespace	= NULL;	// handle namespace later !!
  		$rulesSet = $rulesManager->match($namespace, $this->match);

		// Found rule(s) matching this pattern
		if( ($rulesSet != NULL) && (count($rulesSet) > 0) ) {

			foreach($rulesSet as $oRule) {

				if($debug) {
					$this->log->debug("  Fire begin() for ".get_class($oRule).
												" Pattern:".$this->match); // !!
				}

				// Try
				$res = NULL;
				// Note: passing a digester instance (reference) to Rule->begin()
				$res = $oRule->begin($attrList, $this);// TO-DO return $res errors

				// Catch (Exception)
				if($res['exception'] != NULL) {
					$error = $res['exception'];
					$this->log->error("Begin event threw exception", $error);
					// Throw createSAXException($error);
				}
				// Catch (Error) {
				if($res['error'] != NULL) {
					$error = $res['error'];
					$this->log->error("Begin event threw error", $error);
					// Throw $error;
				}

			}	// foreach(...)

		} else {

			if($debug) {
				$this->log->debug("  No rules found matching '" .
										$this->match . "'.");
			}

		}


	}


	/**
	* Process notification of the end of an XML element being reached.
	*
	* @param object The xml_parser object
	* @param string Name of the current xml element being processed
	* @public
	* @returns void
	*/
	function endElementHandler($parser, $elemName) {

		$debug	= $this->log->getLog('isDebugEnabled');	// boolean
		$saxDebug= $this->saxLog->getLog('isDebugEnabled');

		if($debug) {

			// SAX processing errors
			if($saxDebug) {
				$this->saxLog->debug('endElement(' . $elemName . ')');
			}

			// Common processing errors
			$this->log->debug("  match='" . $this->match . "'");
			$this->log->debug("  bodyText='" . $this->bodyText . "'");
		}

		// Fire "body" events for all relevant rules
      $rulesSet	= NULL;  	// list of rule objects. {oRuleA, oRuleB, ...}
      $namespace	= NULL;		// handle namespace later !!
      $bodyText	= NULL;		// String
      $rulesManager = $this->getRulesManager();
      // Note: cannot get RulesManager to return rule objects as
      //   references. So we receive a copy of each rule object
		$rulesSet =& $rulesManager->match($namespace, $this->match);

		if( ($rulesSet != NULL) && (count($rulesSet) > 0) ) {

			$bodyText = $this->bodyText;

			foreach($rulesSet as $oRule) {

				if($debug) {
					$this->log->debug( "  Fire body() for " . get_class($oRule) );
				}

				// Try
				$res = NULL;
				$res = $oRule->body($bodyText, $this);	// TO-DO return $res errors

				// Catch (Exception)
				if($res['exception'] != NULL) {
					$error = $res['exception'];
					$this->log->error("Body event threw exception", $error);
					// Throw createSAXException($error);
				}
				// Catch (Error) {
				if($res['error'] != NULL) {
					$error = $res['error'];
					$this->log->error("Body event threw error", $error);
					// Throw $error;
				}

			}	// foreach(...)

		} else {

			if($debug) {
				$this->log->debug("  No rules found matching '".$this->match."'.");
			}
		}

		// Recover the body text from the surrounding element
		$this->bodyText = array_pop($this->bodyTexts);	// string
		if($debug) {
			$this->log->debug("  Popping body text '" . $this->bodyText . "'");
		}

		// Fire "end" events for all relevant rules in reverse order
		if($rulesSet != NULL) {

         $rulesSetRev = array_reverse($rulesSet, True);	// preserve keys

			foreach($rulesSetRev as $oRule) {

				if($debug) {
					$this->log->debug("  Fire end() for " . get_class($oRule) );
				}

				// Try
				$res = NULL;
				// Note: passing a digester instance (reference) to Rule->end()
				$res = $oRule->end($this);	// TO-DO return $res errors

				// Catch (Exception)
				if($res['exception'] != NULL) {
					$error = $res['exception'];
					$this->log->error("End event threw exception", $error);
					// Throw createSAXException($error);
				}
				// Catch (Error) {
				if($res['error'] != NULL) {
					$error = $res['error'];
					$this->log->error("End event threw error", $error);
					// Throw $error;
				}

			}	// foreach(...)

		}

		// Recover the previous match expression
		$slashPos = strrpos($this->match, '/');// last occurence of '/'
		if($slashPos === False) {					// note: three equal signs
			$this->match = '';						// not found
		} else {
			// <[xxx-xxx/yyy-yyy]/zzz-zzz>
			$this->match = substr($this->match, 0, $slashPos);
		}

	}


	/**
	* Process notification of character data received from the body of
	* an XML element.
	*
	* @param object The xml_parser object
	* @param string The characters from the XML element body
	* @returns void
	* @public
	*/
	function characterDataHandler($parser, $data) {

		$data = trim($data);

		$saxDebug = $this->saxLog->getLog('isDebugEnabled');
		if($saxDebug) {
			$this->saxLog->debug(' Body Element- characterDataHandler(' . $data. ')');
		}

		// Append these data characters at the end of the bodyText buffer
		$this->bodyText .= $data;
		#$this->bodyText = $data;

	}


	// ----- Rule Methods --------------------------------------------------- //

	/**
	* Set the <code>Rules Manager</code> implementation object containing our
	* rules collection and associated matching policy.
	*
	* @param RulesManager The new RulesManager implementation
	* @public
	* @returns void
	*/
	function setRulesManager(&$rulesManager) {

        $this->rulesMan =& $rulesManager;
        $this->rulesMan->setDigester($this);

    }


	/**
	* Return the <code>RulesManager</code> object containing our
	* rules collection and associated matching policy.  If none has been
	* established, a default implementation will be created and returned.
	*
	* @public
	* @returns RulesManager
	*/
	function &getRulesManager() {

		if($this->rulesMan == NULL) {
			$this->rulesMan = new RulesManager();
			$this->rulesMan->setDigester($this);
		}

		return $this->rulesMan;

	}


	/**
	* <p>Register a new Rule matching the specified pattern.
	* This method sets the <code>Digester</code> property on the rule.</p>
	*
	* @param string The element matching pattern
	* @param Rule The rule to be registered
	* @returns void
	*/
	function addRule($pattern, &$rule) {

		// Note: setting a reference to the Digester instance does
		//			not work as it should. Eg: In ObjectCreateRule-begin(..)
		//			operates on a copy of the digester->stack, so objects
		//			previously pushed onto the stack are not available
		//			(it is a copy of the digester->stack)
		#$rule->setDigester($this);	// recursive - disable to test

		// Setup RulesManager (if not yet setup)
		$oRulesMan	=& $this->getRulesManager();
		$oRulesMan->add($pattern, $rule);

	}


	/**
	* Register a set of Rule instances defined in a RuleSet.
	*
	* @param RuleSet The RuleSet instance to configure from
	* @public
	* @returns void
	*/
	function addRuleSet($ruleSet) {

		$newNamespaceURI = '';	// later
		$newNamespaceURI = NULL;
		$debug	= $this->log->getLog('isDebugEnabled');	// boolean
		if($debug) {
			if($newNamespaceURI == NULL) {
				$this->log->debug("addRuleSet() with no namespace URI");
			} else {
				$this->log->debug("addRuleSet() with namespace URI ".$newNamespaceURI);
			}
		}

		$ruleSet->addRuleInstances($this);

	}


	/**
	* Buld a new Rule object for this pattern
	* Add an "object create" rule for the specified parameters.
	*
	* @param string The element matching pattern
	* @param string The default class name to be created.
	*	 Eg: 'DataSourceConfig'
	* @param string The phpmvc-config xml attribute name which, if
	*   present, contains an override of the config class name to create.
	*   Eg: <data-source ... className="MyDataSourceConfig">
	*	 Note: In this case "MyDataSourceConfig" must be a descendant of
	*	 "DataSourceConfig". This parameter is optional.
	*
	* @returns void
	*/
	function addObjectCreate($pattern, $className, $attributeName=NULL) {

		$oRule = new ObjectCreateRule($className, $attributeName);
		// Eg: ("xxx/yyy/zzz", oDataSourceConfig)
		$this->addRule($pattern, $oRule);

	}


	/**
	* Construct a "set property" rule with the specified name and value
	* attributes.
	* Eg: <object-factory><property name="myProperty" value="valueToSet" ...>>
	*
	* @param string The element matching pattern
	* @param string The attribute name containing the property name to be set
	* @param string The attribute name containing the property value to set
	* @returns void
	* @public
	*/
	function addSetProperty($pattern, $name, $value) {

		$oRule = new SetPropertyRule($name, $value);
		$this->addRule($pattern, $oRule);

    }


	/**
	* Add a "set properties" rule for the specified parameters.
	*  Populates the corresponding properties of the top-of-stack object
	*  (via the SetProperties->begin(..) method)  if an element path
	*  matches the pattern parameter
	*
	* @param string The element matching pattern
	* @param array The names of attributes to map
	* @param array The names of properties mapped to
	* @returns void
	* @public
	*/
	function addSetProperties($pattern, $attributeNames=NULL, $propertyNames=NULL) {

		$oRule = new SetPropertiesRule($attributeNames, $propertyNames);
		$this->addRule($pattern, $oRule);

    }


	/**
	* Add a "set next" rule for the specified parameters.
	*
	* @param string The element matching pattern
	* @param string The method name to call on the parent element
	* @param string [future use !!]
	* @returns void
	* @public
	*/
	function addSetNext($pattern, $methodName, $paramType=NULL) {

		$oRule = new SetNextRule($methodName, $paramType);
		$this->addRule($pattern, $oRule);

	}


	// ----- Object Stack Methods ----------------------------------- ------- //

	/**
	* Return the n'th object down the stack, where 0 is the top element
	* and [getCount()-1] is the bottom element. If the specified index
	* is out of range, return <code>null</code>.

	*  <p>Note: Top-of-Stack is last item pushed onto the stack
	*  Note: Clients may update the state of objects on the stack
	*
	* @param integer Index of the desired element, where 0 is the top of the stack,
	*  1 is the next element down, and so on.
	* @returns object
	* @public
	*/
	function &peek($n=0) {

		$debug = $this->log->getLog('isDebugEnabled');

		// Emulate a stack behavour
		$tos = count($this->stack) - 1;	// last item pushed onto the stack
		$ix = $tos - $n;						// required stack index

		// Return the next-to-top object on the stack without removing it
		$oObject = NULL;
		if(array_key_exists($ix, $this->stack))  {
			$oObject =& $this->stack[$ix];	// [0] is top-of-stack index
		}

		if($oObject != NULL) {
			return $oObject;
		} else {

			if($debug) {
				$this->log->warn("Empty stack (returning NULL)");
			}
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

   }


	/**
	* Push a new object onto the top of the object stack.
	*
	* @param object The new (configuration) object to build
	* @returns void
	* @public
	*/
	function push(&$object) {

		if(count($this->stack) == 0) {
			$this->root =& $object;
		}

		$this->stack[] =& $object;

    }


	/**
	* Pop the top object off of the stack, and return it.  If there are
	* no objects on the stack, return <code>NULL</code>.
	* Note: top-of-stack object was last object to be put on stack
	*
	* @returns object
	* @public
	*/
	function pop() {

		$debug = $this->log->getLog('isDebugEnabled');

		$object = NULL;
		$object = array_pop($this->stack);

		if($object === NULL) {
			if($debug) {
				$this->log->warn('Empty stack, or NULL element (returning NULL)');
			}
			return NULL;
		} else {
			return $object;
		}

	}


	/**
	* Return the current depth of the element stack.
	*
	* @returns integer
	* @public
	*/
	function getCount() {

		return count($this->stack);

	}


	/**
	* Clear the current contents of the object stack.
	*
	* @returns void
	* @public
	*/
	function clear() {

		$this->match		= NULL;
		$this->bodyTexts	= array();
		#$this->params		= NULL;
		#$this->publicId	= NULL;
		$this->stack		= array();

	}


}	// class Digester


?>