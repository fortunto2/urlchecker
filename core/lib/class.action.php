<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 20.04.12
 * Time: 9:39
 * To change this template use File | Settings | File Templates.
 */
 
class Action{
    protected $action=array();
    private $route=array();
    private $CORE;
    private $DeffAction=array(
            '404'=>"404action.php",
            'index'=>'indexaction.php',
    );
    
    function __construct(){
        global $CORE;
        $this->CORE=$CORE;
        $this->route=isset($_GET)?$_GET:array();

        $this->getAction();
        $this->LoadAction();
    }

    function getAction(){
        $name='';
         foreach($this->route as $name=>$value){
            if(is_dir(MAIN_DIR."/core/action/".$name."/")){
                $this->CORE->setMsg($name." is exists","notice");
                $this->action['name']=$name;
                if(is_dir(MAIN_DIR."/core/action/".$name."/".$value."/")){
                    $this->CORE->setMsg($value." good action main","notice");
                    $this->action['name']=$name;
                    $this->action[$name]=$value;
                    break;
                }
            }
        }
        if($name!=''){
            unset($this->route[$name]);
            $name='';
            $value='';
            foreach($this->route as $name=>$value){
                if(is_file(MAIN_DIR."/core/action".$this->getPathAction()."/".$name.".".$value.".php")){
                    $this->CORE->setMsg($value." action is exists","notice");
                    $this->action['doing']=$name.".".$value.".php";
                    break;
                }
            }
            if($name!=''){
                unset($this->route[$name]);
            }
        }
        $this->setUndefAction();
    }

    function setUndefAction(){
        /*
         * Тут уже прочеканые папки и файлы. т.е. они уже существуют нужно проверять только на существование переменной
         */
        if($this->getPathAction(0)==''){
            $this->action['doing']=$this->DeffAction['index'];
        }elseif(isset($this->action['name']) && $this->action['name']!='' &&
           isset($this->action[$this->action['name']]) && $this->action[$this->action['name']]!='' &&
           is_file(MAIN_DIR."/core/action".$this->getPathAction(0)."/index.".$this->action[$this->action['name']].".php") &&
           !isset($this->action['doing'])){
                $this->CORE->setMsg("Main page action ".$this->action[$this->action['name']],"notice");
                $this->action['doing']="index.".$this->action[$this->action['name']].".php";
        }
        elseif(is_file(MAIN_DIR."/core/action".$this->getPathAction(0)."/".$this->DeffAction['404']) && (!isset($this->action['doing']) || $this->action['doing']=='')){
            $this->action['doing']=$this->DeffAction['404'];
            $this->CORE->setMsg("Action undeff. Set 404 action","notice");
        }
    }
    function LoadAction(){
        if(is_file(MAIN_DIR."/core/action".$this->getPathAction(1))){
            $this->CORE->setMsg("Load action: ".MAIN_DIR."/core/action".$this->getPathAction(1),"notice");
            include_once(MAIN_DIR."/core/action".$this->getPathAction(1));
        }else{
            $this->CORE->setMsg("No found action. Redirect main Page","notice");
        }
    }
    function getPathAction($file=0){
        $name='';
        if(isset($this->action['name'])){
            $tmp=$this->action['name'];
            $name="/".$tmp;
            if(isset($this->action[$tmp])){
                $name.="/".$this->action[$tmp];
            }
        }
         if($file==1 && isset($this->action['doing'])){ //С файлом путь показывать или без
            $name.="/".$this->action['doing'];
         }
        return $name;
    }
}
