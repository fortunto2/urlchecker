<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 08.02.12
 * Time: 2:31
 * To change this template use File | Settings | File Templates.
 */
 
class ModGeo
{
   private $CORE;
    private $errors=array();
    private $notice=array();
    private $arrayUrl=array();
    private $checkUrl=array();

   function __construct(){
       global $CORE;
       $this->CORE=$CORE;
   }

   /*function getListSize(){ //Получаем список размеров ротатора
      $data=array();
      $this->CORE->db->query("SELECT `a`.`id`,`a`.`width`,`a`.`height`, (SELECT count(`id`) from `rotator_item` WHERE `id_size`=`a`.`id`) as `count` FROM `rotator_size` as `a`");
      while($row = $this->CORE->db->data())
      {
      		$data[]=$row;//
      }
      return $data;
     }*/
    public function getChecked(){
        return $this->checkUrl;
    }
   public function getError(){
       return $this->errors;
   }
   public function getNotice(){
       return $this->notice;
   }
   public function checkSites($data){
       $i=0;
	$temp=explode("\r\n",$_POST['sitelist']);
	foreach($temp as $subtmp){
		$tmp=$this->testUrlLine($subtmp);
		if($tmp!==false && !in_array($tmp,$this->arrayUrl)){
			$this->arrayUrl[]=$tmp;
			$i++;
		}
		if($i>=50){
			$this->notice[]='В обработку приняты первые 50 сайтов';
			break;
		}
	}
   }
    public function CountNoRegionSites(){
        $this->CORE->db->query("SELECT count(id) as `count` FROM ".$this->CORE->getFullTableName('geono_site'));
        $sql=$this->CORE->db->getRow();
        return $sql['count'];
    }
     public function CountRegionSites(){
        $this->CORE->db->query("SELECT count(id) as `count` FROM ".$this->CORE->getFullTableName('geo_site'));
        $sql=$this->CORE->db->getRow();
        return $sql['count'];
    }
    public function getSitesList(){
        return count($this->arrayUrl);
    }
    public function parseSites(){
	    $i=0;

	foreach($this->arrayUrl as $url){
		$flag=true;
		$this->CORE->db->query("SELECT site,region,date FROM ".$this->CORE->getFullTableName('geo_site')." WHERE site='".$this->CORE->db->escape($url)."'");
		if($this->CORE->db->getRecordCount()>0){
			$sql=$this->CORE->db->getRow();
			$this->checkUrl[$i]['site']=$sql['site'];
            $this->checkUrl[$i]['cache']=$sql['date'];
            $this->checkUrl[$i]['region']=$sql['region'];
			$flag=false;
			unset($sql);
		}else{
			$this->CORE->db->query("SELECT site,date FROM ".$this->CORE->getFullTableName('geono_site')." WHERE site='".$this->CORE->db->escape($url)."'");
			if($this->CORE->db->getRecordCount()>0){
				$sql=$this->CORE->db->getRow();
				$this->checkUrl[$i]['site']=$sql['site'];
                $this->checkUrl[$i]['cache']=$sql['date'];
                $this->checkUrl[$i]['region']='';
				$flag=false;
				unset($sql);
			}
		}
		while($flag){
			$return=$this->parse_html($url,$this->CORE->cfg->get('antigate'));
			if($return!==false){
				$flag=false;
				if($return!=''){
					$this->CORE->db->query("INSERT IGNORE INTO ".$this->CORE->getFullTableName('geo_site')." (site,region,date) VALUES ('".$this->CORE->db->escape($url)."','".$return."',NOW())");
                    $this->checkUrl[$i]['site']=$url;
                    $this->checkUrl[$i]['cache']='';
                    $this->checkUrl[$i]['region']=$return;
				}else{
					$this->CORE->db->query("INSERT IGNORE INTO ".$this->CORE->getFullTableName('geono_site')." (site,date) VALUES ('".$this->CORE->db->escape($url)."',NOW())");
					$this->checkUrl[$i]['site']=$url;
                    $this->checkUrl[$i]['cache']='';
                    $this->checkUrl[$i]['region']='';
				}
			}else{
				break;
			}
		}
		if($flag===true && !isset($sql['site'])){
			$this->notice[]="CAPTCHA";
			break;
		}
		$i++;
    }
}
   public function parse_html($url,$antigate){
//%D0%BF%D0%BE%D0%B3%D0%BE%D0%B4%D0%B0 - погода
//%D0%BC%D0%BE%D1%81%D0%BA%D0%B2%D0%B0 - москва
	$html = trim($this->send_get('http://yandex.ru/yandsearch?text=site%3A'.$url.'+%D0%BF%D0%BE%D0%B3%D0%BE%D0%B4%D0%B0&clid=9582&lr=213',$antigate));

	if(strlen($html)>0){
		$saw = new nokogiri($html);
			$link=$saw->get('li.b-serp-item span.b-serp-url__item a.b-serp-url__link')->toArray();
			if(count($link)>0){
				$gorod='';
				foreach($link as $item){
					if(isset($item['#text']) && !is_array($item['#text']) && substr($item['href'], 0, 17)=="/yandsearch?rstr="){
						$gorod=$item['#text'];
						break;
					}
				}
				return $gorod;
			}else{
				return '';
			}
	}else{
		return false;
	}
}

