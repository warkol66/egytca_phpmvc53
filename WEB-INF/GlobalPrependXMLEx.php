<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/GlobalPrependXMLEx.php,v 1.2 2006/02/22 08:13:14 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:13:14 $
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
$fAbsolutePath = NULL;
$fAbsolutePath = ClassPath::absolutePath($appServerRootDir);
if($fAbsolutePath == False) {
	// RED-ALERT - Kill this process now
	print "<b>ALERT: The AppServer path is not valid. Aborting ...</b><br>";
	exit;
}

///// Required also
// RulesManager
include_once $appServerRootDir.'/WEB-INF/lib/collections/HashMap.php';
// Actionserver::initApplicationDataSources()
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/dbcp/BasicDataSource.php';

///// XML Digester class files
include_once $appServerRootDir.'/WEB-INF/lib/digester/AbstractSAXParser.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/SaxParser.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/Rule.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/Rules.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/RulesManager.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/ObjectCreateRule.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/SetPropertyRule.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/SetPropertiesRule.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/SetNextRule.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/Digester.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/RuleSet.php';
include_once $appServerRootDir.'/WEB-INF/lib/digester/RuleSetBase.php';
include_once $appServerRootDir.'/WEB-INF/classes/phpmvc/config/ConfigRuleSet.php';

?>