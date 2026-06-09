<?php
// profile.php
$title = 'Mon Profil';
ob_start();
session_start();
?>


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
    <!-- Property Rentals -->
    <a href="category.php?cat=property-rentals" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="home" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Property rentals</span>
    </a>
    <!-- Property Sales -->
    <a href="category.php?cat=property-sales" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="shopping-bag" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Property sales</span>
    </a>
    <!-- Lands & Plots -->
    <a href="category.php?cat=lands-plots" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="map" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Lands &amp; Plots</span>
    </a>
    <!-- Vehicles for Sale -->
    <a href="category.php?cat=vehicles-sale" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="truck" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Vehicles for Sale</span>
    </a>
    <!-- Vehicles for Rent -->
    <a href="category.php?cat=vehicles-rent" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="cpu" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Vehicles for rent</span>
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
    <a href="product.php?id=1" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 2: Beautiful Smallholding -->
    <a href="product.php?id=2" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 3: Toyota RAV4 -->
    <a href="product.php?id=3" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 4: Charming Detached House -->
    <a href="product.php?id=4" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 5: Cinderella Toilet -->
    <a href="product.php?id=5" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 6: MacBook Air -->
    <a href="product.php?id=6" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 7: Sunlight T68 Motorhome -->
    <a href="product.php?id=7" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>

    <!-- Card 8: Honda Vintage Motorcycle -->
    <a href="product.php?id=8" class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
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
    </a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>