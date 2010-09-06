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
	if (($value1 == $value2) && (($value1 !== 0 && $value2 !== NULL) && ($value2 !== 0 && $value1 !== NULL)))
		return 'selected="selected"';
	return;
}
