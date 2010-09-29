<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {smarty_modifier_is_descendant} modifier
 *
 *
 * Indica si determinado id ($id) de contenido es descendiente de otro ($parent)
 *
 * Type:     function<br>
 * Name:     ajax_onchange_validation_attribute<br>
 * Purpose:  Helper de Validacion
 * @author Martin Ramos Mejia
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_modifier_is_descendant($contentId,$parentId)
{

	$content = new Content();
	
	return $content->isContentDescendant($parentId,$contentId);

}
