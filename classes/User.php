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
        $this->name = $name ?: null;
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

    public function login() {
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
    
        $query = "SELECT id,email, password, role FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
    
        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && ($this->password === $user['password'])) {
                return [
                    'success' => true,
                    'role' => $user['role'],
                    'id' => $user['id'],
                    'email' => $user['email']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password.'
                ];
            }
        }
        return [
            'success' => false,
            'message' => 'An error occurred during login.'
        ];
    
        $this->db->disconnect(); 
    }
    
}