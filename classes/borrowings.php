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

        if ($book['status'] === 'borrowed') {
            $count = "SELECT COUNT(*) as total 
                     FROM borrowings 
                     WHERE book_id = ? 
                     AND return_date IS NULL 
                     AND borrow_date > (
                         SELECT MIN(borrow_date)
                         FROM borrowings 
                         WHERE book_id = ? 
                         AND return_date IS NULL
                     )";
            $stmt = $this->conn->prepare($count);
            $stmt->execute([$book_id, $book_id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $position = $result['total'] + 1;

            $query = "INSERT INTO borrowings (user_id, book_id, borrow_date) 
                     VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id, $book_id]);

            if ($position === 1) {
                $update = "UPDATE books SET status = 'reserved' WHERE id = ?";
                $stmt = $this->conn->prepare($update);
                $stmt->execute([$book_id]);
            }

            return [
                'success' => true,
                'message' => "Vous êtes en position $position dans la file d'attente"
            ];
        }

        return ['success' => false, 'message' => 'Le livre ne peut pas être réservé'];
    }

    public function getUserBorrowing($user_id) {
        $query = "SELECT 
                    b.*, 
                    books.title, 
                    books.author, 
                    books.cover_image, 
                    books.status,
                    b.borrow_date,
                    COALESCE(b.due_date, DATE_ADD(b.borrow_date, INTERVAL 14 DAY)) as due_date,
                    IF(b.id = (
                        SELECT sub.id
                        FROM borrowings sub
                        WHERE sub.book_id = b.book_id
                        AND sub.return_date IS NULL
                        ORDER BY sub.borrow_date ASC
                        LIMIT 1
                    ), 'borrowed', 'reserved') as borrow_status,
                    CASE 
                        WHEN b.id = (
                            SELECT sub.id
                            FROM borrowings sub
                            WHERE sub.book_id = b.book_id
                            AND sub.return_date IS NULL
                            ORDER BY sub.borrow_date ASC
                            LIMIT 1
                        ) THEN NULL
                        ELSE (
                            SELECT COUNT(*) + 1
                            FROM borrowings sub
                            WHERE sub.book_id = b.book_id
                            AND sub.return_date IS NULL
                            AND sub.borrow_date < b.borrow_date
                        )
                    END as queue_position
                 FROM borrowings b
                 JOIN books ON b.book_id = books.id
                 WHERE b.user_id = ? 
                 AND b.return_date IS NULL
                 ORDER BY b.borrow_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
