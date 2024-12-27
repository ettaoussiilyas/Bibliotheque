<?php
class Book
{
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

    //adding data
    public $adding_erreur = array();
    // private $data = array();

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllBooks()
    {
        $query = "SELECT b.*, c.name as category_name 
                 FROM " . $this->table_name . " b
                 LEFT JOIN categories c ON b.category_id = c.id
                 ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //search a book
    public function searchBooks($searchTerm)
    {
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

    //runder book resullt
    public function renderResults($results)
    {
        $output = '';

        if (!empty($results)) {
            // foreach ($results as $book) {
            //     $output .= "<div class='book-item'>";
            //     $output .= "<h3>" . htmlspecialchars($book['title']) . "</h3>";
            //     $output .= "<p>Author: " . htmlspecialchars($book['author']) . "</p>";
            //     $output .= "<img src='" . htmlspecialchars($book['cover_image']) . "' alt='" . htmlspecialchars($book['title']) . "' style='max-width: 100%; height: auto;'>";
            //     $output .= "</div>";
            // }

            foreach ($results as $book) {
                $output .= "<div class='w-2/4 mb-4 bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl'>";
                // Image Section
                $output .= "<div class='relative h-72'>";
                if (!empty($book['cover_image'])) {
                    $output .= "<img src='" . htmlspecialchars($book['cover_image']) . "' " .
                        "alt='" . htmlspecialchars($book['title']) . "' " .
                        "class='absolute inset-0 w-full h-full object-contain p-2' " .
                        "onerror=\"this.src='https://via.placeholder.com/200x300?text=Image+non+disponible'\">";
                }

                // Status Badge
                $statusClass = $book['status'] === 'available' ? 'bg-green-500' :
                    ($book['status'] === 'borrowed' ? 'bg-red-500' : 'bg-yellow-500');
                $statusText = $book['status'] === 'available' ? 'Disponible' :
                    ($book['status'] === 'borrowed' ? 'Emprunté' : 'Réservé');

                $output .= "<div class='absolute top-2 right-2 {$statusClass} text-white px-3 py-1 rounded-full text-xs font-medium'>";
                $output .= $statusText;
                $output .= "</div>";
                $output .= "</div>";

                // Book Details Section
                $output .= "<div class='p-6'>";
                $output .= "<h5 class='text-xl font-bold text-gray-800 mb-4'>" . htmlspecialchars($book['title']) . "</h5>";
                $output .= "<div class='space-y-2 text-gray-600 mb-6'>";
                $output .= "<p class='flex items-center'><i class='fas fa-user text-[#3498DB] mr-2'></i>" .
                    htmlspecialchars($book['author']) . "</p>";
                $output .= "<p class='flex items-center'><i class='fas fa-bookmark text-[#3498DB] mr-2'></i>" .
                    (isset($book['category_name']) ? htmlspecialchars($book['category_name']) : 'Non catégorisé') . "</p>";
                $output .= "</div>";

                // Buttons Section
                $output .= "<div class='flex justify-between items-center'>";

                // Details Button
                $bookDetails = [
                    'title' => addslashes($book['title']),
                    'author' => addslashes($book['author']),
                    'status' => $book['status'],
                    'category_name' => addslashes($book['category_name'] ?? 'Non catégorisé'),
                    'cover_image' => addslashes($book['cover_image']),
                    'summary' => addslashes($book['summary'] ?? 'Aucun résumé disponible')
                ];

                $output .= "<a href='#' onclick='showBookDetails(" . json_encode($bookDetails) . ")' " .
                    "class='bg-[#3498DB] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#2980B9] transition-colors duration-300'>" .
                    "<i class='fas fa-info-circle mr-1'></i>Détails</a>";

                // Déterminer la page courante
                $current_page = basename($_SERVER['PHP_SELF']);

                // Borrow Button avec if/else
                if ($current_page === 'user.php') {
                    $output .= "<a href='reservation.php?book_id=" . $book['id'] . "' " .
                        "class='bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors duration-300'>" .
                        "<i class='fas fa-book-reader mr-1'></i>Emprunter</a>";
                } else {
                    $output .= "<a href='login.php' " .
                        "class='bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors duration-300'>" .
                        "<i class='fas fa-book-reader mr-1'></i>Emprunter</a>";
                }

                $output .= "</div>"; // Close buttons container
                $output .= "</div>"; // Close book details section
                $output .= "</div>"; // Close main card container
            }


        } else {
            $output = "No results found.";
        }

        return $output;
    }

    //ajax handling
    public function handleAjaxRequest($data)
    {
        if (isset($data['query'])) {
            $results = $this->searchBooks($data['query']);
            echo $this->renderResults($results);
            exit;
        }
    }
    public function getErrors()
    {
        return $this->adding_erreur;
    }

    // Optional: Add a method to check if there are errors
    public function hasErrors()
    {
        return !empty($this->adding_erreur);
    }

    public function addBook($data)
    {
        // Réinitialiser les erreurs
        $this->adding_erreur = array();

        // Validation des champs
        if (empty($data['title'])) {
            $this->adding_erreur['title'] = "Le titre est obligatoire";
        } elseif (strlen($data['title']) < 3) {
            $this->adding_erreur['title'] = "Le titre doit avoir au moins 3 caractères";
        }

        if (empty($data['author'])) {
            $this->adding_erreur['author'] = "L'auteur est obligatoire";
        }

        if (empty($data['category_id'])) {
            $this->adding_erreur['category'] = "La catégorie est obligatoire";
        }

        if (empty($data['summary'])) {
            $this->adding_erreur['summary'] = "Le résumé est obligatoire";
        }

        if (empty($data['status'])) {
            $this->adding_erreur['status'] = "Le statut est obligatoire";
        } elseif (!in_array($data['status'], ['available', 'borrowed', 'reserved'])) {
            $this->adding_erreur['status'] = "Statut invalide";
        }

        // Si il y a des erreurs, on arrête ici
        if (!empty($this->adding_erreur)) {
            return false;
        }

        try {
            // Code d'insertion dans la base de données
            $query = "INSERT INTO " . $this->table_name . " 
                    (title, author, category_id, cover_image, summary, status) 
                    VALUES (:title, :author, :category_id, :cover_image, :summary, :status)";

            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                'title' => $data['title'],
                'author' => $data['author'],
                'category_id' => $data['category_id'],
                'cover_image' => $data['cover_image'] ?? '',
                'summary' => $data['summary'],
                'status' => $data['status']
            ]);

            if (!$result) {
                $this->adding_erreur['database'] = "Erreur lors de l'ajout du livre";
                return false;
            }

            return true;

        } catch (PDOException $e) {
            $this->adding_erreur['database'] = "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    public function deleteBook($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
        return $stmt;

    }

    public function getBookById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBook($data)
    {
        try {
            // Debug - Afficher les données reçues
            error_log("Données reçues dans updateBook: " . print_r($data, true));

            // Validation basique
            if (empty($data['book_id'])) {
                $this->adding_erreur['id'] = "ID du livre manquant";
                return false;
            }

            // prepare request
            $updateData = [
                'id' => $data['book_id'],
                'title' => $data['title'],
                'author' => $data['author'],
                'category_id' => $data['category_id'],
                'cover_image' => $data['cover_image'] ?? '',
                'summary' => $data['summary'] ?? '',
                'status' => $data['status']
            ];

            // Debug - shoing debugging
            //error_log("Données à mettre à jour: " . print_r($updateData, true));

            $query = "UPDATE " . $this->table_name . " 
                    SET title = :title, 
                        author = :author, 
                        category_id = :category_id, 
                        cover_image = :cover_image, 
                        summary = :summary, 
                        status = :status 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute($updateData);

            if (!$result) {
                $this->adding_erreur['database'] = "Erreur lors de la mise à jour: " . print_r($stmt->errorInfo(), true);
                return false;
            }

            return true;

        } catch (PDOException $e) {
            $this->adding_erreur['database'] = "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    public function borrowed()
    {
        $query = "SELECT COUNT(*) as count FROM books WHERE status = 'borrowed'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>