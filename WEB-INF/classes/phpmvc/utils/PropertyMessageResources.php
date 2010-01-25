<?php 
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/PropertyMessageResources.php,v 1.6 2006/02/22 08:32:32 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 08:32:32 $
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
* Concrete subclass of <code>MessageResources</code> that reads message keys
* and corresponding strings from named property resources in the same manner
* that <code>java.util.PropertyResourceBundle</code> does.  The
* <code>base</code> property defines the base property resource name, and
* must be specified.
* <p>
* <strong>IMPLEMENTATION NOTE</strong> - This class trades memory for
* speed by caching all messages located via generalizing the Locale under
* the original locale as well.
* This results in specific messages being stored in the message cache
* more than once, but improves response time on subsequent requests for
* the same locale + key combination.
*
* <p>JCW: CONSIDER: cache formatted message strings to disk on locale-key !!
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakata Struts class)
* @version $Revision: 1.6 $
* @public
*/
class PropertyMessageResources extends MessageResources {

	// ----- Properties ----------------------------------------------------- //

	/**
	* The set of locale keys for which we have already loaded messages, keyed
	* by the value calculated in <code>localeKey()</code>.
	* @private
	* @type array
	*/
	var $locales = array();

	/**
	* The cache of messages we have accumulated over time, keyed by the
	* value calculated in <code>messageKey()</code>.
	* @private
	* @type array
	*/
	var $messages = array();


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new PropertyMessageResources according to the
	* specified parameters.
	*
	* @param MessageResourcesFactory	The MessageResourcesFactory that created us
	* @param string	The configuration parameter for this MessageResources.
	*						Eg: 'LocalStrings', our properties file.
	* @param boolean	The returnNull property we should initialize with. Optional
	* @public
	* @returns void
	*/
	function PropertyMessageResources($factory, $config, $returnNull=NULL) {

		// Build the base class first
		parent::MessageResources($factory, $config, $returnNull);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Returns a text message for the specified key, for the default Locale.
	* A null string result will be returned by this method if no relevant
	* message resource is found for this key or Locale, if the
	* <code>returnNull</code> property is set.  Otherwise, an appropriate
	* error message will be returned.
	* <p>
	*
	* @param Locale	The requested message Locale, or <code>null</code>
	*						for the system default Locale. (can be NULL)
	* @param string	The message key to look up
	* @public
	* @returns string
	*/
	function _getMessage($locale, $key) {

		$trace = $this->log->getLog('isTraceEnabled');
		$debug = $this->log->getLog('isDebugEnabled');

		if($trace) {
			$this->log->trace('Start: PropertyMessageResources->_getMessage(...)'.
									'['.__LINE__.']');
		}

		// Initialize variables we will require
		$localeKey	= $this->localeKey($locale); // 'en_AU_QL'
		$originalKey= $this->messageKey($localeKey, $key);//'en_AU_QL.hello_world'
		$messageKey	= '';	// String
		$message		= '';		// String
		$underscore	= 0;		// Int
		$addIt		= False;  // Add if not found under the original key

		// Loop from specific to general Locales looking for this message
		while(True) {

			// Load this Locale's messages if we have not done so yet
			// (load the properties file from disk)
			$this->loadLocale($localeKey);

			// Check if we have this key for the current locale key
			// Most specific Locale. Eg: 'en_AU_QL'
			$messageKey = $this->messageKey($localeKey, $key);
			if($debug) {
				$this->log->debug(
						' Loop from specific to general Locales looking for this message: '
						.$localeKey.$messageKey.'['.__LINE__.']');
			}

			if( array_key_exists($messageKey, $this->messages) )
				$message = $this->messages[$messageKey];

			if($message != '') {
				if($addIt)
					$this->messages[$originalKey] = $message;

				return $message;
			}

			// Strip trailing modifiers to try a more general locale key
			$addIt = True;
			$pos = strrpos($localeKey, '_');
			if($pos < 1 )
				break; // underscore not found, just the 'en' part perhaps

			$localeKey = substr($localeKey, 0, $pos); // 'en_AU' part

		} // while()


		// Trying the default locale if the current locale is different
		if(!$this->defaultLocale->equals($locale)) {
			$localeKey = $this->localeKey($this->defaultLocale);		// String

			$messageKey = $this->messageKey($localeKey, $key);	// String
			if($debug) {
				$this->log->debug(
						' Trying the default locale if the current locale is different: '
						.$messageKey.'['.__LINE__.']');
			}
			$this->loadLocale($localeKey);

			$message = '';

			if( array_key_exists($messageKey, $this->messages) )
				$message = $this->messages[$messageKey]; // String

			if($message != '') {
				if($addIt) {
					$this->messages[$originalKey] = $message;
				}

				return $message;
			}

		}


		// As a last resort, try the default Locale
		// Eg: 'LocalStrings.properties'
		$localeKey = '';
		$messageKey = $this->messageKey($localeKey, $key);

		if($debug) {
			$this->log->debug(' Last resort, try the default Locale: '.$messageKey.'['.__LINE__.']');
		}

		$this->loadLocale($localeKey); // try to load the default locale

		if( array_key_exists($messageKey, $this->messages) )
			$message = $this->messages[$messageKey]; // String

		if($message != '') {
			if($addIt)
				$this->messages[$originalKey] = $message;

			return $message;
		}

		// No match ... Return an appropriate error indication
		if($this->returnNull) {
			return NULL;
		} else {
			$localeKey = $this->localeKey($locale);
			return ("???" . $this->messageKey($localeKey, $key) . "???");
		}
	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Load the messages associated with the specified Locale key.  For this
	* implementation, the <code>config</code> property should contain a fully
	* qualified package and resource name, separated by periods, of a series
	* of property resources to be loaded from the class loader that created
	* this PropertyMessageResources instance.
	*
	* We try to load the most specific resource first. Eg:
	*  1) 'en_AU_QL' - most specific
	*  2) 'en_AU'
	*  3) 'en'
	*  4) ''         - default appServer Locale
	*
	* @param string	The locale key for the messages to be retrieved.<br>
	*						Eg: 'en_AU_QL.hello_world'
	* @private
	* @returns void
	*/
	function loadLocale($localeKey) {

		$trace = $this->log->getLog('isTraceEnabled');
		$debug = $this->log->getLog('isDebugEnabled');

		if($trace) {
			$this->log->trace( 'Start: PropertyMessageResources->loadLocale('
										. $localeKey .')' . '[' . __LINE__ . ']' );
		}
		if($debug) {
			$this->log->debug(' LocaleKey = "' . $localeKey . '" [' . __LINE__ . ']');
		}


		// Have we already attempted to load messages for this locale?
		if( array_key_exists($localeKey, $this->locales) )
			return;

		$this->locales[$localeKey] = $localeKey;

		// Set up to load the property resource for this locale key, if we can
		// PROVISIONAL CHANGE
		// Note: 
		// If the property file path contains any slash characters, we assume this 
		//  is a fully qualified domain name path and leave the path as-is.
		//  Eg: './phpmvc.com\MyAppResources'
		// If the property file path does not contains any slash characters, we 
		//  assume this is a Java/Struts type domain name scheme and replace all
		//  '.' characters with '/' characters.
		//  Eg: 'com.phpmvc.MyAppResources'
		if( (strpos($this->config, '/')) || (strpos($this->config, '\\')) ) {
			// No transformation of '.' with '/'
			$name = $this->config;
		} else {
			// Transformation of '.' with '/'
			$name = str_replace('.', '/', $this->config); // String
		}

		if(strlen($localeKey) > 0)
			$name .= '_' . $localeKey;
		$name .= '.properties'; // 'actions/LocalStrings_en_AU_QL.properties'

		if($debug) {
			$this->log->debug(' Loading the property resource "' . 
										$name . '" [' . __LINE__ . ']');
		}

		// Load the specified property resource
		$fp = '';
		$fp = @fopen($name, 'r', 1); // using the include_path string variable
		if($fp) {
			$delimChar	= '=';
			$maxLineLen	= 1000;
			$messages	= NULL; // array()

			while( !feof($fp) ) {
				$lineCSV = fgetcsv($fp, $maxLineLen, $delimChar);
				if(trim($lineCSV[0]) == '')
					continue;

				// Ignore comment lines
				// Patch by Michael (mschmitz) - 15-July-2004
				if (preg_match("/^\S*[#!]/", $lineCSV[0])) {
					continue;
				}

				$msgKey = $lineCSV[0];
				$localeMsgKey = $this->messageKey($localeKey, $msgKey);

				// ['en_AU_QL.hello_world'] = ['Hello Cruel World']
				$this->messages[$localeMsgKey] = $lineCSV[1];
			}

			fclose($fp);
		}
	}
}
?>