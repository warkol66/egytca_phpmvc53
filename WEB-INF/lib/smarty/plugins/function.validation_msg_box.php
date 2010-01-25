<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {validation_msg_box} function plugin
 *
 *
 * Helper que crea la caja de mensaje donde se emiten los mensajes de una validacion
 *
 * Type:     function<br>
 * Name:     validation_msg_box<br>
 * Purpose:  Helper de Validacion
 * @author Martin Ramos Mejia
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_validation_msg_box($params, &$smarty)
{
    
	$idField = $params['idField'];
	$output = "<span id='". $idField ."_box'></span>";
	
	return $output;

}

/* vim: set expandtab: */

?>