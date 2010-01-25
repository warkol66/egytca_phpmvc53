<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ConfigRuleSet.php,v 1.5 2006/02/22 07:20:38 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 07:20:38 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
*  Craig R. McClanahan (original Struts class)
* @version $Revision: 1.5 $
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

		$applicationPattern			= 'phpmvc-config';

		// DataSourceConfig
		$dataSourcesPattern			= 'phpmvc-config/data-sources';
		$dataSourcePattern			= 'phpmvc-config/data-sources/data-source';
		$dataSrcSetPropPattern		= 'phpmvc-config/data-sources/data-source/set-property';

		// ActionConfig
		$actionMappingsPattern		= 'phpmvc-config/action-mappings';
		$actionMappingPattern		= 'phpmvc-config/action-mappings/action';
		$actionMapPropPattern		= 'phpmvc-config/action-mappings/action/set-property';
		$actionForwardPattern		= 'phpmvc-config/action-mappings/action/forward';
		$actionFwdPropPattern		= 'phpmvc-config/action-mappings/action/forward/set-property';

		// FormBean
		$formBeansPattern				= 'phpmvc-config/form-beans';
		$formBeanPattern				= 'phpmvc-config/form-beans/form-bean';
		// DynamicActionForm -> "struts-config/form-beans/form-bean/form-property"
		$formBeanPropPattern			= 'phpmvc-config/form-beans/form-bean/set-property';

		// ControllerConfig
		$controllerConfigPattern	= 'phpmvc-config/controller';
		$setControllerConfigPattern= 'phpmvc-config/controller/set-property';

		// PlugIns
		$plugInPattern					= 'phpmvc-config/plug-in';
		$setPlugInPropPattern		= 'phpmvc-config/plug-in/set-property';

		// GlobForwards
		#$globForwardsPattern		= 'star/global-forwards';	// star = *
		#$setPropertyPattern			= 'phpmvc-config/my-object/property';

		// ViewResourcesConfig
		$viewRescConfigPattern		= 'phpmvc-config/view-resources';
		$setViewRescConfigPattern	= 'phpmvc-config/view-resources/set-property';


		// DataSourceConfig <data-source ...>
		// ----------------------------------- //
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
		// Add a back reference to bind the configuration object to its parent
		// (ApplicationConfig) object.
		// Eg: Rule(pattern-to-match, ApplicationConfig->addDataSourceConfig(dataSourceConfig))
		$digester->addSetNext($dataSourcePattern, 'addDataSourceConfig');		
		$digester->addSetProperty($dataSrcSetPropPattern, 'property', 'value');


		// ActionConfig
		// ----------------------------------- //
		$digester->addObjectCreate($actionMappingPattern, 'ActionConfig', 'className');
		$digester->addSetProperties($actionMappingPattern);
		$digester->addSetNext($actionMappingPattern, 'addActionConfig');
		$digester->addSetProperty($actionMapPropPattern, 'property', 'value');

		$digester->addObjectCreate($actionForwardPattern, 'ForwardConfig', 'className');
		$digester->addSetProperties($actionForwardPattern);
		$digester->addSetNext($actionForwardPattern, 'addForwardConfig');
		$digester->addSetProperty($actionFwdPropPattern, 'property', 'value');


		// FormBeanConfig
		// ----------------------------------- //
		$digester->addObjectCreate($formBeanPattern, 'FormBeanConfig', 'className');
		$digester->addSetProperties($formBeanPattern);
		$digester->addSetNext($formBeanPattern, 'addFormBeanConfig');
		// DynamicActionForm -> "struts-config/form-beans/form-bean/form-property"
		$digester->addSetProperty($formBeanPropPattern, 'property', 'value');


		// ControllerConfig
		// ----------------------------------- //
		$digester->addObjectCreate($controllerConfigPattern, 'ControllerConfig', 'className');
		$digester->addSetProperties($controllerConfigPattern);
		$digester->addSetNext($controllerConfigPattern, 'setControllerConfig', 'ControllerConfig');
		$digester->addSetProperty($setControllerConfigPattern, 'property', 'value');


		// PlugIns Config
		// ----------------------------------- //
		// Class (NULL) name MUST be specified in the xml element (<plug-in classname="MyPluginClass" ...>
		$digester->addObjectCreate($plugInPattern, NULL, 'className');
		$digester->addSetProperties($plugInPattern);
		$digester->addSetNext($plugInPattern, 'addPlugIn', 'phpmvc.action.PlugIn');
		$digester->addSetProperty($setPlugInPropPattern, 'property', 'value');


		// ViewResourcesConfig
		// ----------------------------------- //
		$digester->addObjectCreate($viewRescConfigPattern, 'ViewResourcesConfig', 'className');
		$digester->addSetProperties($viewRescConfigPattern);
		$digester->addSetNext($viewRescConfigPattern, 'setViewResourcesConfig', 'ViewResourcesConfig');
		$digester->addSetProperty($setViewRescConfigPattern, 'property', 'value');

    }

} // end class ConfigRuleSet

?>