<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     security_user_has_access
 * Purpose:  Tell if a user have access to an action
 * -------------------------------------------------------------
 */
function smarty_modifier_security_user_has_access($action)
{

	if (isset($_SESSION["loginUser"]))
		$loginUser = $_SESSION["loginUser"];
	else if (isset($_SESSION["loginAffiliateUser"]))
		$loginUserAffiliate = $_SESSION["loginAffiliateUser"];
	else if (isset($_SESSION["loginRegistrationUser"]))
		$loginRegistrationUser = $_SESSION["loginRegistrationUser"];

	if (!empty($loginUserAffiliate))
		$user = $loginUserAffiliate;

	else if (!empty($loginUserRegistration))
		$user = $loginUserRegistration;				

	if (!empty($loginUser)) {
		$user = $loginUser;	
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

	if (ConfigModule::get("global","noCheckLogin") || $noCheckLogin)
		return true;

	if (!empty($loginUser) || !empty($loginUserAffiliate) || !empty($loginRegistrationUser)) {
		if (!empty($securityAction))
			$access = $securityAction->getAccessByUser($user);
		else if (!empty($securityModule))
			$access = $securityModule->getAccessByUser($user);
	}
	
	return $access;
}
