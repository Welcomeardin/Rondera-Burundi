<?php
$title = 'Edit Ad';
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

// Get ad ID
$ad_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing ad data
try {
    $stmt = $pdo->prepare('SELECT * FROM ads WHERE id = ? AND user_id = ?');
    $stmt->execute([$ad_id, $_SESSION['user_id']]);
    $ad = $stmt->fetch();
    
    if (!$ad) {
        $error = 'Ad not found or you do not have permission to edit it.';
    }
} catch (\PDOException $e) {
    $error = 'Failed to load ad: ' . $e->getMessage();
    $ad = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ad) {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['desc'] ?? '');
    $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
    $price_day = isset($_POST['price_day']) ? (float)$_POST['price_day'] : null;
    
    // Validate required fields
    if (empty($title) || empty($location) || empty($description)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // Use price_day for rentals, price for sales
            $final_price = $price ?: $price_day;
            
            // Update ad
            $stmt = $pdo->prepare('UPDATE ads SET title = ?, description = ?, price = ?, location = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$title, $description, $final_price, $location, $ad_id, $_SESSION['user_id']]);
            
            // Handle new photo uploads
            if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                $upload_dir = sys_get_temp_dir() . '/rondera_ads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (is_writable($upload_dir)) {
                    $files = $_FILES['photos'];
                    $file_count = count($files['name']);
                    
                    // Check if there are already primary photos
                    $checkPrimary = $pdo->prepare('SELECT COUNT(*) FROM ad_photos WHERE ad_id = ? AND is_primary = TRUE');
                    $checkPrimary->execute([$ad_id]);
                    $hasPrimary = $checkPrimary->fetchColumn() > 0;
                    
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $files['tmp_name'][$i];
                            $file_name = $files['name'][$i];
                            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                            
                            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $new_filename = 'ad_' . $ad_id . '_' . uniqid() . '.' . $file_ext;
                                $upload_path = $upload_dir . $new_filename;
                                
                                if (move_uploaded_file($tmp_name, $upload_path)) {
                                    $is_primary = (!$hasPrimary && $i === 0); 
                                    $stmt = $pdo->prepare('INSERT INTO ad_photos (ad_id, photo_path, is_primary) VALUES (?, ?, ?)');
                                    $stmt->execute([$ad_id, $upload_path, $is_primary]);
                                }
                            }
                        }
                    }
                }
            }
            
            $success = 'Your ad has been successfully updated!';
            
            // Refresh ad data
            $stmt = $pdo->prepare('SELECT * FROM ads WHERE id = ? AND user_id = ?');
            $stmt->execute([$ad_id, $_SESSION['user_id']]);
            $ad = $stmt->fetch();
            
        } catch (\PDOException $e) {
            $error = 'Failed to update ad: ' . $e->getMessage();
        }
    }
}

// Fetch photos for this ad
$photos = [];
if ($ad) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM ad_photos WHERE ad_id = ? ORDER BY is_primary DESC, created_at ASC');
        $stmt->execute([$ad_id]);
        $photos = $stmt->fetchAll();
    } catch (\PDOException $e) {
        // Ignore photo errors
    }
}
?>

<style>
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        color: #1c1917;
        background: #fff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        border-color: #FF7F11;
        box-shadow: 0 0 0 3px rgba(255,127,17,0.12);
    }
    textarea.form-input { resize: vertical; min-height: 110px; }
</style>

