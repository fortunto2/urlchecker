<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 07.02.12
 * Time: 23:39
 * To change this template use File | Settings | File Templates.
 */

//ini_set("zlib.output_compression","On");
//ini_set("zlib.output_compression_level","6");
ini_set("display_errors",1);
set_time_limit(0);
ini_set('max_execution_time', 0);

define("MAIN_DIR", dirname(__FILE__));

$config=array();
$config['debug']=array();

$config['ajax']=false;
$config['db']='mysql';

$config['cpu']=true;//Включить ЧПУ
$config['fullurl']=true; //абсолютный или относительный путь
$config['route']=array('mode'=>1,'do'=>2);//Порядок маршрутизации

$config['debug']['status']=true;
$config['debug']['mysql']['server']='localhost'; //SQL SERVER
$config['debug']['mysql']['user']='root';
$config['debug']['mysql']['password']='';
$config['debug']['mysql']['database']='urlchecker';
$config['debug']['mysql']['prefix']='seo_';

$config['mysql']['server']='';
$config['mysql']['user']='';
$config['mysql']['password']='';
$config['mysql']['database']='';
$config['mysql']['prefix']='geo_';

$config['version']='0.2';
$config['author']='Agel_Nash';
$config['authorURL']='http://agel-nash.ru';