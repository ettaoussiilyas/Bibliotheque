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
  <title>Login Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            text: {
              50: '#f2fce8',
              100: '#e6fad1',
              200: '#ccf5a3',
              300: '#b3f075',
              400: '#99eb47',
              500: '#80e619',
              600: '#66b814',
              700: '#4d8a0f',
              800: '#335c0a',
              900: '#1a2e05',
            },
            background: {
              50: '#f3fde7',
              100: '#e7fccf',
              200: '#cff8a0',
              300: '#b7f570',
              400: '#9ff240',
              500: '#87ee11',
              600: '#6cbf0d',
              700: '#518f0a',
              800: '#365f07',
              900: '#1b3003',
              950: '#0d1802',
            },
            primary: {
              50: '#f3fee7',
              100: '#e6fccf',
              200: '#cefa9e',
              300: '#b5f76e',
              400: '#9cf53d',
              500: '#83f20d',
              600: '#69c20a',
              700: '#4f9108',
              800: '#356105',
              900: '#1a3003',
              950: '#0d1801',
            },
            secondary: {
              50: '#e7fef6',
              100: '#cffcee',
              200: '#9ff9dd',
              300: '#6ef7cb',
              400: '#3ef4ba',
              500: '#0ef1a9',
              600: '#0bc187',
              700: '#089165',
              800: '#066044',
              900: '#033022',
              950: '#011811',
            },
            accent: {
              50: '#e7fdfe',
              100: '#cffbfc',
              200: '#9ff6f9',
              300: '#6ef2f7',
              400: '#3eeef4',
              500: '#0ee9f1',
              600: '#0bbbc1',
              700: '#088c91',
              800: '#065d60',
              900: '#032f30',
              950: '#011718',
            },
          },
        },
      },
    };
  </script>
</head>
<body class="bg-background-50 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md bg-white shadow-md rounded-lg p-8">
    <h1 class="text-2xl font-semibold text-primary-700 mb-6 text-center">Login</h1>
    <form id="LoginForm" class="space-y-4">
     <div>
        <label for="email" class="block text-sm text-text-700">Email Address</label>
        <input type="email" id="email" name="email" required class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
      </div>
      <div>
        <label for="password" class="block text-sm text-text-700">Password</label>
        <input type="password" id="password" name="password" required minlength="6" class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
      </div>
      
      <button type="submit" class="w-full bg-primary-500 text-white py-2 rounded-lg hover:bg-primary-600">Log In</button>
    </form>
    <p id="error" class="mt-4 text-sm text-red-500 hidden"></p>
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
            'Content-Type': 'application/json', // Ensure the server knows the data type
        },
        body: JSON.stringify({ email, password }), // Convert input data to JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
            errorElement.textContent = 'There was an Error during Login please try again!!';
            errorElement.classList.remove('hidden');
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
                default:
                    break;
            }
        } else {
            console.error('Login failed:', data);
            errorElement.textContent = 'There was an Error during Login please try again!!';
            errorElement.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('An error occurred:', error.message);
        errorElement.textContent = 'There was an Error during Login please try again!!';
        errorElement.classList.remove('hidden');
    });
}

  </script>
</body>
</html>
