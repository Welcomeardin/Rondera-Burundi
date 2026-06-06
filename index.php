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
                <div class="flex items-center gap-1">
                    <img src="Ro-Bu.png" alt="Rondera .Bu" class="h-8" />
                </div>
            </div>

            <!-- desktop navigation: For Businesses, Notifications, Post Ad, Messages, Log In -->
            <nav class="hidden md:flex items-center gap-5 text-sm font-semibold text-stone-800">
                <a href="#" class="flex items-center hover:text-[#984800] transition"><i data-feather="briefcase"></i><span class="ml-1">For Businesses</span></a>
                <a href="#" class="flex items-center hover:text-[#984800] transition"><i data-feather="bell"></i><span class="ml-1">Notifications</span></a>
                <a href="#" class="flex items-center hover:text-[#984800] transition"><i data-feather="plus-circle"></i><span class="ml-1">Post Ad</span></a>
                <a href="#" class="flex items-center hover:text-[#984800] transition"><i data-feather="message-square"></i><span class="ml-1">Messages</span></a>
            </nav>
            <!-- right side icons + sell button -->
            <div class="flex items-center gap-3">
                <button class="hidden md:flex items-center gap-1 bg-[#FF7F11] text-white px-5 py-2 rounded text-sm font-bold hover:bg-[#e06d0c] transition shadow-sm">
                    <!-- <i data-feather="user-circle" class="text-[18px]"></i> -->
                    <span>Log in</span>
                </button>
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

        <!-- Hero / Search area exactly matching FINN style image: "Hva leter du etter?" -->
        <div class="py-8 md:py-12 text-center ">
            <h2 class="text-3xl md:text-4xl font-extrabold text-stone-800 tracking-tight">What are you looking for today?</h2>
            <div class="max-w-3xl mx-auto mt-8">
                <div class="relative bg-white border border-gray-300 rounded search-focus-ring transition-all overflow-hidden flex items-center">
                    <input type="text" placeholder="Search for cars, homes, jobs, boats, electronics..." class="w-full py-4 px-4 text-base outline-none bg-transparent">
                    <button class="hidden md:flex items-center justify-center w-12 h-12 rounded-full bg-black hover:bg-stone-800 text-white transition mr-2 shrink-0">
                        <i data-feather="search" class="w-5 h-5 text-white"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- CATEGORY GRID : full image-like replication -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5">
            <!-- Property -->
            <a href="#" class="category-tile flex flex-col items-center gap-2 p-2">
                <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
                    <i data-feather="home" class="text-2xl text-[#FF7F11]"></i>
                </div>
                <span class="text-xs font-medium text-stone-700">Property</span>
            </a>
            <!-- Clothing & Fashion -->
            <a href="#" class="category-tile flex flex-col items-center gap-2 p-2">
                <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
                    <i data-feather="shopping-bag" class="text-2xl text-[#FF7F11]"></i>
                </div>
                <span class="text-xs font-medium text-stone-700">Clothing &amp; Fashion</span>
            </a>
            <!-- Travel -->
            <a href="#" class="category-tile flex flex-col items-center gap-2 p-2">
                <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
                    <i data-feather="send" class="text-2xl text-[#FF7F11]"></i>
                </div>
                <span class="text-xs font-medium text-stone-700">Travel</span>
            </a>
            <!-- Car -->
            <a href="#" class="category-tile flex flex-col items-center gap-2 p-2">
                <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
                    <i data-feather="truck" class="text-2xl text-[#FF7F11]"></i>
                </div>
                <span class="text-xs font-medium text-stone-700">Car</span>
            </a>
            <!-- Bicycle -->
            <a href="#" class="category-tile flex flex-col items-center gap-2 p-2">
                <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
                    <i data-feather="cpu" class="text-2xl text-[#FF7F11]"></i>
                </div>
                <span class="text-xs font-medium text-stone-700">Electronics</span>
            </a>
        </div>

        <hr class="border-t border-gray-300 -mx-4 md:-mx-10 my-6">


        <!-- Recommended Listings -->
        <div class="flex justify-between items-baseline mt-8 mb-6">
            <h3 class="text-2xl font-bold text-stone-800 tracking-tight">Recommended for you</h3>
            <a href="#" class="text-sm font-medium text-[#984800] hover:underline">See all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Card 1: Unique & Charming Cabin -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=600&auto=format&fit=crop&q=80" alt="Holiday Home">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">🏷️ Recommended</div>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Nykirke</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Unique &amp; charming holiday home by Borrevannet</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">3,000,000 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Total price: 3,076,090 kr</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 2: Beautiful Smallholding -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=600&auto=format&fit=crop&q=80" alt="Smallholding">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">🏷️ Featured</div>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Spongdal</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Large and beautiful smallholding of approx. 11.6 acres</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">10,490,000 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Total price: 10,753,340 kr</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 3: Toyota RAV4 -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1619767886558-efdc259cde1a?w=600&auto=format&fit=crop&q=80" alt="Toyota RAV4">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Oslo</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Toyota RAV4 Plug-In Hybrid - Low Mileage</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">359,900 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Year: 2021 | 45,000 km</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 4: Charming Detached House -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&auto=format&fit=crop&q=80" alt="Detached House">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">🏷️ Hot Offer</div>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Oslo / Skjetten</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Charming 3-story detached house with garden</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">19,000,000 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">5 bedrooms | 210 m²</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 5: Cinderella Toilet -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&auto=format&fit=crop&q=80" alt="Incineration Toilet">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Tranby</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Cinderella Ecocat incineration toilet - Like New</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">5,500 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Cleaned and fully serviced</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 6: MacBook Air -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop&q=80" alt="MacBook Air">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">🏷️ Popular</div>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Bergen</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">MacBook Air M2 - Space Gray, 256GB SSD</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">5,990 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Battery health: 98%</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 7: Sunlight T68 Motorhome -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=600&auto=format&fit=crop&q=80" alt="Motorhome">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">🏷️ Weekly Deal</div>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Jessheim</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Sunlight T68 Motorhome - Perfect Family Camper</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">599,000 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Year: 2019 | Fully equipped</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>

            <!-- Card 8: Honda Vintage Motorcycle -->
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&auto=format&fit=crop&q=80" alt="Motorcycle">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">Kvelde</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">Honda CB 350 F Vintage Motorcycle</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">47,000 kr</p>
                    <p class="text-xs text-stone-500 mt-0.5">Year: 1973 | Excellent condition</p>
                    <div class="flex mt-3 gap-2"><span class="badge-fiks-ferdig">✔️ Ready to Ship</span></div>
                </div>
            </div>
        </div>


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

</body>

</html>