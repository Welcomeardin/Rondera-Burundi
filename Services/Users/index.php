<?php
$title = 'Mon Profil';
ob_start();
session_start();

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

$products = getAdsFromDatabase($pdo, 20);
?>

<!-- Hero / Search area exactly matching FINN style image: "Hva leter du etter?" -->
<div class="py-8 md:py-12 text-center ">
    <h2 class="text-3xl md:text-4xl font-extrabold text-stone-800 tracking-tight">What are you looking for today?</h2>
    <div class="max-w-3xl mx-auto mt-8">
        <div class="relative bg-white border border-gray-300 rounded search-focus-ring transition-all overflow-hidden flex items-center">
            <input type="text" id="live-search" placeholder="Search for cars, homes, jobs, boats, electronics..." class="w-full py-4 px-4 text-base outline-none bg-transparent">
            <button class="hidden md:flex items-center justify-center w-12 h-12 rounded-full bg-black hover:bg-stone-800 text-white transition mr-2 shrink-0">
                <i data-feather="search" class="w-5 h-5 text-white"></i>
            </button>
        </div>
    </div>
</div>

<!-- CATEGORY GRID : full image-like replication -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5">
    <a href="#" onclick="filterByCategory('property-rentals', event)" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="home" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Property rentals</span>
    </a>
    <a href="#" onclick="filterByCategory('property-sales', event)" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="shopping-bag" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Property sales</span>
    </a>
    <a href="#" onclick="filterByCategory('lands-plots', event)" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="map" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Lands &amp; Plots</span>
    </a>
    <a href="#" onclick="filterByCategory('vehicles-sale', event)" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="truck" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Vehicles for Sale</span>
    </a>
    <a href="#" onclick="filterByCategory('vehicles-rent', event)" class="category-tile flex flex-col items-center gap-2 p-2">
        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center">
            <i data-feather="cpu" class="text-2xl text-[#FF7F11]"></i>
        </div>
        <span class="text-xs font-medium text-stone-700">Vehicles for rent</span>
    </a>
</div>

<hr class="border-t border-gray-300 -mx-4 md:-mx-10 my-6">

<!-- Recommended Listings -->
<div class="flex justify-between items-baseline mt-8 mb-6">
    <h3 id="listings-title" class="text-2xl font-bold text-stone-800 tracking-tight">Recommended for you</h3>
    <a href="index.php" class="text-sm font-medium text-[#984800] hover:underline">See all →</a>
</div>

<div id="listings-container">
    <?php if (empty($products)): ?>
        <div class="text-center py-16">
            <i data-feather="package" class="w-12 h-12 text-stone-300 mx-auto mb-4"></i>
            <h4 class="font-bold text-stone-800 mb-2">No listings yet</h4>
            <p class="text-sm text-stone-500 mb-6">Be the first to post an ad on Rondera.</p>
            <a href="post_ad.php" class="inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-6 py-2.5 rounded-full transition shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($products as $product): 
            $isSaved = isset($_SESSION['user_id']) && isFavorite($pdo, (int) $_SESSION['user_id'], $product['id']);
        ?>
        <a href="product.php?id=<?= (int) $product['id'] ?>" class="product-card bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
            <div class="relative h-48 bg-gray-100">
                <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav" 
                    onclick="event.preventDefault(); toggleFavorite(<?= (int)$product['id'] ?>, this);">
                    <i data-feather="heart" class="w-4 h-4 transition-colors <?= $isSaved ? 'text-red-500 fill-red-500' : '' ?> group-hover/fav:fill-red-500"></i>
                </button>
                <?php if (!empty($product['badge'])): ?>
                <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800"><?= htmlspecialchars($product['badge']) ?></div>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <p class="text-xs text-stone-500 uppercase tracking-wide"><?= htmlspecialchars($product['location']) ?></p>
                <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1"><?= htmlspecialchars($product['title']) ?></h4>
                <p class="text-xl font-bold text-stone-900 mt-2"><?= htmlspecialchars($product['price_display']) ?></p>
                <p class="text-xs text-stone-500 mt-0.5"><?= htmlspecialchars($product['subtitle']) ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
const favoriteLoginUrl = <?= json_encode(loginRedirectUrl('index.php')) ?>;
let currentCategory = '';
let searchTimeout;

document.getElementById('live-search').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(e.target.value);
    }, 300);
});

function filterByCategory(cat, e) {
    if (e) e.preventDefault();
    currentCategory = cat;
    document.getElementById('listings-title').textContent = cat.replace('-', ' ').toUpperCase();
    performSearch(document.getElementById('live-search').value);
}

async function performSearch(query) {
    const container = document.getElementById('listings-container');
    container.style.opacity = '0.5';

    try {
        const url = `search_api.php?q=${encodeURIComponent(query)}&cat=${encodeURIComponent(currentCategory)}`;
        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            renderResults(data.results);
        }
    } catch (e) {
        console.error('Search error:', e);
    } finally {
        container.style.opacity = '1';
    }
}

function renderResults(results) {
    const container = document.getElementById('listings-container');
    
    if (results.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 bg-stone-50 rounded-lg border border-gray-200 w-full">
                <i data-feather="search" class="w-12 h-12 text-stone-300 mx-auto mb-4"></i>
                <h4 class="font-bold text-stone-800 mb-2">No matches found</h4>
                <p class="text-sm text-stone-500">Try adjusting your search or category.</p>
            </div>
        `;
        if (typeof feather !== 'undefined') feather.replace();
        return;
    }

    let html = '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">';
    results.forEach(p => {
        html += `
            <a href="product.php?id=${p.id}" class="product-card bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
                <div class="relative h-48 bg-gray-100">
                    <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="${p.img}" alt="${p.title}">
                    <button class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all duration-200 hover:scale-110 flex items-center justify-center z-10 group/fav" 
                        onclick="event.preventDefault(); toggleFavorite(${p.id}, this);">
                        <i data-feather="heart" class="w-4 h-4 transition-colors group-hover/fav:fill-red-500"></i>
                    </button>
                    ${p.badge ? `<div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">${p.badge}</div>` : ''}
                </div>
                <div class="p-4">
                    <p class="text-xs text-stone-500 uppercase tracking-wide">${p.location}</p>
                    <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1">${p.title}</h4>
                    <p class="text-xl font-bold text-stone-900 mt-2">${p.price_display}</p>
                    <p class="text-xs text-stone-500 mt-0.5">${p.subtitle}</p>
                </div>
            </a>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    if (typeof feather !== 'undefined') feather.replace();
}

async function toggleFavorite(adId, btn) {
    if (!isLoggedIn) {
        window.location.href = favoriteLoginUrl;
        return;
    }

    const icon = btn.querySelector('i');
    const formData = new FormData();
    formData.append('ad_id', adId);

    try {
        const response = await fetch('favorite_handler.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            if (data.saved) {
                icon.classList.add('text-red-500', 'fill-red-500');
            } else {
                icon.classList.remove('text-red-500', 'fill-red-500');
            }
        }
    } catch (e) {
        console.error('Error toggling favorite:', e);
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
