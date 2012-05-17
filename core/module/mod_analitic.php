<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 08.02.12
 * Time: 2:31
 * To change this template use File | Settings | File Templates.
 */
 
class ModAnalitic
{
   private $CORE;

   function __construct(){
       global $CORE;
       $this->CORE=$CORE;
   }
    function SiteInDB(){
         $this->CORE->db->query("SELECT count(`id`) FROM ".$this->CORE->getFullTableName('site'));
        return $this->CORE->db->getValue();
    }
}