    private function send_get($url,$antigate){
	 $ch = curl_init($url);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt ($ch, CURLOPT_URL, $url);
	 curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.1 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.2");
	 curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	 curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
 	 curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies1.txt');
	 curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies1.txt');
 	 //curl_setopt ($ch, CURLOPT_COOKIE, $key);
	 $res = curl_exec($ch);
	 curl_close($ch);
/* 	 $refer ="http://ya.ru/";
	 $ch = curl_init($url);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);// обнуляем куки
	 curl_setopt ($ch , CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
	 curl_setopt ($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_REFERER, $refer);
	 curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
	 curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	 curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
 	 curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookies1.txt');
	 curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookies1.txt');
	 $res = curl_exec($ch);
	 curl_close($ch); */
	 if(strlen($res)>0){
		$saw = new nokogiri($res);
		 $title=$saw->get("title")->toArray();
		if(strlen($title[0]['#text'])==5){
			$captcha=$saw->get('div.b-captcha form table.b-captcha__layout img.b-captcha__image')->toArray();
			$gif=file_get_contents($captcha[0]['src']);
			$write=fopen('captcha.gif','w');
			$puts=fputs($write,$gif);
			fclose($write);
			$rep=$this->recognize('captcha.gif',$antigate,false,5,5,0,0,1,6,6);
			if($rep===false){
				$res=$this->send_get($url,$antigate);
			}else{
			/*rep=
key=ВЗЯТЬ
retpath=ВЗЯТЬ*/
				$key=$saw->get("div.b-captcha input[name=key]")->toArray();
				$post='key='.$key[0]['value'].'&retpath='.urlencode($url).'&rep='.$rep;
				$ch = curl_init ();
				curl_setopt($ch, CURLOPT_URL, "http://yandex.ru/checkcaptcha?".$post);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)');
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, 0);
				curl_setopt($ch, CURLOPT_REFERER, $url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies1.txt');
				curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies1.txt');

				$data=curl_exec($ch);
				curl_close($ch);
				$res=send_get($url,$antigate);
			}
		}
	 }

	 return $res;
}
    private function constCookie($data){
        return "fuid01=".$data['fuid01']."; yandexuid=".$data['yandexuid']."; spravka=".$data['spravka']."; ";
    }
    public function testUrlLine($line){
        $link=parse_url($line);
        if(isset($link['host'])){
            return strtolower($link['host']);
        }
        return false;
    }

    private function getSite($file){
        $dataURL=array();
        $row = 1;
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
               $tmp=testUrlLine($data['c']);
               if($tmp!==false){
                $dataURL[]=$tmp;
               }
            }
        }
        fclose($handle);
        return $dataURL;
    }

/*
$filename - полный путь к файлу
$apikey   - ключ для работы
$rtimeout - задержка между опросами статуса капчи
$mtimeout - время ожидания ввода капчи

включить/выключить verbose mode (комментирование происходящего):
$is_verbose - false(выключить),  true(включить)

дополнительно (дефолтные параметры править не нужно без необходимости):
$is_phrase - 0 либо 1 - флаг "в капче 2 и более слов"
$is_regsense - 0 либо 1 - флаг "регистр букв в капче имеет значение"
$is_numeric -  0 либо 1 - флаг "капча состоит только из цифр"
$min_len    -  0 (без ограничений), любая другая цифра указывает минимальную длину текста капчи
$max_len    -  0 (без ограничений), любая другая цифра указывает максимальную длину текста капчи

пример:
$text=recognize("/path/to/file/captcha.jpg","ваш_ключ_из_админки",true);

$text=recognize("/path/to/file/captcha.jpg","ваш_ключ_из_админки",false);  //отключено комментирование

$text=recognize("/path/to/file/captcha.jpg","ваш_ключ_из_админки",false,1,0,0,5);  //отключено комментирование, капча состоит из двух слов, общая минимальная длина равна 5 символам

*/

