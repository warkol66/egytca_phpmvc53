<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     disabled
 * Purpose:  Devuelve el disabled="true" class="readonly" si el valor es show o readonly
 * -------------------------------------------------------------
 */
function smarty_modifier_disabled($value){
	if ($value == "readonly" || $value == "showLog")
		return 'disabled="true" class="readOnly"';
}
