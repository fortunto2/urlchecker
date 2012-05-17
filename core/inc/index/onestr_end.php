<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 20.04.12
 * Time: 12:03
 * To change this template use File | Settings | File Templates.
 */

if($CORE->cfg->get('debug','status')==true){
    global $start_script;
    $Template->assign("BlockDebugOut",array('title'=>'Ошибки','msg'=>$CORE->getMsg("error")),'ErrorDebugOut');
    $Template->assign("BlockDebugOut",array('title'=>'Уведомления','msg'=>$CORE->getMsg("notice")),'NoticeDebugOut');

    $Template->assign("BlockDebugOut",array('title'=>'SQL запросы','msg'=>$CORE->db->sql),'SqlDebugOut');

    $debug=array();
    $debug[].="Число запросов в базу: ".$CORE->db->getCountSql('db');
    $debug[].="Число запросов из кеша: ".$CORE->db->getCountSql('cache');
    $debug[].="Время генирации страницы: ".($CORE->format_microtime()-$CORE->start_script);
    $Template->assign("BlockDebugOut",array('title'=>'Отладка','msg'=>$debug),'InfoDebugOut');
    
    $Template->assign("DebugOut",array(),'DebugOut');
}
echo $Template->render("index");