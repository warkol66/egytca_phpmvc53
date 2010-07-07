<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {javascript_form_validation_button} function plugin
 *
 *
 * Helper que permite crear un boton de submit de un formulario el cual ejecutara la validacion de 
 * javascript del mismo del framework.
 *
 * Type:     function<br>
 * Name:     javascript_form_validation_button<br>
 * Purpose:  Helper de Validacion
 * @author Martin Ramos Mejia
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_javascript_form_validation_button($params, &$smarty){
    
	if (empty($params['value']))
		$buttonName = 'Submit';
	else
		$buttonName = $params['value'];

	if (empty($params['title']))
		$buttonTitle = $buttonName;
	else
		$buttonTitle = $params['title'];

	$output = "<input type='button' value='" . $buttonName . "' title='" . $buttonTitle . "' onClick='javascript:validationValidateFormClienSide(this.form);'>";
	
	return $output;

}
