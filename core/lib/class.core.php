<?php
class core{
    var $cfg; //Конфиги системы
    var $db; //Соединение с базой
    var $prefix; //Префикс таблиц
    var $start_script; //время начала генирации страницы
    var $msg=array();
    function __construct($cfg,$db){
        $this->cfg=$cfg;
        $this->db=$db;
    }
    function format_microtime($tmp=''){
        if($tmp=='') $tmp=microtime();
        $tmp=explode(" ",$tmp);
        return $tmp[1].$tmp[0];
    }
    function setMsg($text,$mode='error'){
        $this->msg[$mode][]=trim($text);
    }
    function getMsg($mode="error"){
        if(isset($this->msg[$mode])){
            return $this->msg[$mode];
        }
        return array();
   }
    function getFullTableName($name){
           return "`".$this->prefix.$name."`";
    }
    function dbConnect(){
        $typeSql=$this->cfg->get('db');
		$option=$this->cfg->get('debug');
		if($option['status']==true){
			$option=$option[$typeSql];
		}else{
			$option=$this->cfg->get($typeSql);
		}
        $this->prefix=$option['prefix'];
        $this->db->connect($option['server'],$option['user'], $option['password'], $option['database']);
    }

    function src($src){
         $src=str_replace($this->cfg->get('imagePath'),"",$src);
         $w=200;
         $h=0;
         $folder=($w>0)?($w.'w'.(($h>0)?('-'.$h.'h'):'')):(($h>0)?($h.'h'):'');
         $src=$this->cfg->get('imgCache').$folder."/".$src;
        return $src;
    }
    
    function getField($data,$ret=''){
        $text=$ret;
        if(is_array($data)){
            $text=str_replace(array("\r\n","\n","\r"), '',trim(implode(" ",$data)));
        }else{
            $text=str_replace(array("\r\n","\n","\r"), '',trim($data));
        }
        if(($text=='' && $ret!='') || ord($text)==194){ //Почему-то нокогири вместо &nbsp; возвращает 194 символ
            $text=$ret;
        }
        return $text;
    }
    function workCurl($url,$data='',$post=0,$cookiekey=''){
	    $ch = curl_init ();

        if($post==0){
            curl_setopt($ch, CURLOPT_URL, $url."?".$data);
		    curl_setopt($ch, CURLOPT_POST, 0);
        }else{
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
		curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, MAIN_DIR.'/assets/cache/cookies1.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, MAIN_DIR.'/assets/cache/cookies1.txt');
        if($cookiekey!=''){
            curl_setopt ($ch, CURLOPT_COOKIE, $cookiekey);
        }
        $data=curl_exec($ch);
		curl_close($ch);
        
        return $data;
   }
}
?>