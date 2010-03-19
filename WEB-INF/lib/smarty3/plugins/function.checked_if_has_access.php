<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {has_access} function plugin
 *
 * Type:     function<br>
 * Name:     has_access<br>
 * Purpose:  And bit a bit of two numbers
 * @author Damian Martinelli
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_checked_if_has_access($params, &$smarty)
{
    if ((intval($params['first']) & intval($params['second'])) > 0 )
			return ' checked="checked"';

		return '';

}

/* vim: set expandtab: */

?>
