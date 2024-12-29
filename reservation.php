<?php
session_start();
if(!isset($_SESSION['role'])){
    header('Location: index.php');
    exit;
}
include_once 'config/db.php';
include_once 'classes/borrowings.php';
include_once 'classes/book.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$database = new DataBase();
$conn = $database->getConnection();
$borrowing = new Borrowings($conn);

// Initialiser la variable message
$message = '';

// Gérer la nouvelle réservation
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $result = $borrowing->addBorrowing($_SESSION['id'], $book_id);
    $message = $result['message'];
}

// Récupérer les emprunts de l'utilisateur
$my_borrowings = $borrowing->getUserBorrowing($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Emprunts - Bibliothèque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-[#2C3E50] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a class="text-white text-2xl font-bold flex items-center" href="user.php">
                        <i class="fas fa-book-reader mr-2"></i>Bibliothèque
                    </a>
                    <!-- Nouveaux liens -->
                    <a href="user.php" class="text-white hover:text-[#E74C3C] transition-colors duration-300">
                        <i class="fas fa-home mr-1"></i> Accueil
                    </a>
                    <a href="reservation.php" class="text-white hover:text-[#E74C3C] transition-colors duration-300">
                        <i class="fas fa-bookmark mr-1"></i> Mes Emprunts
                    </a>
                </div>
                <div class="flex space-x-4">
                    <span class="text-white flex items-center">
                        <i class="fas fa-user mr-1"></i>
                        <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </span>
                    <a class="text-white hover:text-[#E74C3C] transition-colors duration-300 flex items-center" href="logout.php">
                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php if ($message): ?>
            <div class="mb-8 p-4 rounded-lg <?php echo strpos($message, 'succès') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h1 class="text-3xl font-bold text-gray-800 mb-8">Mes Emprunts</h1>

        <?php if (empty($my_borrowings)): ?>
            <p class="text-gray-600">Vous n'avez pas encore emprunté de livres.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($my_borrowings as $borrow): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="relative h-48">
                            <img src="<?php echo htmlspecialchars($borrow['cover_image'] ?? 'https://via.placeholder.com/300x400'); ?>"
                                alt="<?php echo htmlspecialchars($borrow['title']); ?>"
                                class="absolute inset-0 w-full h-full object-contain p-2">
                        </div>
                        <div class="p-6">
                            <h5 class="text-xl font-bold text-gray-800 mb-4">
                                <?php echo htmlspecialchars($borrow['title']); ?>
                            </h5>
                            <div class="space-y-2 text-gray-600">
                                <p class="flex items-center">
                                    <i class="fas fa-user text-[#3498DB] mr-2"></i>
                                    <?php echo htmlspecialchars($borrow['author']); ?>
                                </p>
                                <p class="flex items-center">
                                    <i class="fas fa-calendar text-[#3498DB] mr-2"></i>
                                    Emprunté le: <?php echo date('d/m/Y', strtotime($borrow['borrow_date'])); ?>
                                </p>
                                <p class="flex items-center">
                                    <i class="fas fa-clock text-[#3498DB] mr-2"></i>
                                    <?php if ($borrow['borrow_status'] === 'borrowed'): ?>
                                        À retourner le: <?php echo date('d/m/Y', strtotime($borrow['due_date'])); ?>
                                    <?php else: ?>
                                        En attente de disponibilité
                                    <?php endif; ?>
                                </p>
                                <p class="flex items-center">
                                    <i class="fas fa-info-circle text-[#3498DB] mr-2"></i>
                                    Statut: <span class="ml-2 <?php echo $borrow['borrow_status'] === 'borrowed' ? 'text-green-500' : 'text-yellow-500'; ?>">
                                        <?php 
                                            if ($borrow['borrow_status'] === 'borrowed') {
                                                echo 'Emprunté';
                                            } else {
                                                echo 'Réservé - Position ' . $borrow['queue_position'] . ' dans la file d\'attente';
                                            }
                                        ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 