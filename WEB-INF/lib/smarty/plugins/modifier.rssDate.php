<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     smarty_modifier_rssDate
 * Purpose:  Formatea fecha a DATE_RSS
 * @param string (date in format YYYY-MM-DD HH:MM:SS)
 * -------------------------------------------------------------
 */
function smarty_modifier_rssDate($date_time)
{
	$dateYear   = substr($date_time,0 ,4);
	$dateMonth  = substr($date_time,5 ,2);
	$dateDay    = substr($date_time,8 ,2);
	$dateHour   = substr($date_time,11,2);
	$dateMinute = substr($date_time,14,2);
	$dateSecond = substr($date_time,17,2);
	$formatedDate = date(DATE_RSS, mktime($dateHour, $dateMinute, $dateSecond, $dateMonth, $dateDay, $dateYear));
	return $formatedDate;
}
