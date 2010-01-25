<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     si_no
 * Purpose:  Pasa un 0 a No y un 1 a Si
 * -------------------------------------------------------------
 */
function smarty_modifier_pubDate($fecha_hora)
{
	$ano = substr($fecha_hora,0,4);
	$mes = substr($fecha_hora,5,2);
	$dia = substr($fecha_hora,8,2);
	$hora = substr($fecha_hora,11,2);
	$minuto = substr($fecha_hora,14,2);
	$segundo = substr($fecha_hora,17,2);
	$fecha = date("D, j M Y H:i:s", mktime($hora, $minuto, $segundo, $mes, $dia, $ano));
	$fecha = $fecha." +0000";
	return $fecha;
}

?>
