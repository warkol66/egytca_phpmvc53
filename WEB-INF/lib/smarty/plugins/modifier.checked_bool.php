<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     checked_bool
 * Purpose:  Devuelve el checked="checked" si el valor es 1
 * -------------------------------------------------------------
 */
function smarty_modifier_checked_bool($value){
	if ($value == 1)
		return 'checked="checked"';
}
