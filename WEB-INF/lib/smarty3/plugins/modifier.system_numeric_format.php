<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     system_numeric_format
 * Purpose:  Pasa un numero decimal traido de la base de datos,
 * 			 al formato establecido en la configuracion del sistema.
 * -------------------------------------------------------------
 */
function smarty_modifier_system_numeric_format($number,$decimals = null) {

		global $system;
		
		$thousandsSeparator = $system['config']['system']['parameters']['thousandsSeparator'];
		$decimalSeparator = $system['config']['system']['parameters']['decimalSeparator'];
		if (empty($decimals))
			$decimals = $system['config']['system']['parameters']['numberOfDecimals'];

		return number_format($number,$decimals,$decimalSeparator,$thousandsSeparator);
}

?>
