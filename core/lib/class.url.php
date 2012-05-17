<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 19.04.12
 * Time: 10:35
 * To change this template use File | Settings | File Templates.
 */
 class url{
    var $cpu=false;
    var $route=array();
     var $fullurl=false;
     
     function mainUrl(){
         $url=explode("/",$_SERVER['PHP_SELF']);
         $count=count($url)-1;
         unset($url[$count]);
         $url=implode('/',$url);
         return $url."/";
     }
     function fullUrl($link=''){
         if($this->fullurl){
                return 'http://'.$_SERVER['SERVER_NAME'].$this->mainUrl().$link;
         }else{
             return $this->mainUrl().$link;
         }
     }
        function makeURL($array){
            $url='';
            foreach($array as $item=>$name){
                if($this->cpu==false){
                    if(isset($this->route[$item])){
                        $url[$this->route[$item]]=$item."=".$name;
                    }else{
                        $url[]=$item."=".$name;
                    }
                }else{
                    if(isset($this->route[$item])){
                         $url[$this->route[$item]]=$name;
                    }else{
                        $url[]=$name;
                    }
                }
            }
            ksort($url);
            if($this->cpu==false){
                $url="?".implode("&",$url);
            }else{
                $url=implode("/",$url);
                if($url!=''){
                   $url.="/";
                }
            }
            
            return $this->fullUrl($url);
        }
      public function checkUrl($line){
        $link=parse_url($line);
        if(isset($link['host'])){
            return strtolower($link['host']);
        }
        return false;
    }
}
