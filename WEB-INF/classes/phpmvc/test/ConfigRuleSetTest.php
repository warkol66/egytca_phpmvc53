<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/ConfigRuleSetTest.php,v 1.3 2006/02/22 07:21:00 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 07:21:00 $
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
* <p>The set of Digester rules required to parse a php.MVC application
* configuration file (<code>phpmvc-config.xml</code>).</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits: Craig R. McClanahan (original Struts class)
* @version $Revision: 1.3 $
*/ 
class ConfigRuleSet extends RuleSetBase {

	// ----- Public Methods --------------------------------------------- //

	/**
	* <p>Add the set of Rule instances defined in this RuleSet to the
	* specified <code>Digester</code> instance, associating them with
	*
	* @param digester Digester, an XML digester object
	* @access public
	* @return void
	*/ 
	function addRuleInstances(&$digester) {

		$applicationPattern		= 'phpmvc-config';

		// DataSourceConfig
		$dataSourcesPattern		= 'phpmvc-config/data-sources';
		$dataSourcePattern		= 'phpmvc-config/data-sources/data-source';

		// ActionConfig
		$actionMappingsPattern	= 'phpmvc-config/action-mappings';
		$actionMappingPattern	= 'phpmvc-config/action-mappings/action';
		$actionForwardPattern	= 'phpmvc-config/action-mappings/action/forward';

		// FormBean
		$formBeansPattern			= 'phpmvc-config/form-beans';
		$formBeanPattern			= 'phpmvc-config/form-beans/form-bean';


		$globForwardsPattern		= '*/global-forwards';
		$setPropertyPattern		= 'phpmvc-config/my-object/property';


		// DataSourceConfig <data-source ...>
		// Create a new configuration object ('DataSourceConfig')
		$digester->addObjectCreate(
							$dataSourcePattern,	// <data-source ...>
							'DataSourceConfig',	// config class to build
							'className');			// [optional] specify an alternate to 
														// the default 'DataSourceConfig' config 
														// file, if this attribute is present in
														// the phpmvc-config xml descriptor file
														// Eg: 
														// <data-source ... className="MyDataSourceConfig">
		// Set the configuration objects properties
		// phpmvc-config xml descriptor file attributes must match the target object methods
	 	// Eg: "driverClassName" maps to "BasicDataSource->setDriverClassName"
		$digester->addSetProperties($dataSourcePattern);
		// Add a callback reference to bind the configuration object to its parent
		// (ApplicationConfig) object.
		// Eg: Rule(pattern-to-match, ApplicationConfig->addDataSourceConfig(dataSourceConfig))
		$digester->addSetNext($dataSourcePattern, 'addDataSourceConfig');		


		// ActionConfig
		$digester->addObjectCreate($actionMappingPattern, 'ActionConfig');
		$digester->addSetProperties($actionMappingPattern);
		$digester->addSetNext($actionMappingPattern, 'addActionConfig');	

		$digester->addObjectCreate($actionForwardPattern, 'ForwardConfig');
		$digester->addSetProperties($actionForwardPattern);
		$digester->addSetNext($actionForwardPattern, 'addForwardConfig');	


		// FormBeanConfig
		$digester->addObjectCreate($formBeanPattern, 'FormBeanConfig');
		$digester->addSetProperties($formBeanPattern);
		$digester->addSetNext($formBeanPattern, 'addFormBeanConfig');	

    }

} // end class ConfigRuleSet



/**
* Class that calls <code>addProperty()</code> for the top object
* on the stack, which must be a
* <code>org.apache.struts.config.DataSourceConfig</code>.
*/
#class AddDataSourcePropertyRule extends Rule {
#
#	function AddDataSourcePropertyRule($digester) { }
#
#	function begin($attributes) { }
#
#}


/**
* Class that sets the name of the class to use when creating action mapping
* instances. The value is set on the object on the top of the stack, which
* must be a <code>org.apache.struts.config.ApplicationConfig</code>.
*/
#class SetActionMappingClassRule extends Rule {
#
#	function SetActionMappingClassRule($digester) { }
#
#	function begin($attributes) { }
#
#}


/**
* An object creation factory which creates action mapping instances, taking
* into account the default class name, which may have been specified on the
* parent element and which is made available through the object on the top
* of the stack, which must be a
* <code>org.apache.struts.config.ApplicationConfig</code>.
* @extends AbstractObjectCreationFactory
*/
#class ActionMappingFactory {
#
#	# @return Object
#	function createObject($attributes) { }
#
#}

?>