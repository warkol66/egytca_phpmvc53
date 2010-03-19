<?php

require_once('TimezonePeer.php');

/*
 * Smarty plugin - Modificador de Timezone de Datetime
 *
 * Toma un datetime en GMT-0 y lo lleva a la zona horaria correspondiente al perfil del usuario, o si no ha definido 
 * zona horaria a la de sistema
 *
 * @param string datetime en GMT-0
 * @param string datetime en la zona horaria correspondiente a la configuracion del sistema.
 *
 */
function smarty_modifier_change_timezone($datetime) {

	if (Common::isAdmin()) {
		//es el caso de un usuario administrador el valor de su perfil
		$user = Common::getAdminLogged();
		$timezoneCode = $user->getTimezone();		
		
	}

	if (Common::isAffiliatedUser()) {
		//es el caso de un usuario affiliado el valor de su perfil

		$user = Common::getAffiliatedLogged();
		$timezoneCode = $user->getTimezone();		
		
	}

	if (empty($timezoneCode) || $timezoneCode == "") {		
		//si no hubiera o no fuera un usuario administrador tomamos default de la aplicacion
		global $system;
		$timezoneCode = $system["config"]["system"]["parameters"]["applicationTimeZoneGMT"]["value"];
				
		//solucion por el problema del pasaje de XML a array de PHP
		//que toma el 0 como null
		if ($timezoneCode == null)
			$timezoneCode = 0;

	}

	
	$timezonePeer = new TimezonePeer();
	
	return $timezonePeer->getGMT0TimeOnTimezone($datetime,$timezoneCode);

}
?> 
