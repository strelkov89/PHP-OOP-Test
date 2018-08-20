<?php

function app_autoloader($class) {		
	$replaceItems = array("app\\", "\\");    
    $class = __DIR__ . str_replace($replaceItems, '/', $class) . '.php';    
	require_once($class);	
}

spl_autoload_register('app_autoloader');