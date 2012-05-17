<?php
header('Content-type: text/html; charset=utf-8');
set_time_limit(0);
ini_set('max_execution_time', 0);

require_once('manager/includes/protect.inc.php');
$database_type = "";
$database_server = "";
$database_user = "";
$database_password = "";
$dbase = "";
$table_prefix = "";
$base_url = "";
$base_path = "";

// get the required includes
if($database_user=='') {
        if (!$rt = @include_once "manager/includes/config.inc.php") {
           exit('Could not load MODx configuration file!');
        }
}

// Установка режима MODx API 
define('MODX_API_MODE', true); 
require_once('manager/includes/document.parser.class.inc.php'); 
$modx = new DocumentParser; 
$modx->db->connect(); 
$modx->getSettings();  

/*
* 1) Отсеиваем сайты не подходящие по возрасту
* 2) Проверяем iGood доменов по solomono и отсеиваем заспамленые
* 3) Проверяем число страниц в индексе яндекса и отсеиваем мелкатню
* 4) Отсеиваем сайты с низкой посещалкой http://seo.agel-nash.ru/tools/massvischeck/
*/
define('LIMITDAY', 365);  //Минимум дней сайту
define('LIMITUSER', 500);  //Минимальная посещалка сайта в сутки
define('IGOOD',0.5); //Минимальный порог отношения входящих/исходящих ссылок чем выше, тем качественней
$tmp=getSite("search_sites.csv");
$arrayUrl=array();
foreach($tmp as $url){
	if(parsesolomono_igood($url)){
		$arrayUrl[]=$url;
	}
}
echo "<strong>COUNT URL: ".count($arrayUrl)."</strong><hr />";
foreach($arrayUrl as $url){
	echo $url."<br />";
}
/*
 * getDay (число, месяц, год)
 */
function getDay($getDay, $getMonth, $getYear) {
    $time = time() - mktime(0, 0, 0, $getMonth, $getDay, $getYear);
    $time = ceil($time / 86400);

    return $time;
}

function parsesolomono_igood($url){
	$file = @file_get_contents('http://xml.solomono.ru/?url='.$url);
	$count="0/0";
	if (!empty($file)){
		$file=@file_get_contents('http://xml.solomono.ru/?url='.$url);
		$doc = new DOMDocument();
		$doc->loadXML($file);
		$count=$doc->getElementsByTagName('igood')->item(0)->nodeValue;
	}else $count="0/0";
	$count=explode('/',$count);
	if($count[1]>0){
		if($count[0]/$count[1]>IGOOD){
			return true;
		}
	}
	return false;
}

function getSite($file){
	$dataURL=array();
	$tmp=array();
	$row = 1;
	$handle = fopen($file, "r");
	while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		$num = count($data);
		if($row>0){
			$row--;
			continue;
		}
		if($num==4){
			$date=explode("-",$data[1]);
			if(count($date)==3){
				$date=getDay($date[2], $date[1], $date[0]);
				if($date>LIMITDAY){
					$tmp[]=trim($data[0]);
				}
		   }
		}
	}
	fclose($handle);
	
	//Чекаем посещалку
	$row = 1;
	$handle = fopen($file, "r");
	while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		$num = count($data);

		if($row>0){
			$row--;
			continue;
		}
		if($num==4 && in_array("http://".trim($data[2])."/",$tmp)){
			if($data[3]>LIMITUSER){
				$dataURL[]=$data[0];
		   }
		}
	}
	fclose($handle);
	
	return $dataURL;
}
 ?>