<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {multilang__multilang_actionLabel_translation} modifier plugin
 *
 * Type:     function<br>
 * Name:     multilang_get_text<br>
 * Purpose:  Obtiene un texto en un idioma especifico
 * @author Damian Martinelli
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_modifier_multilang_get_actionLabel_translation($action)
{

	$languageCode = Common::getCurrentLanguageCode();

	$translation = SecurityActionLabelPeer::getByActionAndLanguage($action,$languageCode);
		
	if (!empty($translation))
		return $translation->getLabel();
	return $action;

}
