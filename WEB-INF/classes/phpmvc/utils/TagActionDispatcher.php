<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/TagActionDispatcher.php,v 1.3 2006/02/22 08:52:49 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:52:49 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2004-2006 John C.Wildenauer.  All rights reserved.
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
* This is an application specific implementation of the <code>ActionDispatcher
* </code> class that provides access to basic template tag functionality within
* the php.MVC framework. This class can be used with or without using the php.MVC
* tags services, although a small performance penalty may result due to the extra
* processing required.
* 
* <p>See the serviceResponse() method documentation for usage.</p>
*
* @author John C Wildenauer
* @version $Revision: 1.3 $
* @public
*/
class TagActionDispatcher extends ActionDispatcher {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this class, configured according to the
	* specified parameters.
	*
	* @param string	Uri or Definition name to forward (Eg: '/index.php')
	* @param Wrapper	The Wrapper associated with the resource that will
	*  be forwarded to or included (required).
	* @param string	The revised servlet path to this resource (if any).
	* @param string	The revised extra path information to this resource if any).
	* @param string	Query string parameters included with this request (if any).
	* @param string	Servlet name (if a named dispatcher was created) else 
	* <code>NULL</code>
	*
	* @public
	* @returns void
	*/
	function TagActionDispatcher($uri='', $wrapper='', $servletPath='',
 											$pathInfo='', $queryString='', $name='') {


		// Setup the parent constructor first
		parent::ActionDispatcher($uri='', $wrapper='', $servletPath='',
 											$pathInfo='', $queryString='', $name='');

		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- Private Methods ------------------------------------------------ //

	/**
	* The <code>TagActionDispatcher</code> class provides some basic template tag 
	* functionality within the php.MVC. This class can be used with or without using
	* the php.MVC tags services. 
	* 
	* <p>The <code>TagActionDispatcher->serviceResponse()</code> method exposes three
	* objects to the View resources (templates), as does the base
	* <code>ActionDispatcher->serviceResponse()</code> method:
	* <li>$form - The FormBean object</li>
	* <li>$errors - The Errors collection object</li>
	* <li>$data - The Value (business data) collection object</li>
	* See the {@link ActionDispatcher} class for more information.
	* </p>
	* 
	* <p>The template tag handling parameters are configured via the {@link 
	* ViewResourcesConfig} class. The <code>ViewResourcesConfig</code> class
	* in turn can be configured by adding an &lt;view-resources ... /&gt; element
	* to the application <code>phpmvc-config.xml</code> configuration file.
	* Something like:
	* <pre>
	* &lt;view-resources
	*   appTitle  = "Simple Tab Action"
	*   copyright = "Copyright © 2010 Flash Jack. All rights reserved."
	*   processTags = "0"
	*   compileAll  = "0">
	* &lt;/view-resources&gt;
	* </pre>
	* The <code>ViewResourcesConfig</code> class can be extended to provide custom
	* configuration parameters. For more information on extending the <code>
	* ViewResourcesConfig</code> class see the php.MVC documentation on extending
	* configuration classes. See the {@link ViewResourcesConfig } class for a list 
	* of configuration parameters available .
	* </p>
	* <p>If an &lt;view-resources ... /&gt; element is not defined in the application 
	* <code>phpmvc-config.xml</code> configuration file the default attributes
	* of the <code>ViewResourcesConfig</code> class are used.
	* </p>
	* <p><b>This method performs the following 0perations:</b><br>
	*	a) Retrieve the <code>$form</code>, <code>$errors</code> and <code>$data</code>
	*		objects from the <code>request</code>. If any of these objects have been
	*		previously created (for example in an Action class) the object will now be
	*		visible to the resource template, otherwise the object will be set to 
	*		<code>NULL</code>.<br>
	*	b) Get a reference the {@link ViewResourcesConfig } object containing the
	*		configuration parameters.<br>
	*  c) Setup the paths to the template source files and the compiled template files
	*		using the ViewResourcesConfig parameters.<br>
	*  d) Setup the tag pages: The filename extension (perhaps ".ssp") of the resource 
	*		template is compared to the ViewResourcesConfig->tagFlagStr parameter to
	*		determine if this page requires processing, otherwise the page is handled
	*		as a standard (non-tags) template file.<br>
	*	e)	Determine whether to run the template tag processor according to the 
	*		ViewResourcesConfig->processTags parameter: If <code>processTags</code> evaluates
	*		to True, the resource page (and included pages) is processed by the tag processor
	*		class, otherwise the tag processor is not called. The page developer would normally
	*		set the ViewResourcesConfig->processTags property to <code>True</code> during
	*		development, and <code>False</code> otherwise. Note that even when the <code>
	*		processTags</code> property is set to <code>True</code>, tag pages will only be
	*		compiled if they have been modified. (Eg: the source more recent that the compiled
	*		file).<br>
	*	f) The requested View resource (template) is retrieved (to the $pageBuff) and 
	*		the resulting page contents is attached to the response buffer for dispatch 
	* 		to the client.
	* </p>
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	*
	* @author	John Wildenauer
	* @version	1.0
	* @private
	* @returns void
	*/
	function serviceResponse(&$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace)
			$this->log->trace('Start: TagActionDispatcher->serviceResponse(..)['.__LINE__.']');

		//////////
		// Retrieve attributes from the Request object.
		// Note: $request->getAttribute(...) returns NULL if no value is set

		// Get our FormBean object
		$form		= $request->getAttribute( Action::getKey('FORM_BEAN_KEY') );

		// Get our Errors object
		$errors	= $request->getAttribute( Action::getKey('ERROR_KEY') );

		// Get our Value object (Business data)
		$data		= $request->getAttribute( Action::getKey('VALUE_OBJECT_KEY') );


		//////////
		// Get the resource configuration object (class ViewResourcesConfig)
		
		// Get the parent application configuration object: ApplicationConfig
		$appConfig = NULL;
		$appConfig = $request->getAttribute(Action::getKey('APPLICATION_KEY'));
		// Get the ViewResources configuration object: ViewResourcesConfig
		// Note: If the ViewResources instance has not been configured via an
		//       phpmvc-config.xml configuration file, a new instance is created
		//       when we call ApplicationConfig->getViewResourcesConfig() using
		//       the default ViewResourcesConfig class attributes.
		$viewConfig = NULL;
		$viewConfig =& $appConfig->getViewResourcesConfig();


		//////////
		// Setup the paths and view resource file location

		// The resource (page) to display
		$resourceURI = $this->uri;
		// Remove any leading slashes from the URI
		$firstChar = substr($resourceURI, 0, 1);
		// Note the escaped "\"
		if($firstChar == '/' || $firstChar == '\\') {
			$resourceURI = substr($resourceURI, 1);
		}

		$path		= $viewConfig->tplDir;		// The source template directory ("./WEB-INF/tpl")
		$path_C	= $viewConfig->tplDirC;		// The compiled resource directory. Eg: "./tpl_C"
		$extC		= $viewConfig->extC;			// The compile  flag. Eg: "myPage.ssp[C]"
		$process	= $viewConfig->processTags;// Development/production flag. [True|False]


		//////////
		// Setup the tag pages

		// Indicates tags template source file(s) to be pre-processed. Eg: "myPage.ssp"
		$tagFlagStr = $viewConfig->tagFlagStr; // This extension triggers tag processing ('.ssp')
		$tagFlagCnt = $viewConfig->tagFlagCnt; // Sample the trailing filename character (-4)

		// Indicates tags template source file(s) to be pre-processed. Eg: "myPage.ssp"
		$tagFile		= False;							// Is this a phpmvc tag file? [T|F]
		$tplFile		= '';								// The resource tamplate file to process
		$tagFlagURL = substr($resourceURI, $tagFlagCnt);	// Eg: "myPage<<<.ssp>>>"
		// Is this a phpmvc tags template file. Eg: if( '.ssp' == myPage.ssp )
		if( $tagFlagStr == $tagFlagURL ) {
			// We have a phpmvc tag template file (tag processing may be required)
			$tagFile			= True;
			$resourceURI_C	= $resourceURI.$extC;	// The compiled resource. Eg: "myPage.sspC"
			$tplFile			= $path_C.'/'.$resourceURI_C;
		} else {
			// We have a standard template file (no tag processing required)
			$tplFile = $path.'/'.$resourceURI;	
		}

		// Do we run the template engine processor
		//  - Use $process = True during active development
		//  - Use $process = False when pages are in production (complete).
		if( $tagFile && $process ) {
			// Create a new tag engine instance

			// Include the TagManager.php file in "/WEB-INF/GlobalPrependEx.php"
			$tagMan = new TagManager($viewConfig);
			$resMsg = $tagMan->processTagPages($path, $resourceURI);
			
			if($resMsg != '') {
				echo "<b>Warning:</b> Page compilation failed ...<br>";
				echo ' - '.$resMsg;
				exit;
			} else {
				echo "<b>Notice:</b> Page compilation complete<br>";
			}
		}


		//////////
		// Retrieve the requested page, to the $pageBuff
		$pageBuff = '';
		ob_start();
			#include $requestURI;
			include $tplFile;
			$pageBuff = ob_get_contents();
		ob_end_clean();

		// Attach the output to the response object for later transmission
		$response->setResponseBuffer($pageBuff);

	}

}