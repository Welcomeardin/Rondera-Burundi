<?php
$title = 'My Ads';
ob_start();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

$error = '';
$success = '';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ad'])) {
    $ad_id = (int)$_POST['ad_id'];
    try {
        // First delete associated photos
        $stmt = $pdo->prepare('DELETE FROM ad_photos WHERE ad_id = ?');
        $stmt->execute([$ad_id]);
        
        // Then delete the ad
        $stmt = $pdo->prepare('DELETE FROM ads WHERE id = ? AND user_id = ?');
        $stmt->execute([$ad_id, $_SESSION['user_id']]);
        
        $success = 'Ad deleted successfully!';
    } catch (\PDOException $e) {
        $error = 'Failed to delete ad: ' . $e->getMessage();
    }
}

// Fetch user's ads with photos
try {
    $stmt = $pdo->prepare('
        SELECT a.*, 
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id AND is_primary = TRUE LIMIT 1) as primary_photo,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id LIMIT 1) as first_photo
        FROM ads a 
        WHERE a.user_id = ? 
        ORDER BY a.created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $ads = $stmt->fetchAll();
} catch (\PDOException $e) {
    $error = 'Failed to load your ads.';
    $ads = [];
}
?>

<div class="py-6">

    <!-- Top bar -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-bold text-stone-900">My Ads</h1>
            <p class="text-sm text-stone-500 mt-1">Manage your listings</p>
        </div>
        <a href="post_ad.php" class="flex items-center gap-1.5 px-4 py-2 bg-[#FF7F11] hover:bg-[#e06c09] rounded-lg text-sm font-semibold text-white transition">
            <i data-feather="plus" class="w-4 h-4"></i> Post new ad
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div id="error-message" class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div id="success-message" class="mb-5 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold border border-green-200">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Ads Grid -->
    <?php if (empty($ads)): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="file-text" class="w-8 h-8 text-stone-300"></i>
            </div>
            <h3 class="text-lg font-bold text-stone-900 mb-2">No ads yet</h3>
            <p class="text-stone-500 text-sm mb-6">You haven't posted any ads yet. Start by posting your first ad!</p>
            <a href="post_ad.php" class="inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-6 py-2.5 rounded-full transition shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post your first ad
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach($ads as $ad): ?>
                <div class="product-card bg-white rounded overflow-hidden border border-gray-200 shadow-sm relative group hover:shadow-md transition-shadow cursor-pointer" onclick="window.location.href='edit_ad.php?id=<?php echo $ad['id']; ?>'">
                    <div class="relative h-48 bg-gray-100">
                        <?php 
                        $photo_path = $ad['primary_photo'] ?: $ad['first_photo'];
                        if ($photo_path): 
                        ?>
                            <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="<?php echo htmlspecialchars(resolveProductImage($photo_path, (int) $ad['id'])); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i data-feather="home" class="w-12 h-12 text-stone-300"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">
                            <?php echo $ad['status'] === 'active' ? '🏷️ Active' : '🏷️ ' . ucfirst($ad['status'] ?? 'Pending'); ?>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-stone-500 uppercase tracking-wide"><?php echo htmlspecialchars($ad['location'] ?? ''); ?></p>
                        <h4 class="font-bold text-stone-800 text-md line-clamp-1 mt-1"><?php echo htmlspecialchars($ad['title']); ?></h4>
                        <p class="text-xl font-bold text-stone-900 mt-2">
                            <?php echo $ad['price'] ? number_format($ad['price'], 0, ',', ' ') . ' BIF' : 'Contact for price'; ?>
                        </p>
                        <p class="text-xs text-stone-500 mt-0.5"><?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $ad['category'] ?? 'Property'))); ?></p>
                        <div class="flex mt-4 pt-4 border-t border-gray-50 gap-2 justify-between items-center">
                            <div class="flex gap-2 w-full">
                                <a href="edit_ad.php?id=<?php echo $ad['id']; ?>" 
                                   class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded bg-stone-100 hover:bg-stone-200 transition text-stone-700 text-xs font-bold" 
                                   onclick="event.stopPropagation()">
                                    <i data-feather="edit-2" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                <form method="POST" class="flex-1" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this ad?');">
                                    <input type="hidden" name="delete_ad" value="1">
                                    <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-2 rounded bg-red-50 hover:bg-red-100 transition text-red-600 text-xs font-bold cursor-pointer">
                                        <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
// Fade out success message after 5 seconds
setTimeout(function() {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        successMessage.style.transition = 'opacity 0.5s ease';
        successMessage.style.opacity = '0';
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 500);
    }
}, 5000);

// Fade out error message after 5 seconds
setTimeout(function() {
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        errorMessage.style.transition = 'opacity 0.5s ease';
        errorMessage.style.opacity = '0';
        setTimeout(function() {
            errorMessage.style.display = 'none';
        }, 500);
    }
}, 5000);
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>