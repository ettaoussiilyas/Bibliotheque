<?php

class borrowings
{
    private $conn;
    private $table_name = "borrowings";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addBorrowing($id, $book_id){
        $check_query = "SELECT id FROM borrowings WHERE user_id = ? AND book_id = ? AND return_date IS NULL";
        $stmt = $this->conn->prepare($check_query);
        $stmt->execute([$id, $book_id]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Vous avez déjà ce livre'];
        }


        $book_query = "SELECT statut FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($book_query);
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        if ($book['statut'] === 'available') {
            $query = "INSERT INTO borrowings (user_id, book_id, borrow_date, due_date) 
                     VALUES (?, ?, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 14 DAY))";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id, $book_id]);

            $updat = "UPDATE books SET statut = 'borrowed' WHERE id = ?";
            $stmt = $this->conn->prepare($updat);
            $stmt->execute([$book_id]);
            return ['success' => true, 'message' => 'Le livre a été emprunt'];
        }

        if ($book['status'] === 'borrowed' || $book['status'] === 'reserved') {
            $count = "SELECT COUNT(*) FROM borrowings WHERE book_id = ? AND return_date IS NULL";
            $stmt = $this->conn->prepare($count);
            $stmt->execute([$book_id]);

            $result = $stmt->fetch();
            $position = $result['total'] + 1;

            $query = "INSERT INTO borrowings (user_id, book_id, borrow_date) 
                     VALUES (?, ?, CURRENT_DATE)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id, $book_id]);

            $update = "UPDATE books SET statut = 'reserved' WHERE id = ?";
            $stmt = $this->conn->prepare($update);
            $stmt->execute([$book_id]);
            return [
                'success' => true,
                'message' => "Vous êtes en position $position pour ce livre"
            ];

            return ['success' => false, 'message' => 'Le livre ne peut pas être réservé'];
        }
    }

    public function getUserBorrowing($user_id){
        $query = "SELECT 
                    b.*, 
                    books.title, 
                    books.author, 
                    books.cover_image, 
                    books.status,
                    CASE 
                        WHEN books.status = 'reserved' THEN 'reserved'
                        ELSE 'borrowed'
                    END as borrow_status
                 FROM borrowings b
                 JOIN books ON b.book_id = books.id
                 WHERE b.user_id = ? 
                 AND b.return_date IS NULL
                 ORDER BY b.borrow_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $emprunts = $stmt->fetchAll();

        foreach ($emprunts as $emprunt) {

            $query_query = "SELECT COUNT(*) + 1 as position
                          FROM borrowings 
                          WHERE book_id = ? 
                          AND return_date IS NULL 
                          AND borrow_date < ?";

            $stmt = $this->conn->prepare($query_query);
            $stmt->execute([$emprunt['book_id'], $emprunt['borrow_date']]);
            $result = $stmt->fetch();

            $emprunt['queue_position'] = $result['position'];
        }
        return $emprunts;
    }
}
