<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/MessageResources.php,v 1.5 2006/02/22 08:18:16 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 08:18:16 $
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
* General purpose abstract class that describes an API for retrieving
* Locale-sensitive messages from underlying resource locations of an
* unspecified design, and optionally utilizing the <code>MessageFormat</code>
* class to produce internationalized messages with parametric replacement.
* <p>
* Calls to <code>getMessage()</code> variants without a <code>Locale</code>
* argument are presumed to be requesting a message string in the default
* <code>Locale</code> for this appServer.
* <p>
* Calls to <code>getMessage()</code> with an unknown key, or an unknown
* <code>Locale</code> will return <code>null</code> if the
* <code>returnNull</code> property is set to <code>true</code>.  Otherwise,
* a suitable error message will be returned instead.
*
* <p>
* php.MVC provides a concrete derived class PropertyMessageResources
* that handles messages in text string properties files. Another
* alternative would be to implement a sub class that handles messages
* stored in a database table, eg DBTableMesssageResource.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakata Struts class)
* @version $Revision: 1.5 $
* @public
*/
class MessageResources {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL;

	/**
	* The configuration parameter used to initialize this MessageResources.
	* @private
	* @type string
	*/
	var $config = NULL;

	/**
	* The default Locale for our environment.
	* @private
	* @type Locale
	*/
	var $defaultLocale = NULL; // Locale.getDefault()

	/**
	* The <code>MessageResourcesFactory</code> that created this instance.
	* @private
	* @type MessageResourcesFactory
	*/
	var $factory = NULL;

	/**
	* The default MessageResourcesFactory used to create MessageResources
	* instances.
	* @private
	* @type MessageResourcesFactory
	*/
	var $defaultFactory = NULL;

	/**
	* The set of previously created MessageFormat objects, keyed by the
	* key computed in <code>messageKey()</code>.
	* @private
	* @type array
	*/
	var $formats = array();

	/**
	* Should we return <code>null</code> instead of an error message string
	* if an unknown Locale or key is requested?
	* @private
	* @type boolean
	*/
	var $returnNull = False;


	/** 
	* @access public 
	* @returns string
	*/
	function getConfig() {
		return $this->config;
	}


	/** 
	* @access public 
	* @returns Locale
	*/
	function getDefaultLocale() {
		return $this->defaultLocale;
	}

	/**
	* @param Locale	The default Locale for our environment.
	* @access public 
	* @returns void
	*/
	function setDefaultLocale($locale) {
		$this->defaultLocale = $locale;
	}


	/** 
	* @access public 
	* @returns MessageResourcesFactory
	*/
	function getFactory() {
		return $this->factory;
	}

	/** 
	* @access public 
	* @returns boolean
	*/
	function getReturnNull() {
		return $this->returnNull;
	}

