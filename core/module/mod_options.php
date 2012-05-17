<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Agel_Nash
 * Date: 23.04.12
 * Time: 2:37
 * To change this template use File | Settings | File Templates.
 */
 
class ModOptions
{
   private $CORE;

    function __construct(){
       global $CORE;
       $this->CORE=$CORE;
   }
    function getUnicTovar(){
        $data=array();
        $this->CORE->db->query("SELECT distinct tovar from ".$this->CORE->getFullTableName("orders"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['tovar'];
        }
        return $data;
    }
    function addTovar($data){
        if($data!=''){
            $data=$this->CORE->db->escape($data);
            $sql=$this->CORE->db->insert(array("name"=>$data),$this->CORE->getFullTableName("tovar"));
            if($sql>0){
                return true;
            }
        }
        return false;
    }
    function getTovar(){
        $data=array();
        $this->CORE->db->query("SELECT `id`,`name` from ".$this->CORE->getFullTableName("tovar"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp;
        }
        return $data;
    }
    function getSelectedTovar($id){
        $data=array();
        $id=$this->CORE->db->escape($id);
        $this->CORE->db->query("SELECT text FROM ".$this->CORE->getFullTableName("tovar_list")." WHERE `id_tovar`='".$id."'");
         while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['text'];
        }
        return $data;
    }
    function setAllocTovar($arr){
        if(is_array($arr) && count($arr)>0){
            $flag=true;
            foreach($arr as $id=>$item){
                $id=(int)$id;
                if(is_array($item) && count($item)>0){
                    $tmp=$this->getSelectedTovar($id);
                    foreach($item as $tovar){
                        if(!in_array($tovar,$tmp)){
                            $flag=$this->CORE->db->insert(array("id_tovar"=>$id,"text"=>$this->CORE->db->escape($tovar)),$this->CORE->getFullTableName("tovar_list"));
                            if($flag<=0){
                                $flag=false;
                                return false;
                            }

                        }else{
                            $tempId=array_search($tovar,$tmp);
                            unset($tmp[$tempId]);
                        }
                    }
                }else{
                    $flag=false;
                }
            }
            if($flag) {
                if(count($tmp)>0){
                    foreach($tmp as $item){
                        $this->CORE->db->query("DELETE FROM ".$this->CORE->getFullTableName("tovar_list")." WHERE `id_tovar`='".$id."' AND `text`='".$item."'");
                    }
                }
                return true;
            }
        }
        return false;
    }





    function getUnicMoney(){
        $data=array();
        $this->CORE->db->query("SELECT distinct valuta from ".$this->CORE->getFullTableName("orders"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['valuta'];
        }
        return $data;
    }
    function addMoney($data){
        if($data!=''){
            $data=$this->CORE->db->escape($data);
            $sql=$this->CORE->db->insert(array("name"=>$data),$this->CORE->getFullTableName("money"));
            if($sql>0){
                return true;
            }
        }
        return false;
    }
    function getMoney(){
        $data=array();
        $this->CORE->db->query("SELECT `id`,`name` from ".$this->CORE->getFullTableName("money"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp;
        }
        return $data;
    }
    function getSelectedMoney($id){
        $data=array();
        $id=$this->CORE->db->escape($id);
        $this->CORE->db->query("SELECT text FROM ".$this->CORE->getFullTableName("money_list")." WHERE `id_money`='".$id."'");
         while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['text'];
        }
        return $data;
    }
    function setAllocMoney($arr){
        if(is_array($arr) && count($arr)>0){
            $flag=true;
            foreach($arr as $id=>$item){
                $id=(int)$id;
                if(is_array($item) && count($item)>0){
                    $tmp=$this->getSelectedMoney($id);
                    foreach($item as $money){
                        if(!in_array($money,$tmp)){
                            $flag=$this->CORE->db->insert(array("id_money"=>$id,"text"=>$this->CORE->db->escape($money)),$this->CORE->getFullTableName("money_list"));
                            if($flag<=0){
                                $flag=false;
                                return false;
                            }

                        }else{
                            $tempId=array_search($money,$tmp);
                            unset($tmp[$tempId]);
                        }
                    }
                }else{
                    $flag=false;
                }
            }
            if($flag) {
                if(count($tmp)>0){
                    foreach($tmp as $item){
                        $this->CORE->db->query("DELETE FROM ".$this->CORE->getFullTableName("money_list")." WHERE `id_money`='".$id."' AND `text`='".$item."'");
                    }
                }
                return true;
            }
        }
        return false;
    }









    function getUnicStatus(){
        $data=array();
        $this->CORE->db->query("SELECT distinct status from ".$this->CORE->getFullTableName("orders"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['status'];
        }
        return $data;
    }
    function addStatus($data){
        if($data!=''){
            $data=$this->CORE->db->escape($data);
            $sql=$this->CORE->db->insert(array("name"=>$data),$this->CORE->getFullTableName("status"));
            if($sql>0){
                return true;
            }
        }
        return false;
    }
    function getStatus(){
        $data=array();
        $this->CORE->db->query("SELECT `id`,`name` from ".$this->CORE->getFullTableName("status"));
        while($tmp=$this->CORE->db->data()){
            $data[]=$tmp;
        }
        return $data;
    }
    function getSelectedStatus($id){
        $data=array();
        $id=$this->CORE->db->escape($id);
        $this->CORE->db->query("SELECT text FROM ".$this->CORE->getFullTableName("status_list")." WHERE `id_status`='".$id."'");
         while($tmp=$this->CORE->db->data()){
            $data[]=$tmp['text'];
        }
        return $data;
    }
    function setAllocStatus($arr){
        if(is_array($arr) && count($arr)>0){
            $flag=true;
            foreach($arr as $id=>$item){
                $id=(int)$id;
                if(is_array($item) && count($item)>0){
                    $tmp=$this->getSelectedStatus($id);
                    foreach($item as $status){
                        if(!in_array($status,$tmp)){
                            $flag=$this->CORE->db->insert(array("id_status"=>$id,"text"=>$this->CORE->db->escape($status)),$this->CORE->getFullTableName("status_list"));
                            if($flag<=0){
                                $flag=false;
                                return false;
                            }

                        }else{
                            $tempId=array_search($status,$tmp);
                            unset($tmp[$tempId]);
                        }
                    }
                }else{
                    $flag=false;
                }
            }
            if($flag) {
                if(count($tmp)>0){
                    foreach($tmp as $item){
                        $this->CORE->db->query("DELETE FROM ".$this->CORE->getFullTableName("status_list")." WHERE `id_status`='".$id."' AND `text`='".$item."'");
                    }
                }
                return true;
            }
        }
        return false;
    }
}