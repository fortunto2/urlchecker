<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 07.02.12
 * Time: 23:41
 * To change this template use File | Settings | File Templates.
 */
include_once('config.php');

if(!defined('MAIN_DIR')) die('hack');
session_start();
$start_script=microtime();
require_once(MAIN_DIR."/core/lib/class.config.php");
require_once(MAIN_DIR."/core/lib/class.core.php");
require_once(MAIN_DIR."/core/lib/class.url.php");
require_once(MAIN_DIR."/core/lib/class.template.php");
require_once(MAIN_DIR."/core/lib/class.page.php");
require_once(MAIN_DIR."/core/lib/db/class.".$config['db'].".php");
require_once(MAIN_DIR."/core/lib/class.action.php");

$CORE = new core(
    new config($config),
    new DB
); //Ядро системы
$CORE->start_script=$CORE->format_microtime($start_script);
$CORE->dbConnect();

//$Filter = new Class_Filter;
$Page = new Page();
$Template = new Class_Template(&$Page);
$Template->cpu=$CORE->cfg->get('cpu');
$Template->fullurl=$CORE->cfg->get('fullurl');
$Template->route=$CORE->cfg->get('route');

$Action=new Action();
