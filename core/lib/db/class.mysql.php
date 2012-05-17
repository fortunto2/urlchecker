<?php

    class  DB
    {
        public $connection;
        public $sql=array();
        public $query;
        public $database;
        public $error=false;
        private  $countsql=array('db'=>0,'cache'=>0);
        function connect($host, $user, $password, $database)
        {
            $this->error=false;
            $this->connection = mysql_connect($host, $user, $password);
            $this->database=$database;
            mysql_select_db($database, $this->connection);
            $this->query('SET NAMES utf8');
        }
        function getRow($mode = 'assoc') {
                 if ($mode == 'assoc') {
                    return mysql_fetch_assoc($this->query);
                 }
                 elseif ($mode == 'num') {
                    return mysql_fetch_row($this->query);
                 }
                 elseif ($mode == 'both') {
                    return mysql_fetch_array($this->query, MYSQL_BOTH);
                 } else {
                    return false;
                 }
           }
        function getCountSql($mode=''){
            if(isset($this->countsql[$mode])){
                return $this->countsql[$mode];
            }
            return $this->countsql['db']+$this->countsql['cache'];
        }
        function query($sql)
        {
            $this->sql[] = $sql;
            $this->countsql['db']++;
            $this->query = mysql_query($sql);
            return $this->query;
        }

        function data()
        {
            $data=array();
            if($this->query!=''){
                return mysql_fetch_assoc($this->query);
            }
        }
        function affected() { return mysql_affected_rows(); }
        function update($fields, $table, $where = "") {
          if (!$table){
             return false;
          }else {
             if (is_array($fields)){
                $flds = '';
                foreach ($fields as $key => $value) {
                   if (!empty ($flds))
                      $flds .= ",";
                   $flds .= "`".$key . "`=";
                   $flds .= "'" . $value . "'";
                }
             }else{
                 return false;
             }
             $where = ($where != "") ? "WHERE $where" : "";
             return $this->query("UPDATE $table SET $flds $where");
          }
       }
        function insert($fields, $intotable,$checkdouble="") {
            $flag='insert';
          if (!$intotable)
             return false;
          else {
             if (is_array($fields)){
                 $keys = array_keys($fields);
                $values = array_values($fields);
                if($checkdouble!=''){
                    $sql=$this->query("SELECT `" . implode("`,`", $keys) . "` FROM ".$intotable."WHERE ".$checkdouble);
                    if($this->getRecordCount()==1){
                        $sql=$this->getRow();
                        foreach($fields as $name=>$item){
                            if(!(isset($sql[$name]) && $sql[$name]==$item)){
                                $flag='update';
                                break;
                            }else{
                                $flag='none';
                            }
                        }
                    }else{
                        $flag='insert';
                    }
                }
             }else{
                return false;
             }
             switch($flag){
                 case 'insert':{
                    $flds = "(`" . implode("`,`", $keys) . "`) VALUES('" . implode("','", $values) . "')";
                    $rt = $this->query("INSERT IGNORE INTO ".$intotable." ".$flds);
                    $lid = $this->getInsertId();
                     break;
                 }
                 case 'update':{
                     $flag=$this->update($fields,$intotable,$checkdouble);
                     if($flag!==false){
                        return true;
                     }else{
                        $this->error=true;
                        return false;
                     }
                     break;
                 }
                 default:{
                     return false;
                 }
             }

             return $lid ? $lid : $rt;
          }
          return false;
       }
        function getRecordCount() {
              return (is_resource($this->query)) ? mysql_num_rows($this->query) : 0;
           }
        function escape($s) {
          if (function_exists('mysql_real_escape_string')) {
             $s = mysql_real_escape_string($s);
          } else {
             $s = mysql_escape_string($s);
          }
        return $s;
    }
        function getInsertId(){
            return mysql_insert_id();
        }
        function getValue($query='') {
          if ($query!=''){
             $this->query($query);
          }
           $r = $this->getRow("num");
           return $r[0];
       }
    }
?>
