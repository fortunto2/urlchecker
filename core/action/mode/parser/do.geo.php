<?php
if(!defined('MAIN_DIR'))  die('hack');
global $Template,$CORE,$Page;

$out=array('error'=>'','value'=>0);
if(isset($_GET['url']) && $Template->checkUrl('http://'.$_GET['url'])){
   $out['value']='21/1555';
}else{
    $out['error']='error url';
    $out['value']=0;
}
echo json_encode($out);