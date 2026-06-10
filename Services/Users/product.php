<?php
$title = 'Product Details';
ob_start();
session_start();

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

$p = getProductById($pdo, $id);

if (!$p) {
    header('Location: index.php');
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isOwner = $isLoggedIn && isset($p['seller_id']) && (int) $p['seller_id'] === (int) $_SESSION['user_id'];

$orderUrl = $isLoggedIn
    ? 'order_handler.php?ad_id=' . $id
    : loginRedirectUrl('order_handler.php?ad_id=' . $id);

$chatUrl = $isLoggedIn
    ? 'chat.php?ad_id=' . $id . ($p['seller_id'] ? '&user_id=' . (int) $p['seller_id'] : '')
    : loginRedirectUrl('chat.php?ad_id=' . $id);

$isSaved = $isLoggedIn && isFavorite($pdo, (int) $_SESSION['user_id'], $id);
$favoriteLoginUrl = loginRedirectUrl('product.php?id=' . $id);
?>

<div class="py-6 max-w-5xl mx-auto">

    <a href="index.php" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-stone-900 mb-6 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to listings
    </a>

    <div class="flex flex-col lg:flex-row gap-8">

        <div class="flex-1 min-w-0">
            <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-4">
                <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="w-full h-72 md:h-96 object-cover">
            </div>

            <div class="flex gap-2 mb-6">
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="h-16 w-24 rounded-lg overflow-hidden border <?= $i === 0 ? 'border-[#FF7F11] ring-2 ring-[#FF7F11]/30' : 'border-gray-200' ?> flex-shrink-0 cursor-pointer">
                    <img src="<?= htmlspecialchars($p['img']) ?>" class="w-full h-full object-cover" alt="">
                </div>
                <?php endfor; ?>
            </div>

            <?php if (!empty($p['badge'])): ?>
            <span class="inline-block bg-orange-50 text-[#FF7F11] text-xs font-bold px-3 py-1 rounded-full mb-3"><?= htmlspecialchars($p['badge']) ?></span>
            <?php endif; ?>
            <h1 class="text-2xl md:text-3xl font-extrabold text-stone-900 mb-1 tracking-tight"><?= htmlspecialchars($p['title']) ?></h1>
            <p class="text-sm text-stone-400 mb-4 flex items-center gap-1">
                <i data-feather="map-pin" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($p['location']) ?>
                &nbsp;·&nbsp; <span class="text-stone-400"><?= htmlspecialchars($p['category']) ?></span>
            </p>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-3 text-base">About this listing</h2>
                <p class="text-stone-600 text-sm leading-relaxed"><?= htmlspecialchars($p['desc']) ?></p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-4 text-base">Specifications</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($p['specs'] as $key => $val): ?>
                    <div class="bg-stone-50 rounded-xl p-3">
                        <p class="text-xs text-stone-400 mb-0.5"><?= htmlspecialchars($key) ?></p>
                        <p class="text-sm font-bold text-stone-800"><?= htmlspecialchars($val) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4">

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <p class="text-3xl font-extrabold text-stone-900"><?= htmlspecialchars($p['price_display']) ?></p>
                <?php if (($p['price_display'] ?? '') !== ($p['total_price'] ?? '')): ?>
                <p class="text-xs text-stone-400 mt-0.5">Total incl. fees: <?= htmlspecialchars($p['total_price']) ?></p>
                <?php endif; ?>

                <div class="mt-4 flex flex-col gap-2.5">
                    <?php if ($isOwner): ?>
                        <p class="text-sm text-stone-500 text-center py-2">This is your listing.</p>
                        <a href="myad.php" class="w-full flex items-center justify-center gap-2 bg-black hover:bg-stone-800 text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                            <i data-feather="edit" class="w-4 h-4"></i> Manage My Ad
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($orderUrl) ?>"
                            class="w-full flex items-center justify-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                            <i data-feather="shopping-cart" class="w-4 h-4"></i> Make an Order
                        </a>
                        <a href="<?= htmlspecialchars($chatUrl) ?>"
                            class="w-full flex items-center justify-center gap-2 bg-black hover:bg-stone-800 text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                            <i data-feather="message-circle" class="w-4 h-4"></i> Send a Message
                        </a>
                    <?php endif; ?>
                    <button type="button" id="favorite-btn"
                        onclick="toggleFavorite(<?= $id ?>)"
                        class="w-full flex items-center justify-center gap-2 border-2 font-bold py-2.5 rounded-full transition text-sm <?= $isSaved ? 'border-red-300 text-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300 text-stone-600 hover:text-red-500' ?>">
                        <i data-feather="heart" class="w-4 h-4 <?= $isSaved ? 'fill-red-500' : '' ?>" id="favorite-icon"></i>
                        <span id="favorite-label"><?= $isSaved ? 'Saved to Favorites' : 'Save to Favorites' ?></span>
                    </button>
                </div>

                <div class="border-t border-gray-100 mt-5 pt-4 flex items-center gap-1.5 text-xs text-stone-400">
                    <i data-feather="shield" class="w-3.5 h-3.5 text-green-500"></i>
                    <span>Secure transactions on Rondera</span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-4 text-sm">Seller Information</h2>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#FF7F11] flex items-center justify-center text-white font-extrabold text-lg flex-shrink-0">
                        <?= userInitials($p['owner']['name']) ?>
                    </div>
                    <div>
                        <p class="font-bold text-stone-900 text-sm"><?= htmlspecialchars($p['owner']['name']) ?></p>
                        <p class="text-xs text-stone-400"><?= htmlspecialchars($p['owner']['since']) ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center">
                    <div class="bg-stone-50 rounded-xl p-2.5">
                        <p class="text-lg font-extrabold text-[#FF7F11]"><?= htmlspecialchars($p['owner']['rating']) ?></p>
                        <p class="text-[10px] text-stone-400">Rating</p>
                    </div>
                    <div class="bg-stone-50 rounded-xl p-2.5">
                        <p class="text-lg font-extrabold text-stone-900"><?= explode(' ', $p['owner']['ads'])[0] ?></p>
                        <p class="text-[10px] text-stone-400">Active Ads</p>
                    </div>
                </div>
                <?php if (!$isOwner): ?>
                <a href="<?= htmlspecialchars($chatUrl) ?>" class="mt-4 w-full flex items-center justify-center gap-2 text-sm font-bold text-[#FF7F11] hover:underline">
                    <i data-feather="message-square" class="w-4 h-4"></i> Contact Seller
                </a>
                <?php endif; ?>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-3 text-sm flex items-center gap-2">
                    <i data-feather="map-pin" class="w-4 h-4 text-[#FF7F11]"></i> Location
                </h2>
                <p class="text-sm text-stone-700 font-semibold"><?= htmlspecialchars($p['location']) ?></p>
                <div class="mt-3 h-28 bg-stone-100 rounded-xl flex items-center justify-center text-stone-400 text-xs">
                    <i data-feather="map" class="w-5 h-5 mr-1"></i> Map preview
                </div>
            </div>

            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 text-xs text-stone-600 leading-relaxed">
                <p class="font-bold text-stone-800 mb-1 flex items-center gap-1.5">
                    <i data-feather="alert-circle" class="w-3.5 h-3.5 text-[#FF7F11]"></i> Safety Tips
                </p>
                Never pay in advance before seeing the item. Meet in a public place when possible. Report suspicious listings.
            </div>

        </div>
    </div>
</div>

<script>
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
const favoriteLoginUrl = <?= json_encode($favoriteLoginUrl) ?>;

async function toggleFavorite(adId) {
    if (!isLoggedIn) {
        window.location.href = favoriteLoginUrl;
        return;
    }

    const formData = new FormData();
    formData.append('ad_id', adId);

    const response = await fetch('favorite_handler.php', { method: 'POST', body: formData });
    const data = await response.json();

    if (!data.success) return;

    const btn = document.getElementById('favorite-btn');
    const label = document.getElementById('favorite-label');
    const icon = document.getElementById('favorite-icon');

    if (data.saved) {
        btn.className = 'w-full flex items-center justify-center gap-2 border-2 font-bold py-2.5 rounded-full transition text-sm border-red-300 text-red-500 bg-red-50';
        label.textContent = 'Saved to Favorites';
        icon.classList.add('fill-red-500');
    } else {
        btn.className = 'w-full flex items-center justify-center gap-2 border-2 font-bold py-2.5 rounded-full transition text-sm border-gray-200 hover:border-red-300 text-stone-600 hover:text-red-500';
        label.textContent = 'Save to Favorites';
        icon.classList.remove('fill-red-500');
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
