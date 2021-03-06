<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {js_char_counter} function plugin
 *
 * Type:     function<br>
 * Name:     js_char_counter<br>
 * Purpose:  use the textCounter
 * Example:
 * |-js_char_counter object=$issue columnName="name" fieldName="params[name]" idRemaining="remaining" sizeRemaining="3" classRemaining="charCount" showHide=1 useSpan=1-|
 * @author   Egytca
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

	$title = $params['counterTitle'];

	if ($params['showHide']) {
		$retval['pre'] = "onFocus=\"switch_vis('$idRemaining','inline')\" onBlur=\"switch_vis('$idRemaining','none')\"";
		$showHide = "none";
	}
	else
		$showHide = "inline";

	if ($params['useSpan'])
		$retval['pos'] = "<span id=\"$idRemaining\" title=\"$title\" alt=\"$title\" class=\"$classRemaining\" /></span>\n";
	else
		$retval['pos'] = "<input type=\"text\" disabled=\"disabled\" id=\"$idRemaining\" size=\"$sizeRemaining\" value=\"0\" title=\"$title\" alt=\"$title\" class=\"$classRemaining\" />\n";

	$retval['pos'] .= "<script type=\"text/javascript\">var $idRemaining" . "charCount = new TextCounter('$fieldName', '$idRemaining', $textSize, '$showHide');</script>";

	return $smarty->assign("Counter",$retval);

}
