<?php
include_once '../config/db.php';
include_once '../classes/book.php';
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')) {
    header('Location: ../index.php');
    exit;
}
$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);
$books = $book->getAllBooks();


$errors = isset($_SESSION['book_errors']) ? $_SESSION['book_errors'] : [];
unset($_SESSION['book_errors']);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'add':
            if ($book->addBook($_POST)) {

                header('Location: index.php?success=added');
            } else {
                $_SESSION['book_errors'] = $book->getErrors();
                header('Location: index.php?error=add');
            }
            exit;

        case 'edit':
            if ($book->updateBook($_POST)) {
                header('Location: index.php?success=updated');
            } else {
                $_SESSION['book_errors'] = $book->getErrors();
                header('Location: index.php?error=edit');
            }
            exit;

        case 'delete':
            if ($book->deleteBook($_POST['book_id'])) {
                header('Location: index.php?success=deleted');
            } else {
                header('Location: index.php?error=delete');
            }
            exit;
    }
}

// Load book for editing if ID is provided
$book_to_edit = null;
if (isset($_GET['edit'])) {
    $book_to_edit = $book->getBookById($_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <!-- Books Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Book Management</h1>
            <button onclick="showAddFormBooks()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i>Add New Book
            </button>
        </div>

        <!-- Books Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($book = $books->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="relative h-72">
                        <?php if ($book['cover_image']): ?>
                            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                                alt="<?php echo htmlspecialchars($book['title']); ?>"
                                class="absolute inset-0 w-full h-full object-contain p-2"
                                onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                        <?php endif; ?>
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2 <?php
                                                            echo $book['status'] === 'available' ? 'bg-green-500' : ($book['status'] === 'borrowed' ? 'bg-red-500' : 'bg-yellow-500');
                                                            ?> text-white px-3 py-1 rounded-full text-xs font-medium">
                            <?php echo ucfirst($book['status']); ?>
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
                                <?php echo isset($book['category_name']) ? htmlspecialchars($book['category_name']) : 'Uncategorized'; ?>
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <button onclick='showEditFormBooks(<?php echo json_encode([
                                                                    "id" => $book["id"],
                                                                    "title" => $book["title"],
                                                                    "author" => $book["author"],
                                                                    "category_id" => $book["category_id"],
                                                                    "cover_image" => $book["cover_image"],
                                                                    "summary" => $book["summary"],
                                                                    "status" => $book["status"]
                                                                ]); ?>)'
                                class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form action="bookCrud.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit"
                                    class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition-colors duration-300">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div id="addBookModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full <?php echo isset($_GET['error']) && $_GET['error'] === 'add' ? '' : 'hidden'; ?>">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Add New Book</h3>
                <button onclick="closeAddModalBooks()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Affichage des erreurs pour le formulaire d'ajout -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?php foreach ($errors as $field => $error): ?>
                        <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Add Form -->
            <form action="bookCrud.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Author</label>
                    <input type="text" name="author" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        <option value="1">Fiction</option>
                        <option value="2">Science</option>
                        <option value="3">History</option>
                        <option value="4">Biography</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cover Image URL</label>
                    <input type="url" name="cover_image" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Summary</label>
                    <textarea name="summary" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModalBooks()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md text-sm font-medium hover:bg-blue-600">
                        Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div id="editBookModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Edit Book</h3>
                <button onclick="closeEditModal()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Debug des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="bookCrud.php" method="POST" class="space-y-4" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="book_id" id="edit_book_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="edit_title" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Author</label>
                    <input type="text" name="author" id="edit_author" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="edit_category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select Category</option>
                        <option value="1">Fiction</option>
                        <option value="2">Non Fiction</option>
                        <option value="3">Science</option>
                        <option value="4">History</option>
                        <option value="5">Literature</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cover Image URL</label>
                    <input type="url" name="cover_image" id="edit_cover_image"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Summary</label>
                    <textarea name="summary" id="edit_summary" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="edit_status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Update Book
                    </button>
                </div>
            </form>
        </div>
    </div>


</body>

</html>