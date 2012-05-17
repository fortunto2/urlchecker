<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 20.04.12
 * Time: 12:02
 * To change this template use File | Settings | File Templates.
 */
if(!defined('MAIN_DIR'))  die('hack');

global $Template,$CORE,$Page;

require_once(MAIN_DIR."/core/module/mod_analitic.php");
$ModAnalitic= new ModAnalitic();

$Template->assign("text",array('text'=>$ModAnalitic->SiteInDB()),'SiteInDB');

$Template->assign("text",array('text'=>$CORE->cfg->get('author')),'AuthorScript');
$Template->assign("text",array('text'=>$CORE->cfg->get('authorURL')),'AuthorURL');
$Template->assign("text",array('text'=>$CORE->cfg->get('version')),'VersionScript');



