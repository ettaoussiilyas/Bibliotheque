<?php

class DataBase{
    private $host = "localhost";
    private $db_name = "bibliotheque";
    private $username = "root";
    private $password = "";
    private $conn;
    private $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    public function __construct(){
        $this->conn = null;
        // self::$conn = null;
        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password, $this->options);
            // echo "Connected successfully";
        }catch(PDOException $exception){
            echo "Connection error: ".$exception->getMessage();
        }
    }

    public function disconnect(){
        $this->conn = null;
    }

    public function getConnection(){
        return $this->conn;
    }
    

}


?>