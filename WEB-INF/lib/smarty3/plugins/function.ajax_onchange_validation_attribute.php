<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {ajax_onchange_validation_attribute} function plugin
 *
 *
 * Helper que permite crear el atributo onChange para la validacion de un input de html via AJAX.
 *
 * Type:     function<br>
 * Name:     ajax_onchange_validation_attribute<br>
 * Purpose:  Helper de Validacion
 * @author Martin Ramos Mejia
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_ajax_onchange_validation_attribute($params, &$smarty)
{
    
	$actionName = $params['actionName'];
	$output = "onchange='javascript:validationValidateFieldThruAjax(this,". '"' . $actionName. '"' . ");'";
	
	return $output;

}

/* vim: set expandtab: */

?>