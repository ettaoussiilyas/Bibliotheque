
<?php
session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: ../index.php');
    exit;
}
require_once '../classes/User.php';
$u = new User();
$allUsers = $u->getAllUsers();
if(isset($_GET['success'])){
    echo '<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="text-xl font-bold mb-4">Success</h2>
                <p class="mb-4">' . htmlspecialchars($_GET['success']) . '</p>
                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-green-500 text-white rounded" onclick="this.parentElement.parentElement.parentElement.classList.add(\'hidden\')">Ok</button>
                </div>
            </div>
        </div>';
}elseif (isset($_GET['error'])) {
    echo '<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="text-xl font-bold mb-4">Success</h2>
                <p class="mb-4">' . htmlspecialchars($_GET['error']) . '</p>
                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-red-500 text-white rounded" onclick="this.parentElement.parentElement.parentElement.classList.add(\'hidden\')">Ok</button>
                </div>
            </div>
        </div>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200">
            <div class="p-4">
                <h1 class="text-xl font-bold text-gray-800">Bibiloteque</h1>
            </div>
            <nav class="mt-4">
                <a href="#" onclick="loadPage(this, './dashboard.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
                <a href="#" onclick="loadPage(this, './users.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-user mr-3"></i>
                    Users
                </a>
                <a href="#" onclick="loadPage(this, './bookCrud.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-book mr-3"></i>
                    Books
                </a>
                <a href="#" onclick="loadPage(this, './categorycrud.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-list-alt mr-3"></i>
                    Categories
                </a>
                <a href="#" onclick="loadPage(this, './statistics.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-bar-chart mr-3"></i>
                    Statistics
                </a>
                <a href="#" onclick="loadPage(this, './user-books.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-book-reader mr-3"></i>
                    Emprunts
                </a>
            </nav>
            <div class="p-4 mt-8">
                <a href="../logout.php"
                    class="w-full transition-all font-bold bg-red-100 text-red-500 px-4 py-2 rounded-lg hover:bg-red-200 hover:text-red-700 block text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto" id="content">
        </main>
    </div>

    <script>
        var current;
        function loadPage(ele, page) {
            fetch(page)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                    current = ele.textContent.trim().toLowerCase();
                    switch (current) {
                        case "users":
                            users();
                            break;
                        case 'emprunts':
                            initializeEvents();
                            break;
                    }
                    
                });
            ele.classList.add("text-gray-700", "bg-gray-100");
            var allele = document.querySelectorAll("a .mr-3");
            allele.forEach(e => {
                if (e.parentElement !== ele) {
                    e.parentElement.classList.remove("bg-gray-100", "text-gray-700");
                    e.parentElement.classList.add("text-gray-600", "hover:bg-gray-50");
                }
            });
        }

        function initializeEvents() {
            const forms = document.querySelectorAll('#returnform');
            forms.forEach(form => {

            
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (confirm('Êtes-vous sûr de vouloir marquer ce livre comme retourné ?')) {
                        const formData = new FormData(this);
                        
                        fetch('./user-books.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                        
                                const empruntsLink = document.querySelector('a[onclick*="user-books.php"]');
                                if (empruntsLink) {
                                    loadPage(empruntsLink, './user-books.php');
                                }
                           
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Une erreur est survenue lors du retour du livre.');
                        });
                    }
                });
            });
        }

        function showDelete(ele){
            const deleteButtons = document.querySelectorAll('button[title="Delete"]');
            const deleteModal = document.getElementById('deleteModal');
            const cancelDelete = document.getElementById('cancelDelete');
            const confirmDelete = document.getElementById('confirmDelete');
            deleteModal.classList.remove('hidden');
            cancelDelete.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
            });

            confirmDelete.addEventListener('click', () => {
                var email = ele.parentElement.parentElement.querySelectorAll('td')[1].textContent.trim();
                fetch(`/api/deleteUser.php?user=${email}`).then(response => response.text()).then(res => {
                    if(data !== "ok"){
                        alert("Error Occured While deleting user!");
                    }
                })
                deleteModal.classList.add('hidden');
            });

        }
        function users() {
            
            const deleteButtons = document.querySelectorAll('button[title="Delete"]');
            const deleteModal = document.getElementById('deleteModal');
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    showDelete(button);
                });
            });

            

            const editButtons = document.querySelectorAll('button[title="Edit"]');
            const editModal = document.getElementById('editModal');
            const cancelEdit = document.getElementById('cancelEdit');
            const editUserForm = document.getElementById('editUserForm');
            var ee;
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    editModal.classList.remove('hidden');
                    ee = button;
                    editModal.querySelector("#editName").value = button.parentElement.parentElement.querySelectorAll('td')[0].textContent.trim();
                    editModal.querySelector("#editEmail").value = button.parentElement.parentElement.querySelectorAll('td')[1].textContent.trim();
                    editModal.querySelector("#editRole").value = button.parentElement.parentElement.querySelectorAll('td')[2].textContent.trim();
                    
                });
            });

            cancelEdit.addEventListener('click', () => {
                editModal.classList.add('hidden');
            });

            editUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                console.log(ee);
                var email = ee.parentElement.parentElement.querySelectorAll('td')[1].textContent.trim(),
                newemail = editModal.querySelector("#editEmail").value,
                newname = editModal.querySelector("#editName").value,
                newrole = editModal.querySelector("#editRole").value;
                fetch(`/api/updateUser.php?email=${email}&newemail=${newemail}&newname=${newname}&newrole=${newrole}`).then(response => response.text()).then(res => {
                    if(data !== "ok"){
                        alert("Error Occured While deleting user!");
                    }
                })
                editModal.classList.add('hidden');
            });

        }
        function reloadUserBooks() {
            fetch('./user-books.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                    initializeUserBooks();
                });
        }
        loadPage(document.querySelector("a"), './dashboard.php');





        function showAddForm() {
            document.getElementById('addCategoryModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addCategoryModal').classList.add('hidden');
        }

        function showEditForm(category) {
            // Debug - Afficher les données reçues
            console.log('Données du livre:', category);
            
            // Remplir le formulaire
            // document.getElementById('edit_category_id').value = category.category_id;
            document.getElementById('edit_category_id').value = category.id;
            document.getElementById('edit_name').value = category.name;
            
            // Afficher le modal
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }

        function sendNow(email, name, date){
            fetch(`/api/sendEmail.php?email=${email}&name=${name}&date=${date}`).then(response => response.text()).then(data => {
                switch(data){
                    case 'ok':
                        document.querySelector('button[title="SendEmail"]').classList.add("hidden");
                        break;
                    default:
                        alert("Error Sending the email");
                                    }
            })
        }

        function showAddFormBooks() {
            document.getElementById('addBookModal').classList.remove('hidden');
        }

        function closeAddModalBooks() {
            document.getElementById('addBookModal').classList.add('hidden');
        }

        function showEditFormBooks(book) {
            // Debug - Afficher les données reçues
            console.log('Données du livre:', book);
            
            // Remplir le formulaire
            document.getElementById('edit_book_id').value = book.id;
            document.getElementById('edit_title').value = book.title;
            document.getElementById('edit_author').value = book.author;
            document.getElementById('edit_category_id').value = book.category_id;
            document.getElementById('edit_cover_image').value = book.cover_image || '';
            document.getElementById('edit_summary').value = book.summary || '';
            document.getElementById('edit_status').value = book.status;
            
            // Afficher le modal
            document.getElementById('editBookModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editBookModal').classList.add('hidden');
        }
    </script>
</body>

</html>