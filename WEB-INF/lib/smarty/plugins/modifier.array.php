<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty array modifier plugin
 *
 * Type:     modifier<br>
 * Name:     array<br>
 * Date:     Aug 12, 2011
 * Purpose:  fill an array
 * Input:    string to catenate
 * Example:  {assign var=myArray value='25,50,100'|array}
 *           {assign var=myArray value='25,50,100'|array:"valuekey"}
 *           {assign var=myArray value='=,ten=10,twentyfive=25,fifty=50'|array:"key"}
 *           {assign var=myArray value='=,ten=10,twentyfive=25,fifty=50'|array:"assoc"}
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_array($string, $type = "simple") {

	$array = Array();

  switch ($type) {
		case "simple": //Values in an array
			$array = explode(',', $string);
			return $array;
      break;
	
		case "valuekey": //Values in an array with same value as key
			$array = explode(',', $string);
			return array_combine($array, $array);
      break;
	
		case "key": //Values in an array with key and value separated by '='
			$array = explode(',', $string);
			$asoc_array = Array();
			foreach ($array as $row) {
				$values = explode('=', $row);
				$asoc_array[$values[0]] = $values[1];
			}
			return $asoc_array;
      break;

		case "assoc": //Assoc array 
			$array = explode(',', $string);
			$asoc_array = Array();
			foreach ($array as $row)
				$asoc_array[] = explode('=', $row);

			return $asoc_array;
      break;

	}
}