	/**
	* @param boolean	Should we return <code>null</code> instead of an error
	*						message string if an unknown Locale or key is requested?
	* @access public 
	* @returns void
	*/
	function setReturnNull($returnNull) {
		$this->returnNull = $returnNull;
	}


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new MessageResources according to the specified parameters.
	*
	* @param MessageResourcesFactory	The MessageResourcesFactory that created us
	* @param string	The configuration parameter for this MessageResources
	* @param boolean	The returnNull property we should initialize with. [Optional]
	* @public
	* @returns void
	*/
	function MessageResources($factory, $config, $returnNull=False) {

		$this->factory		= $factory;
		$this->config		= $config;
		$this->returnNull	= $returnNull;

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isTraceEnabled'	, False);
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isErrorEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Abstract message processing method.
	* <p>Returns a text message for the specified key, for the default Locale.
	* A null string result will be returned by this method if no relevant
	* message resource is found for this key or Locale, if the
	* <code>returnNull</code> property is set.  Otherwise, an appropriate
	* error message will be returned.
	*
	* <p>This method must be implemented by a concrete subclass.
	*
	* @param Locale	The requested message Locale, or <code>null</code>
	*  					for the system default Locale. (can be NULL)
	* @param string	The message key to look up
	* @public
	* @returns string
	*/
	function _getMessage($locale, $key) {
		;
	}


	/**
	* Returns a text message after parametric replacement of the specified
	* parameter placeholders.  A null string result will be returned by
	* this method if no resource bundle has been configured.
	*
	* @param Locale	The requested message (User) Locale, or <code>NULL</code>
	*						for the system default Locale
	* @param string	The message key to look up
	* @param array		Array of replacement parameters for placeholders.
	*						Eg: $args = array('1st replacement', '2nd replacement');
	* @param string	arg0. The replacement for placeholder {0} in the message
	* @param string	arg1. The replacement for placeholder {1} in the message
	* @param string	arg2. The replacement for placeholder {2} in the message
	* @param string	arg3. The replacement for placeholder {3} in the message
	*
	* @public
	* @returns string
	*/
	function getMessage($locale, $key, $args='', $arg0='', $arg1='', $arg2='', $arg3='') {

		$trace = $this->log->getLog('isTraceEnabled');
		$debug = $this->log->getLog('isDebugEnabled');

		if($trace) {
			$this->log->trace('Start: MessageResources->getMessage(...)'.
									'['.__LINE__.']');
		}

		// If no User Locale supplied, use the AppServer default Locale
		if($locale == NULL)
			$locale = $this->defaultLocale;

		$format		= NULL; // MessageFormat
		$localeKey	= $this->localeKey($locale); // locale 'en_AU_QL'
		$formatKey	= $this->messageKey($localeKey, $key); // String

		if(array_key_exists($formatKey, $this->formats))
			$format = $this->formats[$formatKey]; // MessageFormat

		// Cache MessageFormat instances as they are accessed
		if($format == NULL) {
			// Call to the derived MassageResources class, Eg: 
			// PropertyMessageResources. PropertyMessageResources class
			// implements the message resources as text property files.
			$formatString = $this->_getMessage($locale, $key); // String

			if($formatString == NULL) {
				if($this->returnNull)
					return NULL;
				else
					return '???' . $formatKey . '???';
			}

			// Not using the MessageResourceFactory just to keep it 
			// simple for now.
			// Eg: 
			// No replacement parameters - new MessageFormat( "Hello World" )
			// With replacement parameters - new MessageFormat( "Hello {0} World" )
			$format = new MessageFormat( $this->escape($formatString) );
			// Cache the format object complete with message string for reuse
			$this->formats[$formatKey] = $format;
		}

		return $format->formatMsg($args, $arg0, $arg1, $arg2, $arg3);

	}


	/**
	* Return <code>true</code> if there is a defined message for the specified
	* key in the specified Locale.
	*
	* @param Locale	The requested message Locale, or <code>NULL</code>
	*						for the system default Locale
	* @param string	The message key to look up
	* @public
	* @returns boolean
	*/
	function isPresent($locale=NULL, $key) {

		$message = $this->getMessage($locale, $key);
		if($message == NULL)
			return False;
		//$message.startsWith("???") && message.endsWith("???")
		elseif( eregi("^???.*???$") )
			return False; // FIXME - Only valid for default implementation
		else
			return True;
	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Escape any single quote characters that are included in the specified
	* message string.
	*
	* @param string	The string to be escaped
	* @private
	* @returns string
	*/
	function escape($string) {

		if( ($string == '') || (strpos($string, '\'') < 1) )
			// NULL string or "'" not found
			return $string;

		// TEST THIS
		$string = str_replace("\'", "\\\'", $string);

		return $string;

	}


	/**
	* Compute and return a key to be used in caching information by a Locale.
	* <strong>NOTE</strong> - The locale key for the default Locale in our
	* environment is a zero length String.
	*
	* @param Locale	The locale for which a key is desired
	* @private
	* @returns string
	*/
	function localeKey($locale) {

		if($locale == NULL) {
			 return '';
		#} elseif($locale->equals($this->defaultLocale)) {
			# return ''
		} else {
			return $locale->toString();
		}
	}


	/**
	* Compute and return a key to be used in caching information
	* by locale key and message key.
	*
	* @param string	The locale key for which this cache key is calculated
	* @param string	The message key for which this cache key is calculated
	* @private
	* @returns string
	*/
	function messageKey($localeKey, $key) {

		$messageKey = '';
		if($localeKey != '')
			$messageKey .= $localeKey . '.';

		return $messageKey .= $key;

	}


	// ----- Static Methods ------------------------------------------------- //

	/**
	* Create and return an instance of <code>MessageResources</code> for the
	* created by the default <code>MessageResourcesFactory</code>.
	*
	* @param string	Configuration parameter for this message bundle.
	* @public
	* @returns MessageResources
	*/
	function getMessageResources($config) {

		if($this->defaultFactory == NULL)
			$this->defaultFactory = MessageResourcesFactory::createFactory();

		return $this->defaultFactory->createResources($config);

	}

}
?>