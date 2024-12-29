<?php

    require_once '../config/db.php';
    require_once '../classes/categories.php';

    //connection
    $database = new DataBase();
    $conn = $database->getConnection();

    //category object
    $category = new Categoty($conn);
    $category_all_rows = $category->getAllCategorys();

    // echo '<pre>';
    // print_r($category_all_rows);
    // echo '</pre>';
    // echo '<hr>';
    // // print_r($category_all_rows['name']);
    // echo '<hr>';

    $errors = isset($_SESSION['category_errors']) ? $_SESSION['category_errors'] : [];
    unset($_SESSION['category_errors']);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch($_POST['action']) {
        case 'add':
            if ($category->addingCategory($_POST)) {
                header('Location: categorycrud.php?success=added');
            } else {
                $_SESSION['category_errors'] = $category->getErrorMessage();
                header('Location: categorycrud.php?error=add');
            }
            exit;

        case 'edit':
            // print_r($_POST);
            // die();
            $result = $category->editCategory($_POST);
            if ($result) {
                header('Location: categorycrud.php?success=updated');
            } else {
                $_SESSION['category_errors'] = $category->getErrorMessage();
                echo $category->getErrorMessage();
                header('Location: categorycrud.php?error=ed123it');
            }
            exit;

        case 'delete':
            if ($category->deletecCategory($_POST['category_id'])) {
                header('Location: categorycrud.php?success=deleted');
            } else {
                header('Location: categorycrud.php?error=delete');
            }
            exit;
    }
}

// Load category for editing if ID is provided
$categoy_to_edit = null;
if (isset($_GET['edit'])) {
    $categoy_to_edit = $category->getCategoryById($_GET['edit']);
}

?>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Category Management</h1>
            <button onclick="showAddForm()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </button>
        </div>

        <!-- category Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($category_all_rows as $category_all_row): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="p-6">
                        <h5 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($category_all_row['name']); ?></h5>
                        <div class="flex justify-between items-center">
                            <button onclick='showEditForm(<?php echo json_encode([
                                "id" => $category_all_row["id"],
                                "name" => $category_all_row["name"]
                            ]); ?>)'
                                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_id" value="<?php echo $category_all_row['id']; ?>">
                                <button type="submit" 
                                        class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition-colors duration-300">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full <?php echo isset($_GET['error']) && $_GET['error'] === 'add' ? '' : 'hidden'; ?>">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Add New Category</h3>
                <button onclick="closeAddModal()" class="text-gray-600 hover:text-gray-800">
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
            <form action="categorycrud.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()"
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

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Edit Category</h3>
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

            <form action="categorycrud.php" method="POST" class="space-y-4" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_category_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="edit_name" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

