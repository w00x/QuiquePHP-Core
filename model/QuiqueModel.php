<?php

class QuiqueModel {
    private $dbh;
    
    public function __construct() {
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
                throw new QuiqueExceptions(SHOW_ERRORS,"Error DB Connection",$e->getMessage());
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }

    }

    public function __destruct() {
        $this->dbh = NULL;
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
                throw new QuiqueExceptions(SHOW_ERRORS,"Error DB Query ({$sql})",$e->getMessage());
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
    }
}
