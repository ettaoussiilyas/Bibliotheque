<?php 
include __DIR__ . '/../config/db.php';
class User{


    private $name;
    private $email;

    private $password;

    private $role;

    private $db;
    private $conn;


    public function register($name, $email, $password){
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = 'authenticated';
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

    public function login($email, $password) {
        $this->email = $email;
        $this->password = $password;
        $this->role = 'authenticated';
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
    
        $query = "SELECT name,id,email, password, role FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
    
        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && ($this->password === $user['password'])) {
                return [
                    'success' => true,
                    'role' => $user['role'],
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name']
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

    public function getAllUsers() {
        $this->conn = null;
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
        $query = "SELECT DISTINCT 
                    u.*,
                    b.due_date,
                    CASE 
                        WHEN b.due_date < CURRENT_DATE AND b.return_date IS NULL THEN 1
                        ELSE 0
                    END as needs_email
                  FROM users u
                  LEFT JOIN borrowings b ON u.id = b.user_id
                  ORDER BY needs_email DESC, u.created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function delete($email){
        $this->email = $email;
        $this->conn = null;
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
        $query = "DELETE FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        return $stmt->execute() ? true : false;
    }

    public function update($email, $newEmail, $newName, $newRole){
        $this->email = $email;
        $this->conn = null;
        $this->db = new DataBase();
        $this->conn = $this->db->getConnection();
        $query = "UPDATE users SET email = :newemail, name = :name, role = :role WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':newemail', $newEmail);
        $stmt->bindParam(':name', $newName);
        $stmt->bindParam(':role', $newRole);
        return $stmt->execute() ? true : false;
    }
    
}