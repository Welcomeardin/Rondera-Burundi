<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Rondera .Bu — Marketplace for everything | Buy & Sell used cars, homes, jobs, boats</title>
    <!-- Tailwind + Fonts + Material Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Hanken+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* custom overrides to match image: FINN.no authentic feel, cream background, unique category styling */
        body {
            background-color: #FFFFFF;
            /* surface-cream from design system — warm scandi tone */
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        h1,
        h2,
        h3,
        .font-headline {
            font-family: 'Hanken Grotesk', 'Inter', sans-serif;
            letter-spacing: -0.01em;
        }

        /* Category hover effect identical to FINN.no style */
        .category-tile {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .category-tile:hover {
            transform: translateY(-3px);
        }

        /* product card subtle lift */
        .product-card {
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -12px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .badge-fiks-ferdig {
            background: #2C6E2F;
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .price-total {
            font-size: 13px;
            color: #6b5a4e;
            font-weight: 450;
        }

        /* custom ring for search */
        .search-focus-ring:focus-within {
            box-shadow: 0 0 0 2px #FF7F11;
        }
    </style>
</head>

<body class="antialiased">

    <!-- ========== HEADER (exactly as FINN image: logo, menu, notifications, sell button) ========= -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-300">
        <div class="max-w-[1280px] mx-4 md:mx-auto px-4 md:px-10 py-3 flex items-center justify-between">
            <!-- left section: menu + logo -->
            <div class="flex items-center gap-3">
                <button class="md:hidden p-1.5  hover:bg-gray-100 transition">
                    <i data-feather="menu" class="text-2xl text-stone-700"></i>
                </button>
                <a href="index.php" class="flex items-center gap-1">
                    <img src="../../uploads/logos/Ro-Bu.png" alt="Rondera .Bu" class="h-8" />
                </a>
            </div>

            <!-- desktop navigation: For Businesses, Notifications, Post Ad, Messages, Log In -->
            <nav class="hidden md:flex items-center gap-5 text-sm font-semibold text-stone-800">
                <a href="for_businesses.php" class="flex items-center hover:text-[#984800] transition"><i data-feather="briefcase"></i><span class="ml-1">For Businesses</span></a>
                <a href="notification.php" class="flex items-center hover:text-[#984800] transition"><i data-feather="bell"></i><span class="ml-1">Notifications</span></a>
                <a href="post_ad.php" class="flex items-center hover:text-[#984800] transition"><i data-feather="plus-circle"></i><span class="ml-1">Post Ad</span></a>
                <a href="chat.php" class="flex items-center hover:text-[#984800] transition"><i data-feather="message-square"></i><span class="ml-1">Messages</span></a>
            </nav>
            <!-- right side icons + sell button -->
            <div class="flex items-center gap-3 relative">
                <?php if (isset($_SESSION['user_id'])): 
                    $initials = strtoupper(substr($_SESSION['user_prenom'] ?? '', 0, 1) . substr($_SESSION['user_nom'] ?? '', 0, 1));
                    if (empty($initials)) {
                        $initials = 'US';
                    }
                ?>
                    <!-- Logged in state dropdown button -->
                    <div class="relative inline-block text-left">
                        <button id="profileDropdownBtn" class="flex items-center justify-center w-10 h-10 rounded-full bg-[#FF7F11] hover:bg-[#e06d0c] text-white font-bold text-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF7F11] cursor-pointer">
                            <?php echo htmlspecialchars($initials); ?>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 transition-all transform origin-top-right scale-95 duration-100">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs text-stone-500">Logged in as</p>
                                    <p class="text-sm font-bold text-stone-850 truncate"><?php echo htmlspecialchars(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')); ?></p>
                                </div>
                                <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition font-medium" role="menuitem">
                                    <i data-feather="user" class="w-4 h-4 mr-2.5 text-stone-500"></i> My Profile
                                </a>
                                <a href="../../Authantification/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition font-medium" role="menuitem">
                                    <i data-feather="log-out" class="w-4 h-4 mr-2.5 text-red-500"></i> Log out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../../Authantification/login.php" class="hidden md:flex items-center gap-1 bg-[#FF7F11] text-white px-5 py-2 rounded text-sm font-bold hover:bg-[#e06d0c] transition shadow-sm">
                        <span>Log in</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- mobile bottom floating nav is separate, but we also display quick categories bar from image -->
        <div class="md:hidden border-t border-gray-100 bg-white px-4 py-2 flex overflow-x-auto gap-6 text-xs font-medium text-stone-700 scrollbar-hide">
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="truck" class="text-[#FF7F11]"></i> Car</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="home"></i> Property</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="briefcase"></i> Job</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="cpu"></i> Electronics</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="anchor"></i> Boat</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="chair"></i> Furniture</a>
            <a href="#" class="flex flex-col items-center gap-0.5 min-w-max"><i data-feather="home"></i> Cabin</a>
        </div>
    </header>

    <main class="max-w-[1280px] mx-4 md:mx-auto px-4 md:px-10 pb-24 md:pb-12 border-l-2 border-r-2 border-gray-300 pl-4 pr-4">
        <?php
        if (isset($content)) {
            echo $content;
        } else {
            echo "No content found";
        }
        ?>

    </main>

    <!-- FOOTER: exactly as image style: bedriftskunde, informasjon, personvern, FINNspirasjon etc -->
    <footer class="bg-white border-t border-gray-300 pt-10 pb-6">
        <div class="max-w-[1280px] mx-4 md:mx-auto px-4 md:px-10 ">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-black text-[#984800] text-xl">Rondera</h4>
                    <p class="text-sm text-stone-600 mt-2">Your premier marketplace</p>
                    <div class="flex gap-2 mt-4"><i data-feather="lock" class="text-stone-500"></i><span class="text-xs">Safe trading with Fiks ferdig</span></div>
                </div>
                <div>
                    <h5 class="font-bold">Marketplace</h5>
                    <ul class="text-sm space-y-1 mt-2 text-stone-700">
                        <li>Become a business customer</li>
                        <li>Information and inspiration</li>
                        <li>Admin for businesses</li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold">About Rondera</h5>
                    <ul class="text-sm space-y-1 mt-2">
                        <li>Privacy</li>
                        <li>Careers</li>
                        <li>Customer Service</li>
                        <li>Rondera Inspiration</li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold">Collaboration & Trust</h5>
                    <ul class="text-sm">
                        <li>Helthjem · Lendo · Nettbil</li>
                        <li>Safe trade – Fiks ferdig</li>
                        <li>Privacy Policy</li>
                        <li>Cookie Settings</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-stone-300 mt-8 pt-6 text-xs text-stone-500 flex flex-col md:flex-row justify-between">
                <p>© 1996-2026 Rondera .Bu | All rights reserved.</p>
                <div class="flex gap-6 mt-2 md:mt-0"><a href="#">Privacy at Rondera</a><a href="#">Accessibility Statement</a><a href="#">Terms of Use</a></div>
            </div>
            <div class="border-t border-stone-300 mt-8 pt-8 text-center">
                <h1 class="text-5xl md:text-8xl lg:text-9xl font-black tracking-tighter select-none leading-none">
                    <span class="text-[#FF7F11]">Rondera</span> <span class="text-black">Burundi.</span>
                </h1>
            </div>
        </div>
    </footer>

    <!-- mobile bottom bar consistent -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around py-2 z-50 shadow-lg">
        <a href="#" class="flex flex-col items-center text-[#FF7F11]"><i data-feather="home"></i><span class="text-[10px]">Home</span></a>
        <a href="#" class="flex flex-col items-center text-stone-600"><i data-feather="search"></i><span class="text-[10px]">Search</span></a>
        <a href="#" class="flex flex-col items-center text-stone-600"><i data-feather="plus-circle" class="text-2xl text-[#FF7F11]"></i><span class="text-[10px]">Sell</span></a>
        <a href="#" class="flex flex-col items-center text-stone-600"><i data-feather="heart"></i><span class="text-[10px]">Favorites</span></a>
        <a href="#" class="flex flex-col items-center text-stone-600"><i data-feather="user"></i><span class="text-[10px]">My Account</span></a>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
    </script>
    <script>
        // Dropdown toggle logic
        const dropdownBtn = document.getElementById('profileDropdownBtn');
        const dropdown = document.getElementById('profileDropdown');

        if (dropdownBtn && dropdown) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('scale-100');
                    dropdown.classList.add('scale-95');
                } else {
                    dropdown.classList.remove('scale-95');
                    dropdown.classList.add('scale-100');
                }
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== dropdownBtn) {
                    dropdown.classList.add('hidden');
                    dropdown.classList.remove('scale-100');
                    dropdown.classList.add('scale-95');
                }
            });
        }
    </script>

</body>

</html>