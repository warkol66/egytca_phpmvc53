<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     security_has_access
 * Purpose:  Tell if a user have access to an action
 * -------------------------------------------------------------
 */
function smarty_modifier_security_has_access($action) {

	$access = false;

	if (isset($_SESSION["loginUser"]) && is_object($_SESSION["loginUser"]) && get_class($_SESSION["loginUser"]) == "User")
		$user = $_SESSION["loginUser"];
	else if (isset($_SESSION["loginAffiliateUser"]) && is_object($_SESSION["loginAffiliateUser"]) && get_class($_SESSION["loginAffiliateUser"]) == "AffiliateUser")
		$user = $_SESSION["loginAffiliateUser"];
	else if (isset($_SESSION["loginRegistrationUser"]) && is_object($_SESSION["loginRegistrationUser"]) && get_class($_SESSION["loginRegistrationUser"]) == "RegistrationUser")
		$user = $_SESSION["loginRegistrationUser"];

	if (isset($user) && get_class($user) == "User") {
		//Si es supervisor, el usuario tiene acceso
		if ($user->isSupervisor())
			return true;
	}

	if (preg_match('/^([a-z]*)[A-Z]/',$action,$regs))
		$moduleRequested = $regs[1];

	$securityAction = SecurityActionPeer::getByNameOrPair($action);
	$securityModule = SecurityModulePeer::get($moduleRequested);

	//Controlo las acciones y modulos que no requieren login
	//Si no se requiere login $noCheckLogin va a ser igual a 1
	$noCheckLogin = 1;
	if (!empty($securityAction))
		$noCheckLogin = $securityAction->getOverallNoCheckLogin();
	else if (!empty($securityModule))
		$noCheckLogin = $securityModule->getNoCheckLogin();
	else
		$noCheckLogin = 0;

	if (ConfigModule::get("global","noCheckLogin") || ConfigModule::get("global","noSecurity") || $noCheckLogin)
		return true;

	if (!empty($user)) {
		if (!empty($securityAction))
			$access = $securityAction->getAccessByUser($user);
		else if (!empty($securityModule))
			$access = $securityModule->getAccessByUser($user);
	}
	
	return $access;
}
