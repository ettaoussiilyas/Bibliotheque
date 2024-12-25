<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Catalogue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <nav class="bg-[#2C3E50] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a class="text-white text-2xl font-bold flex items-center" href="index.html">
                    <i class="fas fa-book-reader mr-2"></i>Bibliothèque
                </a>
                <div class="flex space-x-4">
                    <a class="text-white hover:text-[#E74C3C] transition-colors duration-300 flex items-center" href="#">
                        <i class="fas fa-user-plus mr-1"></i> S'inscrire
                    </a>
                    <a class="text-white hover:text-[#E74C3C] transition-colors duration-300 flex items-center" href="#">
                        <i class="fas fa-sign-in-alt mr-1"></i> Se connecter
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-gradient-to-r from-[#2C3E50] to-[#3498DB] text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Découvrez notre collection</h1>
            <p class="text-xl">Explorez notre vaste sélection de livres et trouvez votre prochaine lecture</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Example Book Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="relative h-72">
                    <img src="/api/placeholder/400/320" alt="Book Cover" class="absolute inset-0 w-full h-full object-contain p-2">
                    <div class="absolute top-2 right-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                        Disponible
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xl font-bold text-gray-800 mb-4">Titre du livre</h5>
                    <div class="space-y-2 text-gray-600 mb-6">
                        <p class="flex items-center">
                            <i class="fas fa-user text-[#3498DB] mr-2"></i>
                            Auteur
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-bookmark text-[#3498DB] mr-2"></i>
                            Catégorie
                        </p>
                    </div>
                    <div class="flex justify-between items-center">
                        <button class="bg-[#3498DB] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#2980B9] transition-colors duration-300">
                            <i class="fas fa-info-circle mr-1"></i>
                            Détails
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors duration-300">
                            <i class="fas fa-book-reader mr-1"></i>
                            Emprunter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Titre du livre</h3>
                <button class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-1/3">
                        <img src="/api/placeholder/400/320" alt="Book Cover" class="w-full rounded-lg">
                    </div>
                    <div class="w-full md:w-2/3">
                        <div class="space-y-4">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-user text-[#3498DB] mr-2 w-6"></i>
                                <span>Auteur</span>
                            </p>
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-bookmark text-[#3498DB] mr-2 w-6"></i>
                                <span>Catégorie</span>
                            </p>
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-info-circle text-[#3498DB] mr-2 w-6"></i>
                                <span>Disponible</span>
                            </p>
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold mb-2">Résumé</h4>
                                <p class="text-gray-600">Résumé du livre...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full text-sm font-medium transition-colors duration-300">
                        <i class="fas fa-book-reader mr-1"></i>
                        Emprunter
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>