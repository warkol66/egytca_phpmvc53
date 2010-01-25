<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {javascript_onchange_validation_attribute} function plugin
 *
 *
 * Helper que permite crear el atributo onChange para la validacion de un input de html via javascript.
 *
 * Type:     function<br>
 * Name:     javascript_onchange_validation_attribute<br>
 * Purpose:  Helper de Validacion
 * @author Martin Ramos Mejia
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_javascript_onchange_validation_attribute($params, &$smarty)
{
 
	$idField = $params['idField'];
	$output = "onchange='javascript:validationValidateField(". '"' . $idField. '"' . ");'";
   	
	return $output;

}

/* vim: set expandtab: */

?>