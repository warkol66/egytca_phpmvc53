<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {popup} function plugin
 *
 * Type:     function<br>
 * Name:     popup<br>
 * Purpose:  make text pop up in windows via overlib
 * @link http://smarty.php.net/manual/en/language.function.popup.php {popup}
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_js_char_counter($params, &$smarty)
{
	if ($params['object'] && is_object($params['object']) && !$params['textSize']) {
		$object = $params['object'];
		$peerClass = get_class($object->getPeer());
		$tableMap = call_user_func(array($peerClass, 'getTableMap'));
		$column = $tableMap->getColumn($params['columnName']);
		$textSize = $column->getSize();
	}
	else if ($params['textSize'])
		$textSize = $params['textSize'];
	else
		return;

	$fieldName = $params['fieldName'];
	$idRemaining = $params['idRemaining'];

	if ($params['sizeRemaining'])
		$sizeRemaining = $params['sizeRemaining'];
	else
		$sizeRemaining = 3;

	if ($params['classRemaining'])
		$classRemaining = $params['classRemaining'];
	else
		$classRemaining = "charCount";

	$title = $params['title'];

	$retval = "<input type=\"text\" disabled=\"disabled\" id=\"$idRemaining\" size=\"$sizeRemaining\" value=\"0\" title=\"$title\" alt=\"$title\" class=\"$classRemaining\" />\n";
	$retval.= "<script type=\"text/javascript\">var $idRemaining" . "charCount = new TextCounter('$fieldName', '$idRemaining', $textSize)</script>\n";
	


  return $retval;
}

/* vim: set expandtab: */

?>