<div class="py-6 px-4 md:px-0">

    <?php if (!$ad): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-feather="alert-circle" class="w-8 h-8 text-red-500"></i>
        </div>
        <h3 class="text-lg font-bold text-stone-900 mb-2"><?php echo htmlspecialchars($error); ?></h3>
        <a href="myad.php" class="inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-6 py-2.5 rounded-full transition shadow-sm">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back to My Ads
        </a>
    </div>

    <?php else: ?>

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-stone-400 mb-6">
        <a href="myad.php" class="hover:text-stone-800 transition">My Ads</a>
        <i data-feather="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-stone-900 font-semibold">Edit Ad</span>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-5 p-4 rounded bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mb-5 p-4 rounded bg-green-50 text-green-700 text-sm font-semibold border border-green-200">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Form Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-[#FF7F11] flex items-center justify-center flex-shrink-0">
            <i data-feather="edit-2" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Edit Ad</h1>
            <p class="text-sm text-stone-400">Update your listing details</p>
        </div>
    </div>

    <!-- Existing Photos -->
    <?php if (!empty($photos)): ?>
    <div class="mb-8">
        <h3 class="text-sm font-bold text-stone-800 mb-3">Current Photos</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach($photos as $photo): ?>
            <div class="relative group">
                <img src="<?php echo htmlspecialchars('ad_photo.php?id=' . (int) $photo['id']); ?>" 
                     alt="Ad photo" 
                     class="w-full h-32 object-cover rounded">
                <?php if ($photo['is_primary']): ?>
                <span class="absolute top-2 left-2 bg-[#FF7F11] text-white text-[10px] px-2 py-0.5 rounded-full font-bold">Primary</span>
                <?php endif; ?>
                <form method="POST" action="delete_photo.php" class="absolute top-2 right-2">
                    <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                    <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">
                    <button type="submit" onclick="return confirm('Delete this photo?')" class="w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-sm">
                        <i data-feather="x" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="bg-stone-50 border-b border-gray-100 px-7 py-4">
            <p class="text-sm font-semibold text-stone-700">Edit your listing details below</p>
            <p class="text-xs text-stone-400 mt-0.5">All fields are required unless marked optional.</p>
        </div>

        <form action="edit_ad.php?id=<?php echo $ad_id; ?>" method="POST" enctype="multipart/form-data" class="p-7">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="title" class="text-sm font-bold text-stone-800">Listing Title</label>
                    <input type="text" name="title" id="title" 
                           class="form-input" 
                           placeholder="e.g. Spacious 3-bedroom apartment near city center"
                           value="<?php echo htmlspecialchars($ad['title'] ?? ''); ?>" required>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="location" class="text-sm font-bold text-stone-800">Location / City</label>
                    <input type="text" name="location" id="location" 
                           class="form-input" 
                           placeholder="e.g. Bujumbura, Rohero"
                           value="<?php echo htmlspecialchars($ad['location'] ?? ''); ?>" required>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="price" class="text-sm font-bold text-stone-800">Price (BIF)</label>
                    <input type="number" name="price" id="price" 
                           class="form-input" 
                           placeholder="e.g. 250000"
                           value="<?php echo htmlspecialchars($ad['price'] ?? ''); ?>">
                </div>

                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="desc" class="text-sm font-bold text-stone-800">Description</label>
                    <textarea name="desc" id="desc"
                              class="form-input" 
                              placeholder="Describe the property, amenities, neighborhood..." required><?php echo htmlspecialchars($ad['description'] ?? ''); ?></textarea>
                </div>

                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-sm font-bold text-stone-800">Add More Photos</label>
                    <label class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-gray-200 rounded p-6 cursor-pointer hover:border-[#FF7F11] transition bg-stone-50">
                        <i data-feather="upload-cloud" class="w-8 h-8 text-stone-300"></i>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-stone-600">Click to upload more photos</p>
                            <p class="text-xs text-stone-400">PNG, JPG up to 10MB each</p>
                        </div>
                        <input type="file" name="photos[]" id="photos"
                            class="hidden" accept="image/*" multiple>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <a href="myad.php" class="w-full md:w-auto text-center px-6 py-2.5 rounded border border-gray-200 text-sm font-bold text-stone-600 hover:bg-stone-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="w-full md:w-auto justify-center px-8 py-2.5 rounded bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold transition shadow-sm flex items-center gap-2">
                    <i data-feather="save" class="w-4 h-4"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
