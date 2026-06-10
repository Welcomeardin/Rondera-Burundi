<?php
session_start();
require_once __DIR__ . '/includes/marketplace_helpers.php';

if (!isset($_SESSION['user_id'])) {
    $return = $_SERVER['REQUEST_URI'] ?? 'product.php';
    header('Location: ' . loginRedirectUrl($return));
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

$buyerId = (int) $_SESSION['user_id'];
$adId = (int) ($_POST['ad_id'] ?? $_GET['ad_id'] ?? 0);
$notes = trim($_POST['notes'] ?? '');
$error = '';

if ($adId <= 0) {
    header('Location: index.php');
    exit();
}

$product = getProductById($pdo, $adId);
if (!$product) {
    header('Location: index.php');
    exit();
}

$sellerId = (int) ($product['seller_id'] ?? 0);
if ($sellerId <= 0) {
    header('Location: product.php?id=' . $adId . '&error=seller');
    exit();
}

if ($sellerId === $buyerId) {
    $error = 'You cannot order your own listing.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === '') {
    try {
        $amount = $product['price'] ?? null;

        $stmt = $pdo->prepare('
            INSERT INTO orders (buyer_id, seller_id, ad_id, amount, status, notes)
            VALUES (?, ?, ?, ?, \'pending\', ?)
        ');
        $stmt->execute([$buyerId, $sellerId, $adId, $amount, $notes ?: null]);
        $orderId = (int) $pdo->lastInsertId();

        $orderMessage = 'I placed an order for "' . $product['title'] . '" (Order #' . $orderId . ').';
        if ($notes !== '') {
            $orderMessage .= ' Note: ' . $notes;
        }

        sendMessage($pdo, $buyerId, $sellerId, $adId, $orderMessage);

        header('Location: chat.php?ad_id=' . $adId . '&user_id=' . $sellerId . '&order=success');
        exit();
    } catch (PDOException $e) {
        $error = 'Could not place order. Please try again.';
    }
}

$title = 'Place Order';
ob_start();
?>

<div class="py-6 max-w-lg mx-auto">
    <a href="product.php?id=<?= $adId ?>" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-stone-900 mb-6 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to listing
    </a>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h1 class="text-2xl font-extrabold text-stone-900 mb-2">Confirm your order</h1>
        <p class="text-sm text-stone-500 mb-6">Review the listing below before placing your order.</p>

        <?php if ($error): ?>
            <div class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="flex gap-4 items-center mb-6 p-4 bg-stone-50 rounded-xl border border-gray-100">
            <img src="<?= htmlspecialchars($product['img']) ?>" alt="" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
            <div class="min-w-0">
                <p class="font-bold text-stone-900 text-sm line-clamp-2"><?= htmlspecialchars($product['title']) ?></p>
                <p class="text-lg font-extrabold text-[#FF7F11] mt-1"><?= htmlspecialchars($product['price_display']) ?></p>
                <p class="text-xs text-stone-500 mt-0.5">Seller: <?= htmlspecialchars($product['owner']['name']) ?></p>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="ad_id" value="<?= $adId ?>">
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Message to seller (optional)</label>
                <textarea name="notes" rows="3" placeholder="Add delivery details or questions..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#FF7F11]/30"></textarea>
            </div>

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                <i data-feather="shopping-cart" class="w-4 h-4"></i> Place Order
            </button>
        </form>

        <p class="text-xs text-stone-400 mt-4 flex items-center gap-1.5">
            <i data-feather="shield" class="w-3.5 h-3.5 text-green-500"></i>
            Your order will be sent to the seller and you can continue chatting.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
