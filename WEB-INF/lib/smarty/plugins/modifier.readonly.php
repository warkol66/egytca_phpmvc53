<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     checked
 * Purpose:  Devuelve el readonly="readonly" class="readonly" si el valor es show o readonly
 * -------------------------------------------------------------
 */
function smarty_modifier_readonly($value){
	if ($value == "readonly" || $value="showLog")
		return 'readonly="readonly" class="readonly"';
}
