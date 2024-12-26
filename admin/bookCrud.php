<?php

    include_once '../config/db.php';
    include_once '../classes/book.php';

    $database = new DataBase();
    $conn = $database->getConnection();

    $book = new Book($conn);
    $books = $book->getAllBooks();

    // Get errors if they exist in session
    session_start();
    $errors = isset($_SESSION['book_errors']) ? $_SESSION['book_errors'] : [];
    unset($_SESSION['book_errors']); // Clear errors after retrieving

    $erreur_add = array();

    if(!$books){
        die("Erreur lors de la récupération des livres ");
    }

    if(isset($_POST['action']) && $_POST['action'] == 'add'){


        $book->addBook($_POST['submit']);
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-4">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
                <div>
                    <h2 class="text-center text-3xl font-extrabold text-gray-900">Add New Book</h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Enter the book details below
                    </p>
                </div>

                <form action="./bookCrud.php" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                    <!-- title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <?php if(isset($erreur_add['title'])): ?>
                            <p class="text-red-500"><?php echo $erreur_add['empty']; ?></p>
                        <?php endif; ?>
                        <input type="text" id="title" name="title_add"  
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- author -->
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                        <?php if(isset($erreur_add['author'])): ?>
                            <p class="text-red-500"><?php echo $erreur_add['author']; ?></p>
                        <?php endif; ?>
                        <input type="text" id="author" name="author_add"  
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category_id" name="category_id_add"  
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a category</option>
                            <option value="1">Fiction</option>
                            <option value="2">Non-Fiction</option>
                            <option value="3">Science Fiction</option>
                            <option value="4">Mystery</option>
                        </select>
                    </div>

                    <!-- image  -->
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image URL</label>
                        <input type="url" id="cover_image" name="cover_image_add" 
                            placeholder="https://example.com/image.jpg"
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- summary -->
                    <div>
                        <label for="summary" class="block text-sm font-medium text-gray-700">Summary</label>
                        <textarea id="summary" name="summary_add" rows="4" required 
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status_add" required 
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="available">Available</option>
                            <option value="borrowed">Borrowed</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <!-- submit  -->
                    <div>
                        <?php if(isset($erreur_add['empty'])): ?>
                            <p class="text-red-500"><?php echo $erreur_add['empty']; ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="hidden" name="action" value="add">
                        <button type="submit" name="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
                <div>
                    <h2 class="text-center text-3xl font-extrabold text-gray-900">Edit Book</h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Update book information below
                    </p>
                </div>

                <form action="./bookCrud.php" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">

                    <input type="hidden" name="book_id" value="<?php echo isset($book_to_edit) ? htmlspecialchars($book_to_edit['id']) : ''; ?>">
                    
                    <!-- title to edit -->
                    <div>
                        <label for="title_edit" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" 
                               id="title_edit" 
                               name="title" 
                               required 
                               value="<?php echo isset($book_to_edit) ? htmlspecialchars($book_to_edit['title']) : ''; ?>"
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- authir to edit -->
                    <div>
                        <label for="author_edit" class="block text-sm font-medium text-gray-700">Author</label>
                        <input type="text" 
                               id="author_edit" 
                               name="author" 
                               required 
                               value="<?php echo isset($book_to_edit) ? htmlspecialchars($book_to_edit['author']) : ''; ?>"
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- category to edit -->
                    <div>
                        <label for="category_id_edit" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category_id_edit" 
                                name="category_id" 
                                required 
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a category</option>
                            <option value="1" <?php echo isset($book_to_edit) ? ($book_to_edit['category_id'] == 1 ? 'selected' : '') : ''; ?>>Fiction</option>
                            <option value="2" <?php echo isset($book_to_edit) ? ($book_to_edit['category_id'] == 2 ? 'selected' : '') : ''; ?>>Non-Fiction</option>
                            <option value="3" <?php echo isset($book_to_edit) ? ($book_to_edit['category_id'] == 3 ? 'selected' : '') : ''; ?>>Science Fiction</option>
                            <option value="4" <?php echo isset($book_to_edit) ? ($book_to_edit['category_id'] == 4 ? 'selected' : '') : ''; ?>>Mystery</option>
                        </select>
                    </div>

                    <!--  image to edit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Cover Image</label>
                        <?php if (isset($book_to_edit) && $book_to_edit['cover_image']): ?>
                            <img src="<?php echo htmlspecialchars($book_to_edit['cover_image']); ?>" 
                                 alt="Current cover" 
                                 class="mt-2 h-32 w-auto object-cover rounded-md">
                        <?php endif; ?>
                    </div>

                    <!-- new image to edit -->
                    <div>
                        <label for="cover_image_edit" class="block text-sm font-medium text-gray-700">Cover Image URL</label>
                        <input type="url" 
                               id="cover_image_edit" 
                               name="cover_image" 
                               placeholder="https://example.com/image.jpg"
                               value="<?php echo isset($book_to_edit) ? htmlspecialchars($book_to_edit['cover_image']) : ''; ?>"
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- summry to edit -->
                    <div>
                        <label for="summary_edit" class="block text-sm font-medium text-gray-700">Summary</label>
                        <textarea id="summary_edit" 
                                  name="summary" 
                                  rows="4" 
                                  required 
                                  class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo isset($book_to_edit) ? htmlspecialchars($book_to_edit['summary']) : ''; ?></textarea>
                    </div>

                    <!-- satus to edit -->
                    <div>
                        <label for="status_edit" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status_edit" 
                                name="status" 
                                required 
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="available" <?php echo isset($book_to_edit) ? ($book_to_edit['status'] == 'available' ? 'selected' : '') : ''; ?>>Available</option>
                            <option value="borrowed" <?php echo isset($book_to_edit) ? ($book_to_edit['status'] == 'borrowed' ? 'selected' : '') : ''; ?>>Borrowed</option>
                            <option value="maintenance" <?php echo isset($book_to_edit) ? ($book_to_edit['status'] == 'maintenance' ? 'selected' : '') : ''; ?>>Maintenance</option>
                        </select>
                    </div>

                    <!-- edit -->
                    <div class="flex space-x-4">
                        <input type="hidden" name="action" value="edit">
                        <button type="submit" 
                                class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Book
                        </button>
                        <a href="./bookCrud.php" 
                           class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>