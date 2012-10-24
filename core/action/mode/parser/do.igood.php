<?php
if(!defined('MAIN_DIR'))  die('hack');
global $Template,$CORE,$Page;

require_once(MAIN_DIR."/core/module/mod_siteparam.php");
$ModSiteparam=new ModSiteparam();

$out=array('error'=>'','value'=>0);
if(isset($_GET['url']) && $Template->checkUrl('http://'.$_GET['url'])){
    $tmp=$ModSiteparam->igood($_GET['url']);
    $tmp=explode("/",$tmp);
    $out['value']=$tmp[0]/$tmp[1];
}else{
    $out['error']='error url';
    $out['value']=0;
}
echo json_encode($out);