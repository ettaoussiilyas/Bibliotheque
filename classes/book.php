<?php
class Book {
    private $conn;
    private $table_name = "books";

    public $id;
    public $title;
    public $author;
    public $category_id;
    public $cover_image;
    public $summary;
    public $status;
    public $created_at;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllBooks() {
        $query = "SELECT b.*, c.name as category_name 
                 FROM " . $this->table_name . " b
                 LEFT JOIN categories c ON b.category_id = c.id
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getBookDetails($id) {
        $query = "SELECT b.*, c.name as category_name 
                 FROM " . $this->table_name . " b
                 LEFT JOIN categories c ON b.category_id = c.id
                 WHERE b.id = ?";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }
}
?> 