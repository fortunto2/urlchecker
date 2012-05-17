<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 10.02.12
 * Time: 12:07
 * To change this template use File | Settings | File Templates.
 */
 
class config {
   var $value=array();
   function __construct($config){
       if(is_array($config)){
           foreach($config as $item=>$val){
               $this->value[$item]=$val;
           }
       }
   }

   public function get($name,$subname=''){
       if(isset($this->value[$name]) && $subname==''){
           return $this->value[$name];
       }elseif($subname!='' && isset($this->value[$name][$subname])){
            return $this->value[$name][$subname];
       }else{
           return '';
       }
   }
    public function set($name,$val){
        $this->value[$name]=$val;
        return $this->value[$name];
    }
}
