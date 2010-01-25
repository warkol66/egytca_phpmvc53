<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/globalPrepend.php,v 1.13 2006/05/17 22:45:50 who Exp $
* $Revision: 1.13 $
* $Date: 2006/05/17 22:45:50 $
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

// Legacy applications will link to this prepend file (globalPrepend.php).
// Including the new 'GlobalPrependXXX' files here should keep them happy.

include_once 'GlobalPrependEx.php';
include_once 'GlobalPrependXMLEx.php';

/*
// REQUIRED BASE CLASS FILES

include_once 'PhpMVC_Log.php';	// Class Log is depreciated. Use class PhpMVC_Log instead.
#include_once 'Log.php';			// Loading Log class only for legacy applications

include_once 'ActionDispatcher.php';
include_once 'FileUtils.php';
include_once 'BootUtils.php';
include_once 'HelperUtils.php';
include_once 'PhpBeanUtils.php';
include_once 'RequestUtils.php';
include_once 'Format.php';
include_once 'MessageFormat.php';
include_once 'Locale.php';
include_once 'MessageResources.php';
include_once 'PropertyMessageResources.php';

include_once 'HttpAppServer.php';
include_once 'RequestBase.php';
include_once 'HttpRequestBase.php';
include_once 'ResponseBase.php';
include_once 'HttpResponseBase.php';

include_once 'Action.php';
include_once 'ActionForm.php';
include_once 'ActionServer.php';
include_once 'RequestProcessor.php';
include_once 'ActionMessage.php';
include_once 'ActionError.php';
include_once 'ActionMessages.php';
include_once 'ActionErrors.php';
include_once 'ForwardAction.php';
include_once 'DispatchAction.php';
include_once 'LookupDispatchAction.php';

include_once 'AppServerConfig.php';
include_once 'AppServerContext.php';

include_once 'ActionConfig.php';
include_once 'ControllerConfig.php';
include_once 'ForwardConfig.php';
include_once 'FormBeanConfig.php';
include_once 'ViewResourcesConfig.php';
include_once 'ApplicationConfig.php';
include_once 'DataSourceConfig.php';


// OPTIONAL CLASS FILES (Include as required)
// For best performance comment-out unused class files.

// PEAR
// Comment out the PEAR includes if you are using external PEAR libraries
include_once 'WEB-INF/lib/pear/DB/mysql.php';
include_once 'WEB-INF/lib/pear/DB/mssql.php';
include_once 'WEB-INF/lib/pear/DB.php';
include_once 'WEB-INF/lib/pear/PEAR.php';
include_once 'WEB-INF/classes/phpmvc/dbcp/PearMysqlDataSource.php';
include_once 'WEB-INF/lib/pear/HTTP_Upload/Upload.php'; 

// ADODB
// Comment out the ADODB includes if you are using external ADODB libraries
include_once 'WEB-INF/lib/adodb/adodb.inc.php';
include_once 'WEB-INF/lib/adodb/drivers/adodb-mysqlt.inc.php';
include_once 'WEB-INF/lib/adodb/drivers/adodb-access.inc.php';
include_once 'WEB-INF/classes/phpmvc/dbcp/AdodbDataSource.php';

// PlugIns
include_once 'WEB-INF/classes/phpmvc/plugins/APlugIn.php';
include_once 'WEB-INF/classes/phpmvc/plugins/SmartyPlugInDriver.php';
include_once 'WEB-INF/lib/smarty/Smarty.class.php';

// File Uploading
include_once 'WEB-INF/classes/phpmvc/upload/UploadedFile.php';
include_once 'WEB-INF/classes/phpmvc/upload/MultiPartUploadFileHandler.php';

// php.MVC Tags
include_once 'WEB-INF/lib/phpmvc_tags/PhpMVC_Tags.php';
include_once 'WEB-INF/lib/phpmvc_tags/TagManager.php';
include_once 'WEB-INF/classes/phpmvc/utils/TagActionDispatcher.php'; 


///// Required also
// RulesManager
include_once 'WEB-INF/lib/collections/HashMap.php';
// Actionserver::initApplicationDataSources()
include_once 'WEB-INF/classes/phpmvc/dbcp/BasicDataSource.php';

///// XML Digester class files
include_once 'WEB-INF/lib/digester/AbstractSAXParser.php';
include_once 'WEB-INF/lib/digester/SaxParser.php';
include_once 'WEB-INF/lib/digester/Rule.php';
include_once 'WEB-INF/lib/digester/Rules.php';
include_once 'WEB-INF/lib/digester/RulesManager.php';
include_once 'WEB-INF/lib/digester/ObjectCreateRule.php';
include_once 'WEB-INF/lib/digester/SetPropertyRule.php';
include_once 'WEB-INF/lib/digester/SetPropertiesRule.php';
include_once 'WEB-INF/lib/digester/SetNextRule.php';
include_once 'WEB-INF/lib/digester/Digester.php';
include_once 'WEB-INF/lib/digester/RuleSet.php';
include_once 'WEB-INF/lib/digester/RuleSetBase.php';
include_once 'WEB-INF/classes/phpmvc/config/ConfigRuleSet.php';


// We can check if ConfigRuleSet has been loaded in our ".../module/WEB-INF/prepend.php"
define('CONFIGRULESET', "1");
*/
?>