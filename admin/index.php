<?php

require_once '../classes/User.php';
$u = new User();
$allUsers = $u->getAllUsers();

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
                <a href="#" onclick="loadPage(this, './books.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-book mr-3"></i>
                    Books
                </a>
                <a href="#" onclick="loadPage(this, './categories.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-list-alt mr-3"></i>
                    Categories
                </a>
                <a href="#" onclick="loadPage(this, './statistics.php')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-bar-chart mr-3"></i>
                    Statistics
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

            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    editModal.classList.remove('hidden');
                });
            });

            cancelEdit.addEventListener('click', () => {
                editModal.classList.add('hidden');
            });

            editUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                editModal.classList.add('hidden');
            });

        }
        loadPage(document.querySelector("a"), './dashboard.php');

    </script>
</body>

</html>