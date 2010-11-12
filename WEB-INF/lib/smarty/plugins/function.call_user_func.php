<?php

/**
 * Permite llamar funciones php dinámicamente.
 * 
 * @author Axel Sanguinetti
 * 
 * @param $params['callback'] función a ser invocada.
 * @param $params['object'] objeto sobre el cual invocar la función. También puede ser el nombre de una clase.
 * @param $params[...] cualquier cantidad de parámetros adicionales para pasar como argumentos de la función.
 * 
 * @return valor retornado por la función.
 */

function smarty_function_call_user_func($params, &$smarty)
{
	$paramsCopy = $params;
	$callback = $paramsCopy['callback'];
	if (!empty($paramsCopy['object']))
		$callback = array($paramsCopy['object'], $callback);
	unset($paramsCopy['callback']);
	unset($paramsCopy['object']);

	return call_user_func_array($callback, $paramsCopy);
}