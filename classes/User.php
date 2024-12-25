<?php 
include __DIR__ . '/../config/db.php';
class User{


    private $name;
    private $email;

    private $password;

    private $role;

    private $db;
    private $conn;


    public function __construct($name, $email, $password){
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = 'authenticated';
    }

    public function register(){
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
        $query = "INSERT INTO users(name, email, password, role) VALUES(:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        if($stmt->execute()){ 
            return true;
        }else{
            return false;
        }
        $this->db->disconnect();
        
    }
}