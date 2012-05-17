<?php
include_once "core/lib/class.nokogiri.php"; 

$arrayUrl=getSite("search_sites.csv");
print_r($arrayUrl);
die();
$key=array("fuid01=4b55eb3819e45ffc.GHz1qZGVLdiellfrdaV8oOurD-eyAQLruoiXkgwQlajZVIiK72GT1sl3vBlpr8MCD-dfUUrA7hZR_ahgXIXDZ-3EAqCx5Nfdnl4SSdbSbfPeOJCprMor9M0eB8hpEVX1; yandexuid=2558214601334127470; spravka=dD0xMzM0MTI3NDc0O2k9MTA5LjIwMS4yMTAuMjIyO3U9MTMzNDEyNzQ3NDk0MTg0NDIxMDtoPTc3NDY3NDI1NTI0ODhiNDg3ZjU4ZjIwN2VmY2M5ZmU0","fuid01=49ca212c0b41019d.HdMAmbg2LGeJmW-_FqwWpeAwUdSD6o8qhBsn43P5bHE49_Jp0JUD3NLKjYcIi0lpiIW546rbIugs6tqgWXPq-qHG_IPe8_IJRh6qx7fSTIML3t_i0ftwlk7S2Ymtfw18; yandexuid=2558214601334127470; spravka=dD0xMzM0MTI3NDc0O2k9MTA5LjIwMS4yMTAuMjIyO3U9MTMzNDEyNzQ3NDk0MTg0NDIxMDtoPTc3NDY3NDI1NTI0ODhiNDg3ZjU4ZjIwN2VmY2M5ZmU0","fuid01=4eb8025f32d83297.U49FRJ2_z4v8OYzNfkJpStkiWcq8dNGqkP26Jp2q-Bd-0gPOXWI9EnCWM9_tGjdZaZms8V1-9g3oKDBCwiWf1ZfhhcGnbaBelpKDNDB_SAIdxHUIKwgOEFFxnahAhwgx","4ea81ea313a6c762.InKDyoz-bMhK0hr2jmYUnCR_3LS4uVNs-o3oY1Y0; yandexuid=2558214601334127470; spravka=dD0xMzM0MTI3NDc0O2k9MTA5LjIwMS4yMTAuMjIyO3U9MTMzNDEyNzQ3NDk0MTg0NDIxMDtoPTc3NDY3NDI1NTI0ODhiNDg3ZjU4ZjIwN2VmY2M5ZmU0","fuid01=4f852e07146ccb19.RT0Rdixs0TqUrCSQaW-9zDFU0gyJxNIolvFQav2HiX3MCG7kXrwDtYKlA0YGAXPlQnxGli9Sm6N1HZT0LUY3YOj7VJYWLRJsD7Uc1b1NMMHo-1J6GUrJGOKt021dLiCd; yandexuid=2558214601334127470; spravka=dD0xMzM0MTI3NDc0O2k9MTA5LjIwMS4yMTAuMjIyO3U9MTMzNDEyNzQ3NDk0MTg0NDIxMDtoPTc3NDY3NDI1NTI0ODhiNDg3ZjU4ZjIwN2VmY2M5ZmU0",
"yandexuid=2659895821334675634; spravka=dD0xMzM0Njc1NjM5O2k9MTc2LjIxNS4xOTYuOTM7dT0xMzM0Njc1NjM5ODY0MjY5NzUxO2g9MmYxODRhM2UzYTJiMDE0NGUyZDE3NmJjZTNlMGFmZWM=; fuid01=4eb8025f32d83297.U49FRJ2_z4v8OYzNfkJpStkiWcq8dNGqkP26Jp2q-Bd-0gPOXWI9EnCWM9_tGjdZaZms8V1-9g3oKDBCwiWf1ZfhhcGnbaBelpKDNDB_SAIdxHUIKwgOEFFxnahAhwgx;");

$fatalCount=0;
$keyI=1;
$i=0;

foreach($arrayUrl as $url){
	$flag=true;
	while($flag){
		$return=parse_html($url,$key,$keyI);
		if($return!==false){
			$flag=false;
			if($return!=''){
				echo $url." <strong>".$return."</strong><br />";
			}else{
				echo $url."<br />";
			}
		}else{
			if($keyI==count($key)){
				break;
			}else{
				$keyI++;
			}
		}
	}
	if($flag===true && !isset($sql['site'])){
		echo "CAPTCHA";
		break;
	}
	$i++;
}
function parse_html($url,$key,$keyI=1){
//%D0%BF%D0%BE%D0%B3%D0%BE%D0%B4%D0%B0 - погода
//%D0%BC%D0%BE%D1%81%D0%BA%D0%B2%D0%B0 - москва
	$html = trim(send_get('http://yandex.ru/yandsearch?text=site%3A'.$url.'+%D0%BF%D0%BE%D0%B3%D0%BE%D0%B4%D0%B0&clid=9582&lr=213',$key[$keyI]));
	if(strlen($html)>0){
		$saw = new nokogiri($html);
		$link=$saw->get('li.b-serp-item span.b-serp-url__item a.b-serp-url__link')->toArray();
		if(count($link)>0){
			$gorod='';
			foreach($link as $item){	
				if(!is_array($item['#text']) && substr($item['href'], 0, 17)=="/yandsearch?rstr="){
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
function send_get($url,$key){
	 $ch = curl_init($url); 
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10); 
	 curl_setopt($ch, CURLOPT_HEADER, 0);  
	 curl_setopt ($ch, CURLOPT_URL, $url);
	 curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.1 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.2");
	 curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	 curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
 	 curl_setopt ($ch, CURLOPT_COOKIE, "cookies1.txt"); 

 	 curl_setopt ($ch, CURLOPT_COOKIE, $key); 
	 $res = curl_exec($ch); 
	 curl_close($ch);
	 return $res;
}

function getSite($file){
	$dataURL=array();
	$row = 1;
	$handle = fopen($file, "r");
	while (feof($handle)!==TRUE) {
		$data=fgets($handle, 4096);
		$link=parse_url($data);
		if(isset($link['host'])){
			$dataURL[]=strtolower($link['host']);
		}
	}
	fclose($handle);
	return $dataURL;
}
 ?>