<?php
// Services/Admin/index.php
session_start();

// Enforce admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // If logged in but not admin, redirect to Users home, otherwise login
    if (isset($_SESSION['user_id'])) {
        header("Location: ../Users/index.php");
    } else {
        header("Location: ../../Authantification/login.php");
    }
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

// Fetch metrics
try {
    $user_count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $house_count = $pdo->query('SELECT COUNT(*) FROM listings')->fetchColumn();
    $pending_houses = $pdo->query("SELECT COUNT(*) FROM listings WHERE status = 'pending'")->fetchColumn();
    $reservation_count = $pdo->query("SELECT COUNT(*) FROM escrows WHERE status = 'released'")->fetchColumn();
    
    // Fetch recent users
    $stmt = $pdo->query('SELECT full_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5');
    $recent_users = $stmt->fetchAll();
} catch (\PDOException $e) {
    $user_count = 0;
    $house_count = 0;
    $pending_houses = 0;
    $reservation_count = 0;
    $recent_users = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rondera .Bu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Hanken+Grotesk:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        h1, h2, h3 {
            font-family: 'Hanken Grotesk', 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="../Users/index.php" class="flex items-center gap-1.5">
                    <img src="../../uploads/logos/Ro-Bu.png" alt="Rondera" class="h-7 w-auto object-contain">
                    <span class="text-xs bg-red-100 text-red-800 font-extrabold px-2 py-0.5 rounded uppercase tracking-wider">Admin</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-stone-700">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?>
                </span>
                <a href="../../Authantification/logout.php" class="flex items-center gap-1.5 bg-stone-900 hover:bg-black text-white px-4 py-2 rounded-full text-xs font-bold transition shadow-sm">
                    <i data-feather="log-out" class="w-3.5 h-3.5"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <!-- Welcome banner -->
        <div class="bg-gradient-to-r from-stone-900 via-stone-800 to-orange-950 text-white rounded-3xl p-8 mb-8 shadow-md relative overflow-hidden">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#FF7F11]">System Overview</span>
                <h1 class="text-3xl md:text-4xl font-extrabold mt-1">Hello, Administrator</h1>
                <p class="text-stone-300 mt-2 text-sm md:text-base max-w-xl">
                    Manage accounts, properties, transactions, and overview listings across the Rondera platform.
                </p>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Metric 1 -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Total Users</p>
                    <p class="text-3xl font-extrabold text-stone-900 mt-1"><?php echo number_format($user_count); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-[#FF7F11]">
                    <i data-feather="users" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Metric 2 -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Listed Properties</p>
                    <p class="text-3xl font-extrabold text-stone-900 mt-1"><?php echo number_format($house_count); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i data-feather="home" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Metric 3 -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Pending Approvals</p>
                    <p class="text-3xl font-extrabold text-stone-900 mt-1"><?php echo number_format($pending_houses); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-650">
                    <i data-feather="clock" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Metric 4 -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Active Bookings</p>
                    <p class="text-3xl font-extrabold text-stone-900 mt-1"><?php echo number_format($reservation_count); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Registrations (Col 2/3) -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden lg:col-span-2">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-stone-900 text-lg">Recent User Registrations</h3>
                    <span class="text-xs font-semibold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-full">New Accounts</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 border-b border-gray-100 text-xs font-bold text-stone-500 uppercase">
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($recent_users)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-stone-400 font-medium">No registrations found.</td>
                                </tr>
                            <?php else: ?>
                                    <tr class="hover:bg-stone-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-stone-900">
                                            <?php echo htmlspecialchars($r_user['full_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-stone-600">
                                            <?php echo htmlspecialchars($r_user['email']); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider 
                                                <?php 
                                                if($r_user['role'] === 'admin') echo 'bg-red-100 text-red-800';
                                                elseif($r_user['role'] === 'business') echo 'bg-blue-100 text-blue-800';
                                                elseif($r_user['role'] === 'agent') echo 'bg-indigo-100 text-indigo-800';
                                                else echo 'bg-green-100 text-green-800';
                                                ?>">
                                                <?php echo htmlspecialchars($r_user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-stone-500 text-xs">
                                            <?php echo date('M d, Y H:i', strtotime($r_user['created_at'])); ?>
                                        </td>
                                    </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Links / Commands (Col 1/3) -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 flex flex-col gap-5">
                <h3 class="font-extrabold text-stone-900 text-lg">Quick Controls</h3>
                
                <div class="flex flex-col gap-3">
                    <a href="../Users/index.php" class="flex items-center justify-between p-4 bg-stone-50 rounded-xl hover:bg-stone-100 transition border border-gray-100 group">
                        <div class="flex items-center gap-3">
                            <i data-feather="globe" class="text-stone-500 w-5 h-5"></i>
                            <span class="text-sm font-semibold text-stone-850">Go to Marketplace</span>
                        </div>
                        <i data-feather="chevron-right" class="w-4 h-4 text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="#" class="flex items-center justify-between p-4 bg-stone-50 rounded-xl hover:bg-stone-100 transition border border-gray-100 group">
                        <div class="flex items-center gap-3">
                            <i data-feather="shield-off" class="text-stone-500 w-5 h-5"></i>
                            <span class="text-sm font-semibold text-stone-850">Security Audit Logs</span>
                        </div>
                        <i data-feather="lock" class="w-4 h-4 text-stone-400"></i>
                    </a>
                </div>
                
                <div class="mt-auto border-t border-gray-100 pt-5">
                    <div class="flex items-center gap-2 text-xs text-stone-400 leading-relaxed">
                        <i data-feather="shield" class="w-4 h-4 text-[#FF7F11] flex-shrink-0"></i>
                        <span>Administrator actions are encrypted and logged for security auditing.</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-stone-400 font-medium">
            <p>© 1996-2026 Rondera .Bu Admin Portal | All rights reserved.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
    </script>
</body>

</html>
