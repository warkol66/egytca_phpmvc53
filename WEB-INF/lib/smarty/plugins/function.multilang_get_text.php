<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {multilang_get_text} function plugin
 *
 * Type:     function<br>
 * Name:     multilang_get_text<br>
 * Purpose:  Obtiene un texto en un idioma especifico
 * @author Damian Martinelli
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_multilang_get_text($params, &$smarty)
{
    $moduleName = $params['module'];
	$textId = $params['textId'];
	$languageCode = $params['code'];
	$originalText = $params['text'];
	
	if (!empty($textId))
		$text = MultilangTextPeer::getByIdAndModuleNameAndCode($textId,$moduleName,$languageCode);
	else
		$text = MultilangTextPeer::getByTextAndModuleNameAndCode($originalText,$moduleName,$languageCode);
		
	if (!empty($text))
		return $text->getText();
	return $originalText;

}

/* vim: set expandtab: */

?>
