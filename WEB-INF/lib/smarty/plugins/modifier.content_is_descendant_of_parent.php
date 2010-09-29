<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {modifier_content_is_descendant_of_parent} modifier
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
function smarty_modifier_content_is_descendant_of_parent($contentId,$parentId)
{

	$content = new Content();
	
	return $content->isContentDescendant($parentId,$contentId);

}

/* vim: set expandtab: */

?>