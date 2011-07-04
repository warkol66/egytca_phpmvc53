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
function smarty_function_popup($params, &$smarty)
{
    $append = '';
    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'text':
            case 'trigger':
            case 'function':
            case 'inarray':
                $$_key = (string)$_value;
                if ($_key == 'function' || $_key == 'inarray')
                    $append .= ',' . strtoupper($_key) . ",'$_value'";
                break;

            case 'caption':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'closetext':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'status':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fgcolor':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'bgcolor':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'textcolor':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'capcolor':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'closecolor':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'textfont':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'captionfont':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'closefont':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fgbackground':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'bgbackground':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'caparray':
            case 'capicon':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'background':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'frame':
                $append .= ',' . strtoupper($_key) . ",'$_value'";
                break;

            case 'textsize':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'captionsize':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'closesize':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'width':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'height':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'border':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'offsetx':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'offsety':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'snapx':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'snapy':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fixx':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fixy':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'padx':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'pady':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'timeout':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'delay':
                $append .= ',' . strtoupper($_key) . ",$_value";
                break;

            case 'sticky':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'left':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;
            case 'right':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;
            
            case 'center':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'above':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'below':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'noclose':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'autostatus':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'autostatuscap':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fullhtml':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'hauto':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'vauto':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'mouseoff':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'closetitle':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'followmouse':
                $append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
                break;

            case 'cellpad':
                $append .= ',' . strtoupper($_key) . ",$_value";
                break;
            
            case 'closeclick':
                if ($_value) $append .= ',' . strtoupper($_key);
                break;

            default:
                $smarty->trigger_error("[popup] unknown parameter $_key", E_USER_WARNING);
        }
    }

    if (empty($text) && !isset($inarray) && empty($function)) {
        $smarty->trigger_error("overlib: attribute 'text' or 'inarray' or 'function' required");
        return false;
    }

    if (empty($trigger)) { $trigger = "onmouseover"; }

    $text = htmlspecialchars($text, ENT_QUOTES);

    $retval = $trigger . '="return overlib(\''.preg_replace(array("!'!","![\r\n]!"),array("\'",'\r'),$text).'\'';

    $retval .= $append . ');"';
    if ($trigger == 'onmouseover')
       $retval .= ' onmouseout="nd();"';


    return $retval;
}

/* vim: set expandtab: */

?>
