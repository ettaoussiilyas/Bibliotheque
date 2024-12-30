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

        $book_query = "SELECT status FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($book_query);
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        if ($book['status'] === 'available') {
            $query = "INSERT INTO borrowings (user_id, book_id, borrow_date, due_date) 
                     VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY))";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id, $book_id]);

            $update = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $this->conn->prepare($update);
            $stmt->execute([$book_id]);
            return ['success' => true, 'message' => 'Le livre a été emprunté avec succès'];
        }

        if ($book['status'] === 'borrowed' || $book['status'] === 'reserved') {
            $query = "INSERT INTO borrowings (user_id, book_id, borrow_date) 
                     VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id, $book_id]);

            $position_query = "
                WITH reservations AS (
                    SELECT 
                        id,
                        ROW_NUMBER() OVER (ORDER BY borrow_date) - 1 as position
                    FROM borrowings
                    WHERE book_id = ?
                    AND return_date IS NULL
                    AND due_date IS NULL
                )
                SELECT position
                FROM reservations
                WHERE id = LAST_INSERT_ID()";
            
            $stmt = $this->conn->prepare($position_query);
            $stmt->execute([$book_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $position = $result['position'];

            if ($position === 0) {
                $update = "UPDATE books SET status = 'reserved' WHERE id = ?";
                $stmt = $this->conn->prepare($update);
                $stmt->execute([$book_id]);
                $position = 1;
            }

            return [
                'success' => true,
                'message' => "Vous êtes en position $position dans la file d'attente"
            ];
        }

        return ['success' => false, 'message' => 'Le livre ne peut pas être réservé'];
    }

    public function getUserBorrowing($user_id) {
        $query = "WITH reservations AS (
                    SELECT 
                        b.*,
                        books.title,
                        books.author,
                        books.cover_image,
                        books.status,
                        CASE 
                            WHEN b.due_date IS NOT NULL THEN 'borrowed'
                            ELSE 'reserved'
                        END as borrow_status,
                        CASE 
                            WHEN b.due_date IS NOT NULL THEN NULL
                            ELSE ROW_NUMBER() OVER (
                                PARTITION BY b.book_id 
                                ORDER BY b.borrow_date
                            ) - 1
                        END as queue_position
                    FROM borrowings b
                    JOIN books ON b.book_id = books.id
                    WHERE b.return_date IS NULL
                )
                SELECT *
                FROM reservations
                WHERE user_id = ?
                ORDER BY borrow_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
