<?
class ModSmartresponder
{
    private $CORE;
    private $smartresponder=array();
    private $auth=false;
    private $errors=array();
    private $notice=array();
    private $method='';
    
   function __construct(){
       global $CORE;
       $this->CORE=$CORE;
       $this->smartresponder=$this->CORE->cfg->get('smartresponder');
       $this->method=$this->smartresponder['mode'];
       $this->auth($this->smartresponder['login'],$this->smartresponder['pass']);
   }
    private function getDataPost($arr){
        if($this->smartresponder['mode']=='api'){
            $arr['api_key']=$this->smartresponder['api'];
        }else{
            $arr['api_id']=$this->smartresponder['md5'];
            $arr['hash']=md5($arr,':'); //Контрольная сумма запроса, которая представляет собой MD5-хэш от строки вида: "переменная_1=значение_1:переменная_2=значение_2: ... :password=ваш_API_пароль".
        }
        return $this->constPostData($arr);
    }
    
    private function constPostData($arr,$sep="&"){
        $tmp=array();
        foreach($arr as $name=>$value){
            $tmp[]=$name."=".$value;
        }
        $tmp=implode($sep,$tmp);
        return $tmp;
    }
    function getListUser($page=0,$limit=0,$search='',$searchby=''){
        $this->CORE->setMsg("Получаем список подписчиков","notice");
        $goparser=true;
        while($goparser){
            $data = array (
                'format' => $this->smartresponder['format'],
                'sortorder' => 'date_added',
                'sortorderdir'=>'0',
                'records_on_page'=>$this->smartresponder['sizepage'],
                'page'=>$page++,
                'action'=>'list',
                'fields_exclude'=>'templates',
            );
            if($search!='' && $searchby!=''){ //Параметры поиска
                $data["search[".$search."]"]=$searchby;
            }
            //$data->pages - страниц в выборке

            $data=$this->getDataPost($data);
            $data=$this->CORE->workCurl("http://api.smartresponder.ru/subscribers.html",$data,1);
            $data=$this->CheckResponse($data,'list');
            if($data!==false){
               if($data->count>0){
                   $list=0;
                   foreach($data->elements as $item){
                        $tmp=array();
                        $tmp['id_user']=(int)$item->id;
                        $tmp['date_added']=date('Y-m-d H:i:s',strtotime(str_replace("/","-",$item->date_added))); //20/04/2012 17:17:21 преобразовать
                        $tmp['email']=$this->CORE->db->escape(strtolower($item->email));
                        $tmp['state']=$this->CORE->db->escape($item->state);
                        $tmp['first_name']=$this->CORE->db->escape($item->first_name);
                        $tmp['middle_name']=$this->CORE->db->escape($item->middle_name);
                        $tmp['last_name']=$this->CORE->db->escape($item->last_name);
                        $tmp['sex']=$this->CORE->db->escape($item->sex);
                        $tmp['country']=$this->CORE->db->escape($item->country);
                        $tmp['city']=$this->CORE->db->escape($item->city);
                        $tmp['address']=$this->CORE->db->escape($item->address);
                        $tmp['phones']=$this->CORE->db->escape($item->phones);
                        $tmp['birth_year']=(int)$item->birth_year;
                        $tmp['birth_month']=(int)$item->birth_month;
                        $tmp['birth_day']=(int)$item->birth_day;

                        $flag=$this->CORE->db->insert($tmp,$this->CORE->getFullTableName('emails'),"id_user='".$tmp['id_user']."'");
                        if($this->CORE->db->error==true && $flag!==true){
                            $this->CORE->setMsg('Не удалось добавить запись в базу данных');
                            $flag=false;
                            break;
                        }elseif($flag===true){
                            $this->CORE->setMsg("Обновлена информация о подписчике ".$tmp['email'],"notice");
                            $list++;
                        }elseif($flag===false){
                             $list++;
                        }else{
                            $this->CORE->setMsg("В базу данных добавлен новый подписчик ".$tmp['email']);
                            $list++;
                        }
                       if($item->deliveries!=''){ ///Список рассылок
                           $flaglist=$this->updateArrayInfo((array)$item->deliveries,$tmp['id_user'],'list');
                       }
                       if($item->tracks!=''){ //Список каналов подписки
                            $flaglist=$this->updateArrayInfo((array)$item->tracks,$tmp['id_user'],'tracks');
                       }
                        /*
                         * TODO Обработка флагов $flaglist
                        */
                       //Список групп мы не будем учитывать, т.к. они будут часто обновляться
                   }
                   if($flag===false && $list==$data->count){
                       $this->CORE->setMsg("На странице ".$page." инфомрация о подписчиках не менялась","notice");
                   }
                   if($limit!=0){
                        $limit--;
                        if($limit<=0){
                            $goparser=false;
                        }
                    }
               }else{
                   $goparser=false;
                   $this->CORE->setMsg("Smartresponderer.API сказал, что у на странице ".$page." нет подписчиков",'notice');
               }
            }else{
                $goparser=false;
            }
        }
    }

