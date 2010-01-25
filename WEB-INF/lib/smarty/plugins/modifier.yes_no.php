<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     yes_no
 * Purpose:  Pasa un 0 a No y un 1 a Yes
 * -------------------------------------------------------------
 */
function smarty_modifier_yes_no($nro)
{
	switch ($nro) {
		case 0: return "No";
		case 1: return "Yes";
	}
	return $nro;
}

?>
