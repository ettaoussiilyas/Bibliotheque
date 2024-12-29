<?php
include_once 'config/db.php';
include_once 'classes/book.php';

$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);
$books = $book->getAllBooks();

if (!$books) {
    die("Erreur lors de la récupération des livres");
}
session_start();
if(isset($_SESSION['role'])){
    switch ($_SESSION['role']) {
        case 'authenticated':
            header('Location: user.php');
            break;
        case 'admin':
            header('Location: /admin/index.php');
        
    }
    exit;
}
//search
#search book scope


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $book->handleAjaxRequest($_POST);
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Catalogue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!--for search part-->
    <link rel="stylesheet" href="./style/index.css"> <!--for search part-->
</head>

<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-[#2C3E50] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a class="text-white text-2xl font-bold flex items-center" href="index.php">
                    <i class="fas fa-book-reader mr-2"></i>Bibliothèque
                </a>
                <div class="flex space-x-4">
                    <a class="text-white hover:text-[#E74C3C] transition-colors duration-300 flex items-center" href="register.php">
                        <i class="fas fa-user-plus mr-1"></i> S'inscrire
                    </a>
                    <a class="text-white hover:text-[#E74C3C] transition-colors duration-300 flex items-center" href="login.php">
                        <i class="fas fa-sign-in-alt mr-1"></i> Se connecter
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="bg-gradient-to-r from-[#2C3E50] to-[#3498DB] text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Découvrez notre collection</h1>
            <p class="text-xl">Explorez notre vaste sélection de livres et trouvez votre prochaine lecture</p>
        </div>
    </div>
    <div class="search-container">
        <input type="text"
            id="search"
            class="search-input"
            placeholder="Search for a book"
            name="query">
        <div id="result"></div>
    </div>
    <!-- Books Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($books as $book): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="relative h-72">
                        <?php if ($book['cover_image']): ?>
                            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                                alt="<?php echo htmlspecialchars($book['title']); ?>"
                                class="absolute inset-0 w-full h-full object-contain p-2"
                                onerror="this.src='https://via.placeholder.com/200x300?text=Image+non+disponible'">
                        <?php endif; ?>
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2 <?php
                                                            echo $book['status'] === 'available' ? 'bg-green-500' : ($book['status'] === 'borrowed' ? 'bg-red-500' : 'bg-yellow-500');
                                                            ?> text-white px-3 py-1 rounded-full text-xs font-medium">
                            <?php
                            echo $book['status'] === 'available' ? 'Disponible' : ($book['status'] === 'borrowed' ? 'Emprunté' : 'Réservé');
                            ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h5 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <div class="space-y-2 text-gray-600 mb-6">
                            <p class="flex items-center">
                                <i class="fas fa-user text-[#3498DB] mr-2"></i>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-bookmark text-[#3498DB] mr-2"></i>
                                <?php echo isset($book['category_name']) ? htmlspecialchars($book['category_name']) : 'Non catégorisé'; ?>
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="#"
                                onclick="showBookDetails({
                                   title: '<?php echo addslashes($book['title']); ?>', 
                                   author: '<?php echo addslashes($book['author']); ?>', 
                                   status: '<?php echo $book['status']; ?>', 
                                   category_name: '<?php echo addslashes($book['category_name'] ?? 'Non catégorisé'); ?>', 
                                   cover_image: '<?php echo addslashes($book['cover_image']); ?>',
                                   summary: '<?php echo addslashes($book['summary'] ?? 'Aucun résumé disponible'); ?>'
                               })"
                                class="bg-[#3498DB] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#2980B9] transition-colors duration-300">
                                <i class="fas fa-info-circle mr-1"></i>
                                Détails
                            </a>
                            <a href="login.php"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors duration-300">
                                <i class="fas fa-book-reader mr-1"></i>
                                Emprunter
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal pour les détails -->
    <div id="bookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-1/3" id="modalImage">
                        <!-- L'image sera insérée ici -->
                    </div>
                    <div class="w-full md:w-2/3">
                        <div class="space-y-4">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-user text-[#3498DB] mr-2 w-6"></i>
                                <span id="modalAuthor"></span>
                            </p>
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-bookmark text-[#3498DB] mr-2 w-6"></i>
                                <span id="modalCategory"></span>
                            </p>
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-info-circle text-[#3498DB] mr-2 w-6"></i>
                                <span id="modalStatus"></span>
                            </p>
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold mb-2">Résumé</h4>
                                <p class="text-gray-600" id="modalSummary"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <a href="login.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full text-sm font-medium transition-colors duration-300">
                        <i class="fas fa-book-reader mr-1"></i>
                        Emprunter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="javascript/index.js"></script>
</body>

</html>