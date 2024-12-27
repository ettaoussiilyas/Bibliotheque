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
        function loadPage(ele, page) {
            fetch(page)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                });
            ele.classList.add("text-gray-700", "bg-gray-100");
            var allele = document.querySelectorAll("a .mr-3");
            allele.forEach(e => {
                if (e.parentElement !== ele) {
                    e.parentElement.classList.remove("bg-gray-100", "text-gray-700");
                    e.parentElement.classList.add("text-gray-600", "hover:bg-gray-50");

                }
            })
        }
        loadPage(document.querySelector("a"), './dashboard.php');
    </script>
</body>

</html>