    private function updateArrayInfo($arr,$idUser,$table){
        $this->CORE->db->query("SELECT id_".$table." FROM ".$this->CORE->getFullTableName("email_".$table."user")." WHERE `id_user`='".$idUser."'");
        $emaillist=array();
        while($temp=$this->CORE->db->data()){
            $emaillist[]=$temp['id_'.$table];
        }
        foreach($arr as $temp){
            if(!in_array($temp->id,$emaillist)){
                $this->CORE->db->insert(array('id_user'=>$idUser,'id_'.$table=>$temp->id),$this->CORE->getFullTableName("email_".$table."user"));
            }else{
                $tempId=array_search($temp->id,$emaillist);
                unset($emaillist[$tempId]);
            }
        }
    }
    function getListSubscribe(){
        $this->CORE->setMsg("Получаем список рассылок","notice");
        $data = array (
			'format' => $this->smartresponder['format'],
			'fields' => 'id,title',
            'action'=>'list',
            'fields_exclude'=>'templates',
        );
        $data=$this->getDataPost($data);
		$data=$this->CORE->workCurl("http://api.smartresponder.ru/deliveries.html",$data,1);
        $data=$this->CheckResponse($data,'list');
        if($data!==false){
           if($data->count>0){
               $list=0;
               foreach($data->elements as $item){
                    $tmp=array();
                    $tmp['id_list']=(int)$item->id;
                    $tmp['title']=$this->CORE->db->escape($item->title);
                    $flag=$this->CORE->db->insert($tmp,$this->CORE->getFullTableName('email_list'),"id_list='".$tmp['id_list']."'");
                    if($this->CORE->db->error==true && $flag!==true){
                        $this->CORE->setMsg('Не удалось добавить запись в базу данных');
                        $flag=false;
                        return "Не удалось обновить информацию в базе данных";
                        break;
                    }elseif($flag===true){
                        $this->CORE->setMsg("Обновлен заголовок рассылки с ID ".$tmp['id_list'],"notice");
                        $list++;
                    }elseif($flag===false){
                         $list++;
                    }else{
                        $this->CORE->setMsg("В базу данных добавлена новая рассылка с ID ".$tmp['id_list']);
                        $list++;
                    }
               }
               if($flag===false && $list==$data->count){
                       $this->CORE->setMsg("Ни одна из рассылок не изменилась","notice");
                       return 'Ни одна из рассылок не изменилась';
               }
               if($list==$data->count){
                   return "Информация о рассылках успешно обновлена";
               }
           }else{
               $this->CORE->setMsg("Smartresponderer.API сказал, что у нас 0 рассылок",'notice');
               return 'Smartresponderer.API сказал, что у нас 0 рассылок';
           }
        }
    }
    function CheckResponse($data,$name){
        if ($data) {
		  $jsonObj = json_decode($data);
		  if($jsonObj===null) {
			$this->CORE->setMsg("Некорректный ответ от smartresponder.API");
		  }
		  elseif(isset($jsonObj->error) && !empty($jsonObj->error)) {
              if(isset($jsonObj->error->message) && isset($jsonObj->error->code)){
                    $this->CORE->setMsg("Smartresponder.API вернул ошибку: " . $jsonObj->error->message. "(code: " . $jsonObj->error->code. ")");
              }else{
                  $this->CORE->setMsg("Smartresponder.API вернул ошибку непонятную ошибку");
              }
		  } else {
              if(isset($jsonObj->$name) && !empty($jsonObj->$name)){
                return $jsonObj->$name;
              }else{
                  $this->CORE->setMsg("Раздела ".$name." в ответе от smartresponder.api не обнаружено");
              }
			return $data;
		  }
		} else {
		   $this->CORE->setMsg("Ошибка соединения со smartresponder.API");
		}
        return false;
    }

    function setAuth($flag){
        $this->auth=$flag;
    }

    function getAuth(){
        return $this->auth;
    }

   public function getError(){
       return $this->errors;
   }

   public function getNotice(){
       return $this->notice;
   }

    private function api_auth(){
       $this->CORE->setMsg("START API AUTH","notice");
    }

   public function auth($login,$pass){
       $this->notice[]="Начинаем авторизацию на смартреспондере";
       if($this->method=='login'){
           //$data='rLogin='.$login.'&rPassword='.$pass.'';
           $data='rLogin='.$login.'&rPassword='.$pass.'&submit=submit form&toReturn=/l_ru/user/&action=1&fSubmitted=1&activeTabId&rfCookie=1&login_author='.iconv("UTF-8", "windows-1251", 'Отправить');
           $url=$this->smartresponder['url'].$this->smartresponder['page']['login'];
           $data=$this->CORE->workCurl($url,$data,1);
           print_r($data);
       }else{
           $this->setAuth(true);
           return true;
       }
       return false;
   }

}
?>