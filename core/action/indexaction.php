<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 20.04.12
 * Time: 9:36
 * To change this template use File | Settings | File Templates.
 */
if(!defined('MAIN_DIR')) die('hack');
include_once(MAIN_DIR . "/core/inc/index/onestr_start.php");

$Template->assign("MenuAction",array(),'MenuAction'); //меню

$data=array('text'=>'');

$Template->tag("h2",array('text'=>"Анализ качества доноров"),"Pagetitle");

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['urllist'])){
    //Чекаем урлы на верность (формат)
    $url=array();
    $postvalue=explode("\n",trim($_POST['urllist']));
    foreach($postvalue as $item){
        $flag=$Template->checkUrl($item);
        if($flag!==false){
            $url[]=$flag;
        }
    }
    if(count($url)>0){
        if(count($url)!=count($postvalue)){
            $tmp=count($postvalue)-count($url);
            $data['text'].=$Template->template("warngNotice",'Количество сайтов не принятых на анализ: '.$tmp);
        }
        $data['text'].=$Template->template("TableFilter",$url);
    }else{
        $data['text'].=$Template->template("errorNotice",'Нет данных для анализа');
        $data['text'].=$Template->template("FormFilter",array());
    }


    $Template->assign("text",$data,"PageContent");
    /*
     * 1) Чекаем урлы на валидность (формат урл)
     * 2) Генерим таблицу только с правильными урлами. Иначе выводим форму и ошибку.
     * 3) По наступлению события onload начинаем чекать урлы
     *      а) проверка доступности
     *      б) Страниц в индексе
     *      в) Возраст домена
     *      г) iGood
     *      д) Посещалка
     *  5) По заершению проверки выводится кнопка далее и на странице выводятся только все подходящие урлы
     */

}else{
    $Template->assign("FormFilter",array(),"PageContent");
}
include_once(MAIN_DIR . "/core/inc/index/onestr_end.php");