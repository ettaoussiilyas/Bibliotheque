<?php
require_once '../config/db.php';

class BookSearch {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function searchBooks($searchTerm) {
        if (empty($searchTerm)) {
            return [];
        }

        $query = "SELECT * FROM books WHERE title LIKE :search_title OR author LIKE :search_author";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'search_title' => "%$searchTerm%",
            'search_author' => "%$searchTerm%"
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function renderResults($results) {
        $output = '';
        
        if (!empty($results)) {
            foreach ($results as $book) {
                $output .= "<div class='book-item'>";
                $output .= "<h3>" . htmlspecialchars($book['title']) . "</h3>";
                $output .= "<p>Author: " . htmlspecialchars($book['author']) . "</p>";
                $output .= "</div>";
            }
        } else {
            $output = "No results found.";
        }
        
        return $output;
    }
    
    public function handleAjaxRequest() {
        if (isset($_POST['query'])) {
            $results = $this->searchBooks($_POST['query']);
            echo $this->renderResults($results);
            exit;
        }
    }
}

// Handle AJAX request if it exists
$bookSearch = new BookSearch();
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $bookSearch->handleAjaxRequest();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Book Search</title>
    <style>
        .search-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .search-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .book-item {
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }
        
        .book-item h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .book-item p {
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <input type="text" 
               id="search" 
               class="search-input" 
               placeholder="Search for a book" 
               name="query">
        <div id="result"></div>
    </div>

    <script>
        $(document).ready(function(){
            let searchTimeout;
            
            $("#search").on("keyup", function(){
                clearTimeout(searchTimeout);
                
                const query = $(this).val();
                
                searchTimeout = setTimeout(function() {
                    if (query.length >= 2) {
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {query: query},
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(data) {
                                $("#result").html(data);
                            },
                            error: function() {
                                $("#result").html("An error occurred while searching.");
                            }
                        });
                    } else {
                        $("#result").html("");
                    }
                }, 300);
            });
        });
    </script>
</body>
</html>