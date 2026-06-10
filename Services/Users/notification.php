<?php
$title = 'Notifications';
ob_start();
session_start();

require_once __DIR__ . '/includes/marketplace_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . loginRedirectUrl('notification.php'));
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

$userId = (int) $_SESSION['user_id'];
$orders = getUserOrders($pdo, $userId);
$favorites = getUserFavorites($pdo, $userId);
$pendingOrderCount = getPendingOrderNotificationCount($pdo, $userId);

$statusLabels = [
    'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-800'],
    'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-blue-100 text-blue-800'],
    'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-100 text-red-800'],
    'completed' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-800'],
];

function formatNotifDate(string $datetime): string
{
    $time = strtotime($datetime);
    if ($time >= strtotime('today')) {
        return 'Today · ' . date('g:i A', $time);
    }
    if ($time >= strtotime('yesterday')) {
        return 'Yesterday · ' . date('g:i A', $time);
    }
    return date('M j, Y · g:i A', $time);
}
?>

<div class="py-8">
    <h1 class="text-3xl md:text-4xl font-extrabold text-stone-900 mb-6 tracking-tight">Notifications</h1>

    <div class="flex flex-col lg:flex-row gap-8">

        <div class="flex-1 min-w-0">

            <div class="border-b border-gray-200 mb-8">
                <nav class="flex gap-0 -mb-px" id="notif-tabs">
                    <button onclick="switchTab('orders')" id="tab-orders"
                        class="tab-btn px-6 py-3 text-sm font-bold border-b-2 border-[#FF7F11] text-[#FF7F11] transition-all flex items-center gap-2">
                        <i data-feather="shopping-bag" class="w-4 h-4"></i>
                        Orders
                        <?php if ($pendingOrderCount > 0): ?>
                            <span class="w-5 h-5 bg-[#FF7F11] rounded-full text-white text-[10px] font-bold flex items-center justify-center"><?= $pendingOrderCount ?></span>
                        <?php endif; ?>
                    </button>
                    <button onclick="switchTab('favorites')" id="tab-favorites"
                        class="tab-btn px-6 py-3 text-sm font-bold border-b-2 border-transparent text-stone-500 hover:text-stone-800 transition-all flex items-center gap-2">
                        <i data-feather="heart" class="w-4 h-4"></i>
                        Favorites
                        <?php if (count($favorites) > 0): ?>
                            <span class="w-5 h-5 bg-stone-200 text-stone-700 rounded-full text-[10px] font-bold flex items-center justify-center"><?= count($favorites) ?></span>
                        <?php endif; ?>
                    </button>
                </nav>
            </div>

            <!-- Orders -->
            <div id="panel-orders">
                <?php if (empty($orders)): ?>
                    <div class="flex flex-col items-center justify-center py-16 text-center max-w-md mx-auto">
                        <div class="w-16 h-16 bg-[#FF7F11]/10 rounded-full flex items-center justify-center mb-6">
                            <i data-feather="shopping-bag" class="w-7 h-7 text-[#FF7F11]"></i>
                        </div>
                        <p class="text-base font-extrabold text-stone-900 mb-2">No orders yet</p>
                        <p class="text-sm text-stone-500 leading-relaxed">
                            When you place or receive an order, it will show up here.
                        </p>
                        <a href="index.php"
                            class="mt-8 inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white font-bold px-7 py-3 rounded-full transition shadow-sm text-sm">
                            <i data-feather="search" class="w-4 h-4"></i>
                            Browse listings
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($orders as $order):
                            $status = $statusLabels[$order['status']] ?? ['label' => ucfirst($order['status']), 'class' => 'bg-stone-100 text-stone-700'];
                        ?>
                            <div class="bg-white border border-gray-200 rounded p-4 md:p-5 transition-shadow">
                                <div class="flex gap-4">
                                    <a href="product.php?id=<?= (int) $order['ad_id'] ?>" class="flex-shrink-0">
                                        <img src="<?= htmlspecialchars($order['img']) ?>" alt=""
                                            class="w-20 h-20 md:w-24 md:h-24 rounded-lg object-cover border border-gray-100">
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-start justify-between gap-2 mb-1">
                                            <div>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide text-stone-400 mb-1">
                                                    <i data-feather="<?= $order['is_seller'] ? 'arrow-down-left' : 'arrow-up-right' ?>" class="w-3 h-3"></i>
                                                    <?= $order['is_seller'] ? 'Order received' : 'Order placed' ?>
                                                </span>
                                                <h3 class="font-bold text-stone-900 text-sm md:text-base truncate">
                                                    <a href="product.php?id=<?= (int) $order['ad_id'] ?>" class="hover:text-[#FF7F11] transition">
                                                        <?= htmlspecialchars($order['ad_title']) ?>
                                                    </a>
                                                </h3>
                                            </div>
                                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full <?= $status['class'] ?>">
                                                <?= htmlspecialchars($status['label']) ?>
                                            </span>
                                        </div>

                                        <p class="text-lg font-extrabold text-[#FF7F11] mb-1"><?= htmlspecialchars($order['amount_display']) ?></p>
                                        <p class="text-xs text-stone-500 mb-2">
                                            Order #<?= (int) $order['id'] ?>
                                            · <?= $order['is_seller'] ? 'Buyer' : 'Seller' ?>: <?= htmlspecialchars($order['other_user_name']) ?>
                                            · <?= formatNotifDate($order['created_at']) ?>
                                        </p>

                                        <?php if (!empty($order['notes'])): ?>
                                            <p class="text-xs text-stone-900 bg-stone-50 rounded px-3 py-2 mb-2 border border-gray-100">
                                                <?= htmlspecialchars($order['notes']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="flex flex-wrap gap-2 mt-2" id="order-actions-<?= (int) $order['id'] ?>">
                                            <?php if ($order['is_seller'] && $order['status'] === 'pending'): ?>
                                                <button onclick="updateOrder(<?= (int) $order['id'] ?>, 'confirmed')"
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full transition shadow-sm">
                                                    <i data-feather="check" class="w-3.5 h-3.5"></i>
                                                    Confirm Order
                                                </button>
                                                <button onclick="updateOrder(<?= (int) $order['id'] ?>, 'cancelled')"
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-full transition border border-red-100">
                                                    <i data-feather="x" class="w-3.5 h-3.5"></i>
                                                    Decline
                                                </button>
                                            <?php elseif ($order['is_seller'] && $order['status'] === 'confirmed'): ?>
                                                <button onclick="updateOrder(<?= (int) $order['id'] ?>, 'completed')"
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-stone-900 hover:bg-black text-white px-3 py-1.5 rounded-full transition shadow-sm">
                                                    <i data-feather="package" class="w-3.5 h-3.5"></i>
                                                    Mark Completed
                                                </button>
                                            <?php endif; ?>

                                            <a href="chat.php?user_id=<?= (int) $order['other_user_id'] ?>&ad_id=<?= (int) $order['ad_id'] ?>"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#FF7F11] hover:underline px-2 py-1.5">
                                                <i data-feather="message-circle" class="w-3.5 h-3.5"></i>
                                                Message <?= $order['is_seller'] ? 'buyer' : 'seller' ?>
                                            </a>
                                            <a href="product.php?id=<?= (int) $order['ad_id'] ?>"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:underline px-2 py-1.5">
                                                <i data-feather="external-link" class="w-3.5 h-3.5"></i>
                                                View listing
                                            </a>
                                            <button onclick="deleteOrder(<?= (int) $order['id'] ?>)"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 hover:underline px-2 py-1.5 ml-auto">
                                                <i data-feather="trash-2" class="w-3.5 h-3.5"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Favorites -->
            <div id="panel-favorites" class="hidden">
                <?php if (empty($favorites)): ?>
                    <div class="flex flex-col items-center justify-center py-16 text-center max-w-md mx-auto">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-6">
                            <i data-feather="heart" class="w-7 h-7 text-red-400"></i>
                        </div>
                        <p class="text-base font-extrabold text-stone-900 mb-2">No favorites yet</p>
                        <p class="text-sm text-stone-500 leading-relaxed">
                            Tap the heart on any listing to save it here and find it quickly later.
                        </p>
                        <a href="index.php"
                            class="mt-8 inline-flex items-center gap-2 bg-black hover:bg-stone-800 text-white font-bold px-7 py-3 rounded-full transition shadow-sm text-sm">
                            <i data-feather="search" class="w-4 h-4"></i>
                            Start browsing
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($favorites as $fav): ?>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group" id="fav-card-<?= (int) $fav['id'] ?>">
                                <a href="product.php?id=<?= (int) $fav['id'] ?>" class="block">
                                    <div class="relative">
                                        <img src="<?= htmlspecialchars($fav['img']) ?>" alt="<?= htmlspecialchars($fav['title']) ?>"
                                            class="w-full h-40 object-cover">
                                        <button type="button"
                                            onclick="event.preventDefault(); removeFavorite(<?= (int) $fav['id'] ?>)"
                                            class="absolute top-3 right-3 w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-sm transition"
                                            title="Remove from favorites">
                                            <i data-feather="heart" class="w-4 h-4 text-red-500 fill-red-500"></i>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-stone-900 text-sm truncate group-hover:text-[#FF7F11] transition"><?= htmlspecialchars($fav['title']) ?></h3>
                                        <p class="text-lg font-extrabold text-[#FF7F11] mt-1"><?= htmlspecialchars($fav['price_display']) ?></p>
                                        <p class="text-xs text-stone-400 mt-1 flex items-center gap-1">
                                            <i data-feather="map-pin" class="w-3 h-3"></i>
                                            <?= htmlspecialchars($fav['location']) ?>
                                        </p>
                                        <p class="text-[10px] text-stone-400 mt-2">Saved <?= formatNotifDate($fav['saved_at']) ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Sidebar summary -->
        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="border border-gray-200 rounded p-6 bg-white space-y-5">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-[#FF7F11] flex items-center justify-center">
                            <i data-feather="shopping-bag" class="w-4 h-4 text-white"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-stone-900">Orders</h2>
                    </div>
                    <p class="text-2xl font-extrabold text-stone-900"><?= count($orders) ?></p>
                    <p class="text-xs text-stone-500 mt-1">
                        <?php if ($pendingOrderCount > 0): ?>
                            <?= $pendingOrderCount ?> pending order<?= $pendingOrderCount > 1 ? 's' : '' ?> to review
                        <?php else: ?>
                            All caught up
                        <?php endif; ?>
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center">
                            <i data-feather="heart" class="w-4 h-4 text-red-500"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-stone-900">Favorites</h2>
                    </div>
                    <p class="text-2xl font-extrabold text-stone-900"><?= count($favorites) ?></p>
                    <p class="text-xs text-stone-500 mt-1">Listings you saved with the heart</p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <a href="index.php"
                        class="w-full flex items-center justify-center gap-2 border-2 border-[#FF7F11] text-[#FF7F11] hover:bg-[#FF7F11] hover:text-white font-bold px-4 py-2.5 rounded-full transition text-sm">
                        <i data-feather="search" class="w-4 h-4"></i>
                        Browse more
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('panel-orders').classList.toggle('hidden', tab !== 'orders');
    document.getElementById('panel-favorites').classList.toggle('hidden', tab !== 'favorites');

    ['orders', 'favorites'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.classList.remove('border-transparent', 'text-stone-500');
            btn.classList.add('border-[#FF7F11]', 'text-[#FF7F11]');
        } else {
            btn.classList.add('border-transparent', 'text-stone-500');
            btn.classList.remove('border-[#FF7F11]', 'text-[#FF7F11]');
        }
    });

    if (typeof feather !== 'undefined') feather.replace();
}

async function removeFavorite(adId) {
    const formData = new FormData();
    formData.append('ad_id', adId);

    const response = await fetch('favorite_handler.php', { method: 'POST', body: formData });
    const data = await response.json();

    if (data.success && !data.saved) {
        const card = document.getElementById('fav-card-' + adId);
        if (card) {
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.remove(), 300);
        }
    }
}

async function updateOrder(orderId, status) {
    if (!confirm('Are you sure you want to mark this order as ' + status + '?')) return;

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('status', status);

    try {
        const response = await fetch('order_status_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error updating order: ' + (data.error || 'Unknown error'));
        }
    } catch (e) {
        alert('Network error. Please try again.');
    }
}

async function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order from your history?')) return;

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('action', 'delete');

    try {
        const response = await fetch('order_status_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error deleting order: ' + (data.error || 'Unknown error'));
        }
    } catch (e) {
        alert('Network error. Please try again.');
    }
}

const urlTab = new URLSearchParams(window.location.search).get('tab');
if (urlTab === 'favorites') switchTab('favorites');
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
