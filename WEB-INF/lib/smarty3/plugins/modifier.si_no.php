<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     si_no
 * Purpose:  Pasa un 0 a No y un 1 a Si
 * -------------------------------------------------------------
 */
function smarty_modifier_si_no($nro)
{
	switch ($nro) {
		case 0: return "No";
		case 1: return "Si";
	}
	return $nro;
}

?>
