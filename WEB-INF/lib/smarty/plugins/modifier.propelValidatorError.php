<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     propelValidatorError
 * Purpose:  Devuelve el class="formError" el valor refiere a una columna con error
 * -------------------------------------------------------------
 */
function smarty_modifier_propelValidatorError($objUser,$value){
	$failures = $objUser->getValidationFailures();
	foreach ($failures as $failure) {
		$columnName = explode('.',$failure->getColumn());
		if ($columnName[1] == strtoupper($value))
			return 'class="formError" ';
	}
	return '';
}

