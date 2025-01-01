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
                SELECT COUNT(*) as queue_position
                FROM borrowings 
                WHERE book_id = ? 
                AND return_date IS NULL 
                AND due_date IS NULL";
            
            $stmt = $this->conn->prepare($position_query);
            $stmt->execute([$book_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $queue_position = $result['queue_position'];

            if ($queue_position === 1) {
                $update = "UPDATE books SET status = 'reserved' WHERE id = ?";
                $stmt = $this->conn->prepare($update);
                $stmt->execute([$book_id]);
            }

            return [
                'success' => true,
                'message' => "Réservé - Position " . $queue_position . " dans la file d'attente"
            ];
        }

        return ['success' => false, 'message' => 'Le livre ne peut pas être réservé'];
    }

    public function getUserBorrowing($user_id) {
        $query = "WITH first_borrower AS (
                    SELECT book_id, MIN(borrow_date) as first_borrow_date
                    FROM borrowings
                    WHERE return_date IS NULL
                    GROUP BY book_id
                ),
                reservations AS (
                    SELECT 
                        b.*,
                        books.title,
                        books.author,
                        books.cover_image,
                        books.status,
                        books.summary,
                        CASE 
                            WHEN b.borrow_date = fb.first_borrow_date AND b.due_date IS NOT NULL THEN 'borrowed'
                            ELSE 'reserved'
                        END as borrow_status,
                        CASE 
                            WHEN b.due_date IS NOT NULL THEN NULL
                            ELSE (
                                SELECT COUNT(*)
                                FROM borrowings b2 
                                WHERE b2.book_id = b.book_id 
                                AND b2.return_date IS NULL 
                                AND b2.due_date IS NULL
                                AND b2.borrow_date <= b.borrow_date
                                AND b2.id <= b.id
                            )
                        END as queue_position
                    FROM borrowings b
                    JOIN books ON b.book_id = books.id
                    LEFT JOIN first_borrower fb ON b.book_id = fb.book_id
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
