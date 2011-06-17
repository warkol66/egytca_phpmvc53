<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     checked
 * Purpose:  Devuelve el checked="checked" si el valor1 es igual a valor2
 * -------------------------------------------------------------
 */
function smarty_modifier_checked_if_has_access($value1,$value2){
	if ((intval($value1) & intval($value2)) > 0 )
		return 'checked="checked"';
}
