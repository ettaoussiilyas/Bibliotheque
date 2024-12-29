<?php
include_once '../config/db.php';
include_once '../classes/borrowings.php';
include_once '../classes/book.php';

$database = new DataBase();
$conn = $database->getConnection();

$borrowing = new Borrowings($conn);

// Handle book return
if(isset($_POST['return_book'])) {
    try {
        $book_id = $_POST['book_id'];
        $user_id = $_POST['user_id'];
        
        // Commencer une transaction
        $conn->beginTransaction();
        
        // l'emprunteur actuel
        $check_query = "SELECT id FROM borrowings 
                       WHERE book_id = ? 
                       AND return_date IS NULL 
                       ORDER BY borrow_date ASC 
                       LIMIT 1";
        $stmt = $conn->prepare($check_query);
        $stmt->execute([$book_id]);
        $current_borrow = $stmt->fetch(PDO::FETCH_ASSOC);

        //le livre retourne
        $query = "UPDATE borrowings SET return_date = NOW() 
                WHERE book_id = ? AND user_id = ? AND return_date IS NULL";
        $stmt = $conn->prepare($query);
        $result1 = $stmt->execute([$book_id, $user_id]);

        // verification  des reservations
        $query = "SELECT id, user_id 
                 FROM borrowings 
                 WHERE book_id = ? 
                 AND return_date IS NULL 
                 ORDER BY borrow_date ASC 
                 LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$book_id]);
        $nextUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nextUser) {
            $query = "UPDATE borrowings 
                     SET due_date = DATE_ADD(NOW(), INTERVAL 14 DAY)
                     WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nextUser['id']]);
            
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $result2 = $stmt->execute([$book_id]);
        } else {
           
            $query = "UPDATE books SET status = 'available' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $result2 = $stmt->execute([$book_id]);
        }

        if($result1 && $result2) {
            $conn->commit();
            echo json_encode(['success' => true]);
            exit;
        } else {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Update failed']);
            exit;
        }
    } catch(Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// tout borrowings avec user info
$query = "SELECT 
            b.id as borrow_id,
            b.user_id,
            b.book_id,
            b.borrow_date,
            b.due_date,
            b.return_date,
            u.name as user_name,
            u.email as user_email,
            bk.title as book_title,
            bk.author as book_author,
            bk.status as book_status,
            CASE 
                WHEN b.return_date IS NOT NULL THEN 'returned'
                WHEN b.borrow_date = (
                    SELECT MIN(b2.borrow_date)
                    FROM borrowings b2
                    WHERE b2.book_id = b.book_id
                    AND b2.return_date IS NULL
                ) THEN 'borrowed'
                ELSE 'reserved'
            END as borrow_status
          FROM borrowings b
          JOIN users u ON b.user_id = u.id
          JOIN books bk ON b.book_id = bk.id
          ORDER BY b.borrow_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Gestion des Emprunts</h2>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Utilisateur</th>
                    <th class="px-4 py-3 text-left">Livre</th>
                    <th class="px-4 py-3 text-left">Date d'emprunt</th>
                    <th class="px-4 py-3 text-left">Date de retour prévue</th>
                    <th class="px-4 py-3 text-left">Statut</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($borrowings as $borrow): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div>
                                <div class="font-medium"><?= htmlspecialchars($borrow['user_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($borrow['user_email']) ?></div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <div class="font-medium"><?= htmlspecialchars($borrow['book_title']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($borrow['book_author']) ?></div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><?= date('d/m/Y', strtotime($borrow['borrow_date'])) ?></td>
                        <td class="px-4 py-3">
                            <?php if ($borrow['due_date']): ?>
                                <?= date('d/m/Y', strtotime($borrow['due_date'])) ?>
                            <?php else: ?>
                                <span class="text-gray-500">Non définie</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($borrow['return_date']): ?>
                                <span class="px-2 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                    Retourné le <?= date('d/m/Y', strtotime($borrow['return_date'])) ?>
                                </span>
                            <?php elseif ($borrow['borrow_status'] === 'borrowed'): ?>
                                <span class="px-2 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                    En cours
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if (!$borrow['return_date'] && $borrow['due_date']): ?>
                                <form  method="POST" style="display: inline;">
                                    <input type="hidden" name="return_book" value="1">
                                    <input type="hidden" name="book_id" value="<?= $borrow['book_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $borrow['user_id'] ?>">
                                    <button type="submit" 
                                            class="bg-green-500 text-white px-3 py-1 rounded-full text-sm hover:bg-green-600 transition-colors">
                                        Marquer comme retourné
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 