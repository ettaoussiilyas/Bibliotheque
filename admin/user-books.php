<?php
include_once '../config/db.php';
include_once '../classes/book.php';

session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: index.php');
    exit;
}

$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);

// Handle book return
if(isset($_POST['return_book'])) {
    $result = $book->returnBook(
        $_POST['return_book'],
        $_POST['book_id'],
        $_POST['user_id']
    );
    echo json_encode($result);
    exit;
}

// Get all borrowings
$borrowings = $book->getAllBorrowings();
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
                                <form method="POST" style="display: inline;" id="returnform">
                                    <input type="hidden" name="return_book" value="<?= $borrow['borrow_id'] ?>">
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