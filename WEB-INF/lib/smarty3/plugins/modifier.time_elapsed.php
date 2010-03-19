<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     time_elapsed
 * Purpose:  Devuelve la cantidad de horas o días desde una fecha (días por defecto)
 * -------------------------------------------------------------
 */
function smarty_modifier_time_elapsed($date,$mode = "d")
{
	$dateTimestamp = strtotime($date);	
	$seconds_elapsed = gmmktime() - $dateTimestamp;
	switch ($mode) {
		case "h":
			//horas
			$time_elapsed = round($seconds_elapsed / 3600);
			break;
		default:
			//dias
			$time_elapsed = round($seconds_elapsed / 86400);		
	}
	return $time_elapsed;
}

?>
