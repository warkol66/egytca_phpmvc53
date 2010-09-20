<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/BootUtils.php,v 1.5 2006/02/22 07:13:50 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 07:13:50 $
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
* Application configuration and setup methods
*
* @author John C. Wildenauer
* @version $Revision: 1.5 $
* @public
*/
class BootUtils {

	/**
	* Load the application configuration
	* <p>Returns an application configuration object (ApplicationConfig)
	*
	* @param ActionServer		The php.MVC controller object reference
	* @param AppServerConfig	The AppServerConfig object reference
	* @param string				The relative path to the phpmvc configuration files
	* @param string				The config Id. As in: 'config', 'config/admin', ...
	* @param array					The config data for this config Id. 
	*                          Something like: array('name'=>'phpmvc-config.xml', 'fc'=>True).
	*									Where 'name' is the config file name and 'fc' is the "Force Compile"
	*									status for this XML configuration file.
	* @param string				This app server instance root directory. Like: 'C:\WWW\phpmvc-base'
	* @param string				The XML (Digester) classes)prepend file. ['GlobalPrependXMLEx.php']
	* @public
	* @returns ApplicationConfig
	*/
	function loadAppConfig(&$actionServer, &$appServerConfig, $configPath='', $cfgId='config', 
									$cfgValue='', $appServerRootDir='', $globalPrependXML='') {

		$initXMLConfig	= False;
		$cfgDataMTime	= 0;
		$cfgXMLMTime	= 0;
		$forceCompile	= False;		// Force a recompile of the XML config file (development)
		$oApplicationConfig	= '';
		$phpmvcConfigXMLFile	= 'phpmvc-config.xml';
		$phpmvcConfigDataFile= 'phpmvc-config.data';

		if($cfgValue != '') {
			$phpmvcConfigXMLFile 	= $cfgValue['name'];
			$forceCompile				= $cfgValue['fc'];
			// Setup the serialised XML config file name. Eg: 'phpmvc-config-admin.data'
			$phpmvcConfigDataFile	= preg_replace("/\..*/", '', $phpmvcConfigXMLFile); // strip ext
			$phpmvcConfigDataFile  .= '.data';	// Add the ".data" ext. Eg: 'phpmvc-config-admin.data'
		}

		if($configPath == '') {
			// Setup default path to the sub-application configuration files
			$configPath = './WEB-INF';
		}


		if( (! file_exists($configPath.'/'.$phpmvcConfigDataFile)) || $forceCompile == True ) {	
			// No config data file, or $forceCompile - so initialise the application
			$initXMLConfig = True;
		} else {
			// Check the config file timestamps
			$cfgDataMTime	= filemtime($configPath.'/'.$phpmvcConfigDataFile);
			$cfgXMLMTime	= filemtime($configPath.'/'.$phpmvcConfigXMLFile);
			if($cfgXMLMTime > $cfgDataMTime) {
				// The 'phpmvc-config.xml' has been modified, so we
				// need to reinitialise the application
				$initXMLConfig = True;
			}

			//sino me fijo si no cambio algun xml de algun modulo
			if (!$initXMLConfig) {
				global $moduleRootDir;
				$modulesPath = $moduleRootDir."WEB-INF/classes/modules";
	
				$modules = scandir($modulesPath);
				$i = 0;
				while ($i<count($modules) && !$initXMLConfig) {
					$module = $modules[$i];
					if (substr("$module", -1) != "." && $module != ".svn" && is_dir($modulesPath.'/'.$module)) {
						$expectedFile = $moduleRootDir."WEB-INF/classes/modules/".$module."/setup/phpmvc-config-".$module.".xml";
						if (file_exists($expectedFile)) {
							$cfgXMLMTime	= filemtime($expectedFile);
							if($cfgXMLMTime > $cfgDataMTime) {
								$initXMLConfig = True;
							}
						}
					}
					$i++;
				}	
			}
			
		}

		// Unserialise	the application config data
		if($initXMLConfig == False) {
			// 'phpmvc-config.xml' has not been modified, so we
			// do not need to process the xml config data
			$strConfig = implode('', @file($configPath.'/'.$phpmvcConfigDataFile));
			$oApplicationConfig = @unserialize($strConfig);

			if($oApplicationConfig) {
				// The ApplicationConfig class should be good to go

				// Check the class serialise version info. The class structure serialised
				// should be compatable with the class definition.
				// TO-DO: check all config classes
	
				// Eg: 'ApplicationConfig:ApplicationConfig.php:20021025-0955
				$strClassID	= $_strClassID = ''; 
				// The instanciated class info
				$strClassID	= $oApplicationConfig->getClassID();
				// The class definition info
				$_strClassID = ApplicationConfig::_getClassID();

				$aClassID = explode(':', $strClassID);	// convert the 'xxx:yyy:nnn' to array()
				$classVersionID = $aClassID[2];			// expecting a class serial date stamp
				$_aClassID = explode(':', $_strClassID);
				$_classVersionID = $_aClassID[2];

				if($_classVersionID != $classVersionID) {
					// Error: php.MVC configuration data stored on disk is not compatable 
					// with this config class
					#echo '<b>Error:</b> php.MVC configuration version stored on disk is not '.
					#		'compatable with this config class "ApplicationConfig". Please '.
					#		'regenerate the application configuration data from '.
					#		'"phpmvc-config.xml"<br><br>';
					// Don't bother the user, we'll just try and recompile the app config
					$initXMLConfig = True;	
					$oApplicationConfig = NULL;
				}

				$actionServer->appServerConfig = $appServerConfig;

			} else { 
				// The ApplicationConfig class is dodgy, so we flag for a recomplie
				$initXMLConfig = True;
				echo "<b>Warning:</b>Cached ApplicationConfig data file seems corrupted ...<br>";
				echo "Trying to recompile the application configuration file: ";
				echo "$configPath/$phpmvcConfigXMLFile<br><br>";
			}
		}


		// Compile this application configuration file
		if($initXMLConfig) {

			//genero phpmvc-config-all.xml con el config base y todos los config de cada modulo
			global $moduleRootDir;
			$modulesPath = $moduleRootDir."WEB-INF/classes/modules";
			$xmlPath = $moduleRootDir."WEB-INF/".$phpmvcConfigXMLFile;
			$fullXmlPath = $moduleRootDir."WEB-INF/phpmvc-config-all.xml";
			$xmlContent = file_get_contents($xmlPath);
			
			$xmlContent = str_replace("</phpmvc-config>", "", $xmlContent);

			$modules = scandir($modulesPath);
			foreach ($modules as $module) {
				if (substr("$module", -1) != "." && $module != ".svn" && is_dir($modulesPath.'/'.$module)) {
					$expectedFile = $moduleRootDir."WEB-INF/classes/modules/".$module."/setup/phpmvc-config-".$module.".xml";
					if (file_exists($expectedFile)) {
						$content = file_get_contents($expectedFile);
						$xmlContent .= $content;
					}
				}
			}
			
			$xmlContent .= "</phpmvc-config>";
			
			file_put_contents($fullXmlPath, $xmlContent);


			// Set the relative path to the application xml configuration file.
			// Something like: './WEB-INF/phpmvc-config.xml'.
			$actionServer->setConfigPath($configPath.'/phpmvc-config-all.xml');

			// Initialise the php.MVC Web application
			// Note: 
			//    1) We may need to include the Digester classes here if the cached Application Config 
			//    data is dodgy.
			//    2) The $appServerRootDir variable is needed in the prepend file.
	
			// Try to figure the path to this framework instance, if not supplied
			if($appServerRootDir == '') {
				// Get the path to this instance of the phpmvc base
				$path = __FILE__;
				preg_match("/^(.*)web-inf.*$/i", $path, $regs);
				$phpmvcRoot  = $regs[1];							// Like: 'C:/Www/phpmvc-base/'
				$phpmvcRoot = substr($phpmvcRoot, 0, -1); 	// Drop the trailing slash
				$appServerRootDir = $phpmvcRoot;	
			}

			// Guess the XML Digester prepend class file if not supplied.
			if($globalPrependXML == '') {
				$globalPrependXML = 'GlobalPrependXMLEx.php';
			}

			// Check if the file exists, else bale out.
			if(!file_exists($appServerRootDir.'/WEB-INF/'.$globalPrependXML)) {
				echo "<b>Warning:</b> Cannot find the XML prepend file : <br>".
				$appServerRootDir.'/WEB-INF/'.$globalPrependXML."<br>".
				"In BootUtils::loadAppConfig(...)<br>";
				return;
			}

			// Load the xml Digester classes (required to initialise)
			include_once $appServerRootDir.'/WEB-INF/'.$globalPrependXML;

			// Initialise the php.MVC config classes
			$oApplicationConfig = $actionServer->init($appServerConfig);

			// Serialise the config data
			$strConfig = serialize($oApplicationConfig);
			$fp = fopen($configPath.'/'.$phpmvcConfigDataFile, 'w');
			fputs($fp, $strConfig);
			fclose($fp);

		}

		return $oApplicationConfig;

	}


	/**
	* Retrieve the 'action path' part of the request URI
	* <p>Returns the 'action path' eg. "logonForm", or NULL id no 'action path'
	* is found
	*
	* @param _REQ_VARS Array	The HTTP GET or POST vars
	* @param string				The 'action' identifier. Eg 'do', as in 
	*									"index.php?do=logonForm"
	* @param integer				The minimum allowable 'action path' string length [1]
	* @param integer				The maximum allowable 'action path' string length [35]
	* @public
	* @returns string
	*/
	function getActionPath($_REQ_VARS, $actionID='do', $actPathMin=1, $actPathMax=35) {

		// Allow safe characters only
		$pattern = '/^[a-z0-9_]{'.$actPathMin.','.$actPathMax.'}$/i';

		foreach($_REQ_VARS as $varName => $varVal) {

			if($varName != '' && $varName == $actionID) {
				if( preg_match($pattern, $varVal) ) {
					return $varVal;
				}
			}
		}

		return NULL;

	}

}
?>