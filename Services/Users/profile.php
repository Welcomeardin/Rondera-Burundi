<?php
// profile.php
$title = 'My Profile';
ob_start();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

$error = '';
$success = '';

// Fetch user data
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (\PDOException $e) {
    $error = 'Failed to load profile data.';
}

// Count user stats
try {
    $fav_count = $pdo->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
    $fav_count->execute([$_SESSION['user_id']]);
    $favorites = $fav_count->fetchColumn();

    $msg_count = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE sender_id = ? AND is_read = 0');
    $msg_count->execute([$_SESSION['user_id']]);
    $unread_messages = $msg_count->fetchColumn();

    // marketplace_db does not have notifications table in the exact same schema, default to 0
    $unread_notifs = 0;
} catch (\PDOException $e) {
    $favorites = 0;
    $unread_messages = 0;
    $unread_notifs = 0;
}

// Update profile details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    if (empty($prenom) || empty($nom)) {
        $error = 'First name and Last name are required.';
    } else {
        try {
            $fullname = $prenom . ' ' . $nom;
            $update_stmt = $pdo->prepare('UPDATE users SET full_name = ?, phone = ? WHERE id = ?');
            $update_stmt->execute([$fullname, $telephone, $_SESSION['user_id']]);
            
            // Update session data
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_nom'] = $nom;
            
            $success = 'Profile updated successfully!';
            
            // Reload user data
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (\PDOException $e) {
            $error = 'Failed to update profile: ' . $e->getMessage();
        }
    }
}

// Split full name for form values
$nameParts = explode(' ', $user['full_name'] ?? '', 2);
$userPrenom = $nameParts[0] ?? '';
$userNom = $nameParts[1] ?? '';

$initials = strtoupper(substr($userPrenom, 0, 1) . substr($userNom, 0, 1));
if (empty($initials)) $initials = 'US';
$fullname = $user['full_name'] ?? '';
$joinDate = isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '';
?>

