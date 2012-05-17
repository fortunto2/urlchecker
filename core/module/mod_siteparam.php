<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 08.02.12
 * Time: 2:31
 * To change this template use File | Settings | File Templates.
 */
 
class ModSiteparam
{
   private $CORE;

   function __construct(){
       global $CORE;
       $this->CORE=$CORE;
   }
   function igood($url){
	$file = @file_get_contents('http://xml.solomono.ru/?url='.$url);
	$count="0/0";
	if (!empty($file)){
		$file=@file_get_contents('http://xml.solomono.ru/?url='.$url);
		$doc = new DOMDocument();
		$doc->loadXML($file);
		$count=$doc->getElementsByTagName('igood')->item(0)->nodeValue;
	}
    else $count="0/0";
	return $count;
    }
}