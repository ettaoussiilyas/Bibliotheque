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
                <a href="#" class="flex items-center px-4 py-2 text-gray-700 bg-gray-100">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-user mr-3"></i>
                    Users
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-book mr-3"></i>
                    Books
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-list-alt mr-3"></i>
                    Categories
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-bar-chart mr-3"></i>
                    Statistics
                </a>
            </nav>
            <div class="p-4 mt-8">
                <button class="w-full transition-all font-bold bg-red-100 text-red-500 px-4 py-2 rounded-lg hover:bg-red-200 hover:text-red-700">
                    Logout
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                    <div class="relative">
                        <input type="text" placeholder="Search for projects" class="w-96 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-600">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- GitHub banner -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-400 p-4 rounded-lg text-white mb-8 flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-star mr-2"></i>
                        <span>Star this project on GitHub</span>
                    </div>
                    <button class="text-white">View more →</button>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm text-gray-500">Total clients</h3>
                                <p class="text-2xl font-bold">6389</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm text-gray-500">Account balance</h3>
                                <p class="text-2xl font-bold">$46,760.89</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm text-gray-500">New sales</h3>
                                <p class="text-2xl font-bold">376</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm text-gray-500">Pending contacts</h3>
                                <p class="text-2xl font-bold">35</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b">
                                <th class="p-4">CLIENT</th>
                                <th class="p-4">AMOUNT</th>
                                <th class="p-4">STATUS</th>
                                <th class="p-4">DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <img src="/api/placeholder/40/40" alt="Hans" class="w-10 h-10 rounded-full">
                                        <div class="ml-3">
                                            <div class="font-medium">Hans Burger</div>
                                            <div class="text-sm text-gray-500">10x Developer</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">$863.45</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Approved</span>
                                </td>
                                <td class="p-4 text-gray-500">6/10/2020</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add any JavaScript functionality here
        // For example, handling sidebar toggles, search functionality, etc.
    </script>
</body>
</html>