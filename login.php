<?php 

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bibliothèque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-[#2C3E50] to-[#3498DB] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Bienvenue</h1>
            <p class="text-gray-500">Connectez-vous à votre compte</p>
        </div>

        <form id="LoginForm" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3498DB] focus:border-transparent"
                            placeholder="Entrez votre email">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-2">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3498DB] focus:border-transparent"
                            placeholder="Entrez votre mot de passe">
                    </div>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-[#2C3E50] to-[#3498DB] text-white py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                Se connecter
            </button>
        </form>

        <div class="text-center">
            <p class="text-gray-600">Vous n'avez pas de compte ?
                <a href="register.php" class="text-[#3498DB] font-medium hover:underline">S'inscrire</a>
            </p>
        </div>

        <p id="error" class="text-center text-red-500 hidden"></p>
    </div>

    <script>
        const form = document.getElementById('LoginForm');
        const errorElement = document.getElementById('error');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            register(email, password);
            form.reset();
        });

        function register(email, password) {
            fetch('./api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    switch (data.destination) {
                        case 'dashboard.php':
                            document.location = './user.php';
                            break;
                        case 'admin/dashboard.php':
                            document.location = './admin/';
                            break;
                    }
                } else {
                    errorElement.textContent = 'Email ou mot de passe incorrect';
                    errorElement.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('An error occurred:', error.message);
                errorElement.textContent = 'Une erreur est survenue, veuillez réessayer';
                errorElement.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>
