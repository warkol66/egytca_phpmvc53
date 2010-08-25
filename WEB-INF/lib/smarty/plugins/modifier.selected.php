<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     selected
 * Purpose:  Devuelve el selected="selected" los valores recibidos son iguales
 * -------------------------------------------------------------
 */
function smarty_modifier_selected($value1,$value2){
	if ($value1 === $value2)
		return 'selected="selected"';
	return '';
}
