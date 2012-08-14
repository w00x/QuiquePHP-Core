<?php

class QuiqueModel {
    private $dbh;
    private $model_name;
    private $sql_select;
    
    public function __construct() {
        $this->sql_select = " * ";
        
        $class_name = strtolower(get_class($this));
        $this->model_name = "";
        
        if(strpos($class_name,"_model") !== FALSE) {
             $this->model_name = str_replace("_model", "", $class_name);
        }
        
        $db_config = QuiqueConfig::get_arr_yml_config("database.yml");
        $app_db_config = $db_config[MODULE_NAME];
        
        $db_name = $app_db_config["db_name"];
        $host = $app_db_config["host"];
        $driver = $app_db_config["driver"];
        $user = $app_db_config["user"];
        $password = $app_db_config["password"];
        
        $dsn = "{$driver}:dbname={$db_name};host={$host}";
        
        try {
            $this->dbh = new PDO($dsn, $user, $password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            try {
                throw new QuiqueExceptions(SHOW_ERRORS,"Error DB",$e->getMessage());
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
    }
    
    public function get_dbh() {
        return $this->dbh;
    }
    
    public function sql_query($sql,$params = array()) {
        try {
            $sth = $this->dbh->prepare($sql);

            if(count($params) > 0) {
                $sth->execute($params);
            }
            else {
                $sth->execute();
            }
            return $sth;
        }
        catch(PDOException $e) {
            try {
                throw new QuiqueExceptions(SHOW_ERRORS,"Error DB",$e->getMessage());
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
    }
    
    public function select($columns) {
        $this->sql_select = " ".$columns." ";
    }
    
    public function find($id,$fetch_style = PDO::FETCH_ASSOC) {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name} WHERE id = :id";
        return $this->sql_query($sql,array(":id"=>$id))->fetchAll($fetch_style);
    }
    
    public function find_stmt($id) {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name} WHERE id = :id";
        return $this->sql_query($sql,array(":id"=>$id));
    }
    
    public function delete($id,$fetch_style = PDO::FETCH_ASSOC) {
        $sql = "DELETE FROM {$this->model_name} WHERE id = :id";
        return $this->sql_query($sql,array(":id"=>$id))->fetchAll($fetch_style);
    }
    
    public function delete_stmt($id) {
        $sql = "DELETE FROM {$this->model_name} WHERE id = :id";
        return $this->sql_query($sql,array(":id"=>$id));
    }
    
    public function where($sql_where,$arr_params = array(),$fetch_style = PDO::FETCH_ASSOC) {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name} WHERE ";
        return $this->sql_query($sql.$sql_where,$arr_params)->fetchAll($fetch_style);
    }
    
    public function where_stmt($sql_where,$arr_params = array()) {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name} WHERE ";
        return $this->sql_query($sql.$sql_where,$arr_params);
    }
    
    public function delete_where($sql_where, $arr_params = array(), $fetch_style = PDO::FETCH_ASSOC) {
        $sql = "DELETE FROM {$this->model_name} WHERE ";
        return $this->sql_query($sql.$sql_where,$arr_params)->fetchAll($fetch_style);
    }
    
    public function delete_where_stmt($sql_where,$arr_params = array()) {
        $sql = "DELETE FROM {$this->model_name} WHERE ";
        return $this->sql_query($sql.$sql_where,$arr_params);
    }
    
    public function __call($name, $arguments) {
        return $this->parser_funtion_to_sql_query($name, $arguments);
    }
    
    private function parser_funtion_to_sql_query($function_name,$arguments) {
        if(strpos($function_name,"findBy") !== FALSE) {
            $column = strtolower(str_replace("findBy", "", $function_name));
            $val = $this->where($column." = :".$column." LIMIT 1", array(":".$column => $arguments[0]));
            return $val[0];
        }
        elseif(strpos($function_name,"findAllBy") !== FALSE) {
            $column = strtolower(str_replace("findAllBy", "", $function_name));
            $val = $this->where($column." = :".$column, array(":".$column => $arguments[0]));
            return $val;
        }
        elseif(strpos($function_name,"deleteBy") !== FALSE) {
            $column = strtolower(str_replace("deleteBy", "", $function_name));
            $val = $this->delete_where($column." = :".$column, array(":".$column => $arguments[0]));
            return $val[0];
        }
        elseif(strpos($function_name,"findStmtBy") !== FALSE) {
            $column = strtolower(str_replace("findBy", "", $function_name));
            $val = $this->where_stmt($column." = :".$column." LIMIT 1", array(":".$column => $arguments[0]));
            return $val;
        }
        elseif(strpos($function_name,"findAllStmtBy") !== FALSE) {
            $column = strtolower(str_replace("findAllBy", "", $function_name));
            $val = $this->where_stmt($column." = :".$column, array(":".$column => $arguments[0]));
            return $val;
        }
        elseif(strpos($function_name,"deleteStmtBy") !== FALSE) {
            $column = strtolower(str_replace("deleteBy", "", $function_name));
            $val = $this->delete_where_stmt($column." = :".$column, array(":".$column => $arguments[0]));
            return $val;
        }
    }
    
    public function set_model_name($model_name) {
        $this->model_name = $model_name;
    }
    
    public function selectAll($fetch_style = PDO::FETCH_ASSOC) {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name}";
        return $this->sql_query($sql)->fetchAll($fetch_style);
    }
    
    public function selectAllStmt() {
        $sql = "SELECT {$this->sql_select} FROM {$this->model_name}";
        return $this->sql_query($sql);
    }
}