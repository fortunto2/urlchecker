<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 20.04.12
 * Time: 9:37
 * To change this template use File | Settings | File Templates.
 */
//экспорт подписчиков на unisender

if(!defined('MAIN_DIR')) die('hack');
include_once(MAIN_DIR . "/core/inc/index/onestr_start.php");

$Template->assign("MenuAction",array("mode"=>"import","do"=>"unisender"),'MenuAction'); //меню
$Template->assign("text",array("text"=>"Делаем импорт подписчиков на unisender"),"PageContent");

include_once(MAIN_DIR . "/core/inc/index/onestr_end.php");