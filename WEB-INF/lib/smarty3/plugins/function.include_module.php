<?php

function explode_assoc($glue1, $glue2, $array)
{
  $array2=explode($glue2, $array);
  foreach($array2 as  $val)
  {
            $pos=strpos($val,$glue1);
            $key=substr($val,0,$pos);
            $array3[$key] =substr($val,$pos+1,strlen($val));
  }
  return $array3;
}

//usage:
//$str="key1=val1&key2=val2&key3=val3";
//$array=explode_assoc('=','&',$str);


function smarty_function_include_module($params, &$smarty)
{  
    $module = $params['module'];
    $action = $params['action'];
    $optionsString = $params['options'];
    $options = explode_assoc('=','&',$optionsString);
    $include = $params['module']."Include";
    
    //include la clase include correspondiente y obtengo su resultado en $result
    require_once($include.".php");
    $includeObject = new $include();
    $method = "get".$action;
    $result = $includeObject->$method($options);
    $smarty->assign("result",$result);
    
    //Debo cambiarle el outputfilter para poder usar otro external
    $smartyOutputFilter = new SmartyOutputFilter();
    $smartyOutputFilter->template = 'TemplateInclude.tpl';
    $oldSmartyOutputFilter = $smarty->_plugins['outputfilter']['SmartyOutputFilter_smarty_add_template'][0];
    $smarty->register_outputfilter(array($smartyOutputFilter,"smarty_add_template"));
    
    //Si esta vacio el template opcional, debo buscar el template en el forward del action
    if (empty($options['template'])) {
        $vars = $smarty->get_template_vars();
        $mapping = $vars["mapping"];
        $applicationConfig = $mapping->getApplicationConfig();
        $actionPath = $module.$action;
        $actionPath[0] = strtolower($actionPath[0]);
        $actionConfig = $applicationConfig->findActionConfig($actionPath);
        if (!empty($_SESSION["loginUser"])) {
            $smarty->assign("loginUser",$_SESSION["loginUser"]);
            $forwardConfig = $actionConfig->findForwardConfig("includeLogged"); 
        }
        else {
            $forwardConfig = $actionConfig->findForwardConfig("includeNotLogged"); 
        }
        if (empty($forwardConfig))
            $forwardConfig = $actionConfig->findForwardConfig("include"); 
   
        //obtengo el template
        $template = $forwardConfig->getPath();
    } else
        $template = $options['template'];
        
    //Obtengo el html resultante
		if( !$smarty->template_exists($template) ){
			echo "NO EXISTE TEMPLATE: '".$template."'.";
		}
    $html_result = $smarty->fetch($template); 
    
    //vuelvo a poner el viejo outputfilter de antes
    $smarty->register_outputfilter($oldSmartyOutputFilter);

    return $html_result;
}

/* vim: set expandtab: */
