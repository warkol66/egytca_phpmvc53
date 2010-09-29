<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {ajax_onchange_validation_attribute} function plugin
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
function smarty_function_content_is_descendant_of_parent($params, &$smarty)
{

	$parentId = $params['parentId'];
	$id = $params['id'];

	$content = new Content();
	var_dump($content->isContentDescendant($parentId,$id));
	return $content->isContentDescendant($parentId,$id);

}

/* vim: set expandtab: */

?>