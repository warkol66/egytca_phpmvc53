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
	$loginUser = $_SESSION["loginUser"];

	$loginUserAffiliate = $_SESSION["loginAffiliateUser"];

	$loginUserRegistration = $_SESSION["loginRegistrationUser"];

	if (!empty($loginUserAffiliate))
		$user = $loginUserAffiliate;

	if (!empty($loginUser)) {
		$user = $loginUser;	
		//Si es supervisor, el usuario tiene acceso
		if ($user->isSupervisor())
			return true;
	}

	if (!empty($loginUserRegistration))
		$user = $loginUserRegistration;				
	
	$actualAction = SecurityActionPeer::getByNameOrPair($action);	

	//si el action no existe, el usuario no tiene acceso
	if (empty($actualAction))
		return false;
	
	$hasAccess = $actualAction->hasAccess($user);
	
	return $hasAccess;
}

?>
