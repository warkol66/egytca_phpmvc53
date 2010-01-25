<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {multilang__get_translation} modifier plugin
 *
 * Type:     function<br>
 * Name:     multilang_get_text<br>
 * Purpose:  Obtiene un texto en un idioma especifico
 * @author Damian Martinelli
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_modifier_multilang_get_translation($text,$moduleName)
{

	$languageCode = Common::getCurrentLanguageCode();

	$originalText = $text;
	
	$text = MultilangTextPeer::getByTextAndModuleNameAndCode($originalText,$moduleName,$languageCode);
		
	if (!empty($text))
		return $text->getText();
	return $originalText;

}