<div class="py-6">

    <!-- Top bar -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-xl font-bold text-stone-900">My Rondera</h1>
        <a href="../../Authantification/logout.php" class="flex items-center gap-1.5 px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-stone-700 hover:bg-stone-50 transition">
            <i data-feather="log-out" class="w-4 h-4"></i> Log out
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mb-5 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold border border-green-200">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Profile Header Card -->
    <div class="flex items-center gap-5 mb-6">
        <div class="w-24 h-24 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center flex-shrink-0 shadow-sm">
            <?php if (!empty($user['photo_profil'])): ?>
                <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
            <?php else: ?>
                <div class="w-full h-full rounded-full bg-stone-200 flex items-center justify-center">
                    <i data-feather="user" class="w-10 h-10 text-stone-400"></i>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="text-2xl font-extrabold text-stone-900"><?php echo htmlspecialchars($fullname); ?></h2>
            <p class="text-sm text-stone-500 mt-0.5"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <?php if ($joinDate): ?>
                <p class="text-xs text-[#0063fb] font-medium mt-1">Member since <?php echo $joinDate; ?></p>
            <?php endif; ?>
            <button onclick="document.getElementById('editProfileModal').classList.remove('hidden')" class="mt-2 inline-flex items-center gap-1.5 px-4 py-1.5 border border-gray-300 rounded-full text-xs font-bold text-stone-700 hover:bg-stone-50 transition">
                <i data-feather="edit-2" class="w-3 h-3"></i> Edit profile
            </button>
        </div>
    </div>

    <!-- Verification Banner -->
    <div class="flex items-center justify-between bg-stone-50 border border-gray-200 rounded-2xl p-5 mb-8">
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 bg-[#0063fb] rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <i data-feather="shield" class="w-3.5 h-3.5 text-white"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-stone-900">Help make Rondera safer</p>
                <p class="text-xs text-stone-500 mt-0.5 max-w-lg">Verify your identity to access all features. You appear more reliable to others and it will increase your chance of a successful trade.</p>
            </div>
        </div>
        <button class="flex-shrink-0 inline-flex items-center gap-2 bg-[#0063fb] hover:bg-[#004fc7] text-white text-sm font-bold px-5 py-2.5 rounded-lg transition shadow-sm">
            <i data-feather="check-circle" class="w-4 h-4"></i> Verify Account
        </button>
    </div>

    <!-- Feature Tiles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

        <!-- My account -->
        <button onclick="document.getElementById('editProfileModal').classList.remove('hidden')" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group">
            <div class="mb-4">
                <i data-feather="user" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">My account</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">View your information on Rondera</p>
        </button>

        <!-- My ads -->
        <a href="post_ad.php" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4">
                <i data-feather="file-text" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">My ads</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">View all your ads and get an overview of statistics</p>
        </a>

        <!-- Favorites -->
        <a href="#" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4 relative">
                <i data-feather="heart" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
                <?php if ($favorites > 0): ?>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF7F11] text-white text-[9px] font-bold rounded-full flex items-center justify-center"><?php echo $favorites; ?></span>
                <?php endif; ?>
            </div>
            <h3 class="text-base font-bold text-stone-900">Favorites</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">See all the ads you like and have added as your favorite</p>
        </a>

        <!-- Saved searches -->
        <a href="notification.php" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4">
                <i data-feather="search" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">Saved searches</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">Keep track and customize your saved searches</p>
        </a>

        <!-- Messages -->
        <a href="chat.php" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4 relative">
                <i data-feather="message-square" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
                <?php if ($unread_messages > 0): ?>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF7F11] text-white text-[9px] font-bold rounded-full flex items-center justify-center"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </div>
            <h3 class="text-base font-bold text-stone-900">Messages</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">View your conversations and chat with buyers or sellers</p>
        </a>

        <!-- Notifications -->
        <a href="notification.php" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4 relative">
                <i data-feather="bell" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
                <?php if ($unread_notifs > 0): ?>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF7F11] text-white text-[9px] font-bold rounded-full flex items-center justify-center"><?php echo $unread_notifs; ?></span>
                <?php endif; ?>
            </div>
            <h3 class="text-base font-bold text-stone-900">Notifications</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">Stay up to date with activity on your listings</p>
        </a>

        <!-- My Properties -->
        <a href="category.php?cat=property-rentals" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4">
                <i data-feather="home" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">My Properties</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">Get a full overview of your properties</p>
        </a>

        <!-- For businesses -->
        <a href="for_businesses.php" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group block">
            <div class="mb-4">
                <i data-feather="briefcase" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">For businesses</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">View our business solutions</p>
        </a>

        <!-- Settings -->
        <button onclick="document.getElementById('editProfileModal').classList.remove('hidden')" class="bg-white border border-gray-200 rounded-2xl p-6 text-left hover:shadow-md hover:border-gray-300 transition group">
            <div class="mb-4">
                <i data-feather="settings" class="w-6 h-6 text-stone-700 group-hover:text-[#FF7F11] transition"></i>
            </div>
            <h3 class="text-base font-bold text-stone-900">Settings</h3>
            <p class="text-xs text-stone-500 mt-1 leading-relaxed">Settings for your account at Rondera</p>
        </button>

    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in">
        <!-- Modal header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-extrabold text-stone-900">Edit Profile</h2>
            <button onclick="document.getElementById('editProfileModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-stone-100 transition text-stone-500">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal body / form -->
        <form action="profile.php" method="POST" class="p-6 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="prenom" class="text-xs font-bold text-stone-600 uppercase tracking-wider">First Name</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userPrenom); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all text-sm" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="nom" class="text-xs font-bold text-stone-600 uppercase tracking-wider">Last Name</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userNom); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all text-sm" required>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-stone-400 uppercase tracking-wider">Email (cannot be changed)</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-stone-50 text-stone-400 text-sm cursor-not-allowed" disabled>
            </div>

            <div class="flex flex-col gap-1">
                <label for="telephone" class="text-xs font-bold text-stone-600 uppercase tracking-wider">Telephone</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0063fb] focus:border-transparent transition-all text-sm">
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('editProfileModal').classList.add('hidden')" class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-bold text-stone-600 hover:bg-stone-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#0063fb] hover:bg-[#004fc7] text-white text-sm font-bold transition shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