private function recognize($filename, $apikey, $is_verbose = false, $rtimeout = 5, $mtimeout = 120, $is_phrase = 0, $is_regsense = 0, $is_numeric = 0, $min_len = 0, $max_len = 0)
{
	if (!file_exists($filename))
	{
		if ($is_verbose) echo "file $filename not found\n";
		return false;
	}
	$fp=fopen($filename,"r");
	if ($fp!=false)
	{
		$body="";
		while (!feof($fp)) $body.=fgets($fp,1024);
		fclose($fp);
                $ext=strtolower(substr($filename,strpos($filename,".")+1));
	}
	else
	{
		if ($is_verbose) echo "could not read file $filename\n";
		return false;
	}

    if ($ext=="jpg") $conttype="image/pjpeg";
    if ($ext=="gif") $conttype="image/gif";
    if ($ext=="png") $conttype="image/png";


    $sendhost="antigate.com";
    $boundary="---------FGf4Fh3fdjGQ148fdh";

    $content="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"method\"\r\n";
    $content.="\r\n";
    $content.="post\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"key\"\r\n";
    $content.="\r\n";
    $content.="$apikey\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"phrase\"\r\n";
    $content.="\r\n";
    $content.="$is_phrase\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"regsense\"\r\n";
    $content.="\r\n";
    $content.="$is_regsense\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"numeric\"\r\n";
    $content.="\r\n";
    $content.="$is_numeric\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"min_len\"\r\n";
    $content.="\r\n";
    $content.="$min_len\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"max_len\"\r\n";
    $content.="\r\n";
    $content.="$max_len\r\n";
    $content.="--$boundary\r\n";
    $content.="Content-Disposition: form-data; name=\"file\"; filename=\"capcha.$ext\"\r\n";
    $content.="Content-Type: $conttype\r\n";
    $content.="\r\n";
    $content.=$body."\r\n"; //тело файла
    $content.="--$boundary--";


    $poststr="POST http://$sendhost/in.php HTTP/1.0\r\n";
    $poststr.="Content-Type: multipart/form-data; boundary=$boundary\r\n";
    $poststr.="Host: $sendhost\r\n";
    $poststr.="Content-Length: ".strlen($content)."\r\n\r\n";
    $poststr.=$content;

   // echo $poststr;

    if ($is_verbose) echo "connecting $sendhost...";
    $fp=fsockopen($sendhost,80,$errno,$errstr,30);
    if ($fp!=false)
    {
    	if ($is_verbose) echo "OK\n";
    	if ($is_verbose) echo "sending request ".strlen($poststr)." bytes...";
    	fputs($fp,$poststr);
    	if ($is_verbose) echo "OK\n";
    	if ($is_verbose) echo "getting response...";
    	$resp="";
    	while (!feof($fp)) $resp.=fgets($fp,1024);
    	fclose($fp);
    	$result=substr($resp,strpos($resp,"\r\n\r\n")+4);
    	if ($is_verbose) echo "OK\n";
    }
    else
    {
    	if ($is_verbose) echo "could not connect to anti-captcha\n";
        if ($is_verbose) echo "socket error: $errno ( $errstr )\n";
    	return false;
    }

    if (strpos($result, "ERROR")!==false or strpos($result, "<HTML>")!==false)
    {
    	if ($is_verbose) echo "server returned error: $result\n";
        return false;
    }
    else
    {
        $ex = explode("|", $result);
        $captcha_id = $ex[1];
    	if ($is_verbose) echo "captcha sent, got captcha ID $captcha_id\n";
        $waittime = 0;
        if ($is_verbose) echo "waiting for $rtimeout seconds\n";
        sleep($rtimeout);
        while(true)
        {
            $result = file_get_contents('http://antigate.com/res.php?key='.$apikey.'&action=get&id='.$captcha_id);
            if (strpos($result, 'ERROR')!==false)
            {
            	if ($is_verbose) echo "server returned error: $result\n";
                return false;
            }
            if ($result=="CAPCHA_NOT_READY")
            {
            	if ($is_verbose) echo "captcha is not ready yet\n";
            	$waittime += $rtimeout;
            	if ($waittime>$mtimeout)
            	{
            		if ($is_verbose) echo "timelimit ($mtimeout) hit\n";
            		break;
            	}
        		if ($is_verbose) echo "waiting for $rtimeout seconds\n";
            	sleep($rtimeout);
            }
            else
            {
            	$ex = explode('|', $result);
            	if (trim($ex[0])=='OK') return trim($ex[1]);
            }
        }

        return false;
    }
}
}
