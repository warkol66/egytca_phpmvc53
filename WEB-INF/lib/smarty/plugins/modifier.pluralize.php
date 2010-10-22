<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty capitalize modifier plugin
 *
 * Type:     modifier<br>
 * Name:     pluralize<br>
 * Purpose:  obtiene el plural de un texto en ingles
 * @author   Axel Sanguinetti
 * @param string
 * @return string
 */
function smarty_modifier_pluralize($string) {
    return Common::pluralize($string);
}


?>
