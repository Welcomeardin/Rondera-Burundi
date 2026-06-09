<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// If user is already logged in, redirect them according to their role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ../Services/Admin/index.php');
    } else {
        header('Location: ../Services/Users/index.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'This email address is already registered.';
            } else {
                // Split fullname into nom and prenom for session compatibility
                $parts = explode(' ', $fullname, 2);
                $prenom = $parts[0];
                $nom = isset($parts[1]) ? $parts[1] : '';
                if (empty($nom)) {
                    $nom = $prenom; // fallback
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user
                $insert_stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password_hash, role, status, verified) VALUES (?, ?, ?, ?, \'user\', \'active\', 1)');
                $insert_stmt->execute([$fullname, $email, $telephone, $hashed_password]);

                $user_id = $pdo->lastInsertId();

                // Automatically log user in
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_role'] = 'user';
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;

                // Redirect to Users dashboard
                header('Location: ../Services/Users/index.php');
                exit();
            }
        } catch (\PDOException $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account - Rondera .Bu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Hanken+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3 {
            font-family: 'Hanken Grotesk', 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <!-- Top Horizontal Line & Logo -->
    <div class="w-full flex justify-center pt-8 pb-4 border-b border-gray-300 bg-white">
        <!-- <a href="../index.php">
            <img src="../uploads/logos/Ro-Bu.png" alt="Rondera" class="h-8">
        </a> -->
    </div>

    <!-- Main Content with Vertical Lines -->
    <div class="flex-grow flex items-center justify-center p-4 md:p-8 w-full max-w-[1280px] mx-auto border-l-2 border-r-2 border-gray-300">
        <div class="max-w-[1000px] w-full flex flex-col md:flex-row overflow-hidden relative">

            <!-- Left Side (Blue Hero Area) -->
            <div class="bg-white md:w-1/2 p-10 md:p-14 flex flex-col justify-center">
                <img src="../uploads/logos/Ro-Bu.png" alt="Rondera" class="h-6 w-auto mb-4 object-contain object-left">

                <h1 class="text-3xl md:text-4xl font-extrabold text-stone-900 mb-2 leading-tight tracking-tight">
                    Experience the best version of Rondera
                </h1>

                <p class="text-stone-700 mb-8 text-lg">
                    As a logged-in user you can send messages, save searches and favorites, and create ads.
                </p>

                <div class="mt-auto">
                    <p class="text-sm text-stone-600 mb-1">Problems registering?</p>
                    <a href="#" class="text-sm text-[#0063fb] hover:underline font-medium">Find help here</a>
                </div>
            </div>

            <!-- Right Side (White Form Area) -->
            <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center relative z-10 bg-white rounded-t-3xl md:rounded-t-none md:rounded-l-[2.5rem] md:-ml-8 -mt-8 md:mt-0">
                <div class="max-w-xs mx-auto w-full">

                    <h2 class="text-3xl font-bold text-center mb-2">Create an account</h2>
                    <p class="text-center text-sm text-stone-600 mb-6">
                        Join Rondera.<br>
                        We'll send a confirmation link to your email.
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-4">
                            <label for="fullname" class="block text-sm font-bold text-stone-800 mb-1">Full Name</label>
                            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-bold text-stone-800 mb-1">Email address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all">
                        </div>

                        <div class="mb-4">
                            <label for="telephone" class="block text-sm font-bold text-stone-800 mb-1">Telephone</label>
                            <input type="tel" id="telephone" name="telephone" placeholder="Enter your phone number" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all">
                        </div>

                        <div class="mb-5">
                            <label for="password" class="block text-sm font-bold text-stone-800 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Create a strong password" class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all">
                                <button type="button" id="togglePassword" onclick="togglePasswordVisibility('password','togglePassword')" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 transition-colors p-1 focus:outline-none" aria-label="Show password">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#1e1e1e] hover:bg-black text-white font-bold py-3.5 px-4 rounded-full transition-colors mb-6 shadow-sm">
                            Register
                        </button>
                    </form>

                    <div class="text-center text-sm text-stone-800 font-medium">
                        Already have an account? <a href="login.php" class="text-stone-900 underline hover:no-underline">Log in</a>
                    </div>

                    <!-- <p class="text-[11px] text-stone-500 mt-8 text-center leading-relaxed">
                        By creating an account, you agree to our Terms of Use and Privacy Policy. <a href="#" class="underline">Read more</a>.
                    </p> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <div class="w-full border-t border-b border-gray-300 bg-white">
        <div class="py-8 text-center text-sm font-medium text-stone-600 flex flex-wrap justify-center gap-6 px-4 max-w-[1280px] mx-auto">
            <a href="../index.php" class="hover:underline hover:text-stone-900">Home</a>
            <a href="../Services/about.php" class="hover:underline hover:text-stone-900">About us</a>
            <!-- <a href="#" class="hover:underline hover:text-stone-900">How it works</a> -->
            <a href="../Services/contact.php" class="hover:underline hover:text-stone-900">Contact us</a>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
        function togglePasswordVisibility(inputId, btnId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(btnId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
                ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    </script>
</body>

</html>