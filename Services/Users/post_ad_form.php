<?php
$title = 'Post an Ad';
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category = $_GET['cat'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['desc'] ?? '');
    
    // Validate required fields
    if (empty($title) || empty($location) || empty($description)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // Prepare data based on category
            $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
            $price_day = isset($_POST['price_day']) ? (float)$_POST['price_day'] : null;
            
            // Use price_day for rentals, price for sales
            $final_price = $price ?: $price_day;
            
            // Insert into ads table
            $stmt = $pdo->prepare('INSERT INTO ads (user_id, category, title, description, price, location, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $category, $title, $description, $final_price, $location, 'pending']);
            
            $ad_id = $pdo->lastInsertId();
            
            // Handle photo uploads
            if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                // Use system temp directory for uploads
                $upload_dir = sys_get_temp_dir() . '/rondera_ads/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Check if directory is writable
                if (!is_writable($upload_dir)) {
                    $error = 'Upload directory is not writable: ' . $upload_dir;
                }
                
                if (empty($error)) {
                    $files = $_FILES['photos'];
                    $file_count = count($files['name']);
                    
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $files['tmp_name'][$i];
                            $file_name = $files['name'][$i];
                            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                            
                            // Validate file type
                            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                // Generate unique filename
                                $new_filename = 'ad_' . $ad_id . '_' . uniqid() . '.' . $file_ext;
                                $upload_path = $upload_dir . $new_filename;
                                
                                if (move_uploaded_file($tmp_name, $upload_path)) {
                                    // Save to database with full path
                                    $photo_path = $upload_path;
                                    $is_primary = ($i === 0); // First photo is primary
                                    
                                    $stmt = $pdo->prepare('INSERT INTO ad_photos (ad_id, photo_path, is_primary) VALUES (?, ?, ?)');
                                    $stmt->execute([$ad_id, $photo_path, $is_primary]);
                                }
                            }
                        }
                    }
                }
            }
            
            $success = 'Your ad has been successfully posted!';
            
        } catch (\PDOException $e) {
            $error = 'Failed to post ad: ' . $e->getMessage();
        }
    }
}

$forms = [
    'property-rentals' => [
        'label' => 'Property Rentals',
        'icon'  => 'home',
        'sub'   => 'Houses, apartments, rooms & short stays',
        'fields' => [
            ['name'=>'title',      'label'=>'Listing Title',       'type'=>'text',     'placeholder'=>'e.g. Spacious 3-bedroom apartment near city center'],
            ['name'=>'location',   'label'=>'Location / City',     'type'=>'text',     'placeholder'=>'e.g. Bujumbura, Rohero'],
            ['name'=>'price',      'label'=>'Monthly Rent (BIF)',  'type'=>'number',   'placeholder'=>'e.g. 250000'],
            ['name'=>'size',       'label'=>'Size (m²)',           'type'=>'number',   'placeholder'=>'e.g. 85'],
            ['name'=>'bedrooms',   'label'=>'Bedrooms',            'type'=>'select',   'options'=>['1','2','3','4','5','6+']],
            ['name'=>'bathrooms',  'label'=>'Bathrooms',           'type'=>'select',   'options'=>['1','2','3','4+']],
            ['name'=>'furnished',  'label'=>'Furnished?',          'type'=>'select',   'options'=>['Furnished','Semi-Furnished','Unfurnished']],
            ['name'=>'available',  'label'=>'Available From',      'type'=>'date',     'placeholder'=>''],
            ['name'=>'desc',       'label'=>'Description',         'type'=>'textarea', 'placeholder'=>'Describe the property, amenities, neighborhood...'],
            ['name'=>'photos',     'label'=>'Photos',              'type'=>'file',     'placeholder'=>''],
        ],
    ],
    'property-sales' => [
        'label' => 'Property Sales',
        'icon'  => 'home',
        'sub'   => 'Homes, villas & commercial buildings for sale',
        'fields' => [
            ['name'=>'title',      'label'=>'Listing Title',       'type'=>'text',     'placeholder'=>'e.g. Beautiful 4-bedroom villa with pool'],
            ['name'=>'location',   'label'=>'Location / City',     'type'=>'text',     'placeholder'=>'e.g. Bujumbura, Kiriri'],
            ['name'=>'price',      'label'=>'Asking Price (BIF)',  'type'=>'number',   'placeholder'=>'e.g. 80000000'],
            ['name'=>'size',       'label'=>'Size (m²)',           'type'=>'number',   'placeholder'=>'e.g. 200'],
            ['name'=>'bedrooms',   'label'=>'Bedrooms',            'type'=>'select',   'options'=>['1','2','3','4','5','6+']],
            ['name'=>'bathrooms',  'label'=>'Bathrooms',           'type'=>'select',   'options'=>['1','2','3','4+']],
            ['name'=>'year_built', 'label'=>'Year Built',         'type'=>'number',   'placeholder'=>'e.g. 2015'],
            ['name'=>'type',       'label'=>'Property Type',      'type'=>'select',   'options'=>['House','Apartment','Villa','Commercial','Other']],
            ['name'=>'desc',       'label'=>'Description',         'type'=>'textarea', 'placeholder'=>'Describe the property, features, and location...'],
            ['name'=>'photos',     'label'=>'Photos',              'type'=>'file',     'placeholder'=>''],
        ],
    ],
    'lands-plots' => [
        'label' => 'Lands & Plots',
        'icon'  => 'map',
        'sub'   => 'Serviced plots, agricultural & residential land',
        'fields' => [
            ['name'=>'title',      'label'=>'Listing Title',       'type'=>'text',     'placeholder'=>'e.g. Prime residential plot near main road'],
            ['name'=>'location',   'label'=>'Location / District', 'type'=>'text',     'placeholder'=>'e.g. Bujumbura North, Cibitoke'],
            ['name'=>'price',      'label'=>'Asking Price (BIF)',  'type'=>'number',   'placeholder'=>'e.g. 15000000'],
            ['name'=>'area',       'label'=>'Area (m²)',           'type'=>'number',   'placeholder'=>'e.g. 800'],
            ['name'=>'land_type',  'label'=>'Land Type',          'type'=>'select',   'options'=>['Residential','Commercial','Agricultural','Industrial','Mixed Use']],
            ['name'=>'title_deed', 'label'=>'Title Deed?',        'type'=>'select',   'options'=>['Yes — Full Title','Yes — Certificate of Occupancy','No — In Progress']],
            ['name'=>'desc',       'label'=>'Description',         'type'=>'textarea', 'placeholder'=>'Describe the plot, access roads, utilities available...'],
            ['name'=>'photos',     'label'=>'Photos',              'type'=>'file',     'placeholder'=>''],
        ],
    ],
    'vehicles-sale' => [
        'label' => 'Vehicles for Sale',
        'icon'  => 'truck',
        'sub'   => 'Cars, motorcycles, trucks & more',
        'fields' => [
            ['name'=>'title',      'label'=>'Listing Title',       'type'=>'text',     'placeholder'=>'e.g. Toyota Land Cruiser V8 — Full Option'],
            ['name'=>'location',   'label'=>'Location / City',     'type'=>'text',     'placeholder'=>'e.g. Bujumbura'],
            ['name'=>'price',      'label'=>'Asking Price (BIF)',  'type'=>'number',   'placeholder'=>'e.g. 45000000'],
            ['name'=>'make',       'label'=>'Make / Brand',        'type'=>'text',     'placeholder'=>'e.g. Toyota, Honda, BMW'],
            ['name'=>'model',      'label'=>'Model',               'type'=>'text',     'placeholder'=>'e.g. Land Cruiser, Civic, X5'],
            ['name'=>'year',       'label'=>'Year',                'type'=>'number',   'placeholder'=>'e.g. 2019'],
            ['name'=>'mileage',    'label'=>'Mileage (km)',        'type'=>'number',   'placeholder'=>'e.g. 85000'],
            ['name'=>'fuel',       'label'=>'Fuel Type',           'type'=>'select',   'options'=>['Petrol','Diesel','Hybrid','Electric','LPG']],
            ['name'=>'gearbox',    'label'=>'Gearbox',             'type'=>'select',   'options'=>['Automatic','Manual']],
            ['name'=>'color',      'label'=>'Color',               'type'=>'text',     'placeholder'=>'e.g. Black, White, Silver'],
            ['name'=>'condition',  'label'=>'Condition',           'type'=>'select',   'options'=>['Brand New','Excellent','Good','Fair']],
            ['name'=>'desc',       'label'=>'Description',         'type'=>'textarea', 'placeholder'=>'Describe the vehicle, history, accessories included...'],
            ['name'=>'photos',     'label'=>'Photos',              'type'=>'file',     'placeholder'=>''],
        ],
    ],
    'vehicles-rent' => [
        'label' => 'Vehicles for Rent',
        'icon'  => 'key',
        'sub'   => 'Daily, weekly & long-term vehicle hire',
        'fields' => [
            ['name'=>'title',      'label'=>'Listing Title',       'type'=>'text',     'placeholder'=>'e.g. Toyota Hiace — Daily/Weekly Rental'],
            ['name'=>'location',   'label'=>'Pickup Location',     'type'=>'text',     'placeholder'=>'e.g. Bujumbura City Center'],
            ['name'=>'price_day',  'label'=>'Price per Day (BIF)', 'type'=>'number',   'placeholder'=>'e.g. 85000'],
            ['name'=>'price_week', 'label'=>'Price per Week (BIF)','type'=>'number',   'placeholder'=>'e.g. 500000'],
            ['name'=>'make',       'label'=>'Make / Brand',        'type'=>'text',     'placeholder'=>'e.g. Toyota, Land Rover'],
            ['name'=>'model',      'label'=>'Model',               'type'=>'text',     'placeholder'=>'e.g. Hiace, Defender'],
            ['name'=>'seats',      'label'=>'Number of Seats',     'type'=>'select',   'options'=>['2','4','5','7','9','12','18','25+']],
            ['name'=>'driver',     'label'=>'Driver Included?',    'type'=>'select',   'options'=>['Yes — Driver Included','No — Self Drive','Both Options Available']],
            ['name'=>'fuel_inc',   'label'=>'Fuel Included?',      'type'=>'select',   'options'=>['Yes','No']],
            ['name'=>'desc',       'label'=>'Description',         'type'=>'textarea', 'placeholder'=>'Describe the vehicle, rental conditions, deposit required...'],
            ['name'=>'photos',     'label'=>'Photos',              'type'=>'file',     'placeholder'=>''],
        ],
    ],
];

$slug = isset($_GET['cat']) && isset($forms[$_GET['cat']]) ? $_GET['cat'] : null;
$form = $slug ? $forms[$slug] : null;
?>

<style>
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 3px;
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

<div class="py-4">

    <?php if (!$form): ?>
    <!-- STEP 1: Category Selection -->
    <div class="mb-8 px-4 md:px-0">
        <a href="index.php" class="inline-flex items-center gap-1.5 text-sm text-stone-400 hover:text-stone-800 mb-5 transition">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back to marketplace
        </a>
        <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Post an Ad</h1>
        <p class="text-stone-500 mt-2">Select a category to get started.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 px-4 md:px-0">
        <?php foreach($forms as $s => $f): ?>
        <a href="post_ad_form.php?cat=<?= $s ?>"
            class="flex items-center gap-4 bg-white border border-gray-200 rounded p-5 hover:border-[#FF7F11] hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-full bg-orange-50 group-hover:bg-[#FF7F11] flex items-center justify-center flex-shrink-0 transition">
                <i data-feather="<?= $f['icon'] ?>" class="w-5 h-5 text-[#FF7F11] group-hover:text-white transition"></i>
            </div>
            <div>
                <h3 class="font-bold text-stone-900"><?= htmlspecialchars($f['label']) ?></h3>
                <p class="text-xs text-stone-400"><?= htmlspecialchars($f['sub']) ?></p>
            </div>
            <i data-feather="chevron-right" class="w-4 h-4 text-stone-300 group-hover:text-[#FF7F11] ml-auto transition"></i>
        </a>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- STEP 2: Category-Specific Form -->

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-stone-400 mb-6 px-4 md:px-0">
        <a href="post_ad.php" class="hover:text-stone-800 transition">Post an Ad</a>
        <i data-feather="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-stone-900 font-semibold"><?= htmlspecialchars($form['label']) ?></span>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mx-4 md:mx-0 mb-5 p-4 rounded bg-red-50 text-red-700 text-sm font-semibold border border-red-200">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mx-4 md:mx-0 mb-5 p-4 rounded bg-green-50 text-green-700 text-sm font-semibold border border-red-200">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Form Header -->
    <div class="flex items-center gap-4 mb-8 px-4 md:px-0">
        <div class="w-12 h-12 rounded-full bg-[#FF7F11] flex items-center justify-center flex-shrink-0">
            <i data-feather="<?= $form['icon'] ?>" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight"><?= htmlspecialchars($form['label']) ?></h1>
            <p class="text-sm text-stone-400"><?= htmlspecialchars($form['sub']) ?></p>
        </div>
    </div>

    <!-- Category switcher tabs -->
    <div class="border-b border-gray-200 mb-8 -mx-4 md:-mx-10 px-4 md:px-10 overflow-x-auto">
        <nav class="flex gap-0 min-w-max">
            <?php foreach($forms as $s => $f): ?>
            <a href="post_ad_form.php?cat=<?= $s ?>"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold whitespace-nowrap border-b-2 transition-all
                    <?= $s === $slug
                        ? 'border-[#FF7F11] text-[#FF7F11]'
                        : 'border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300' ?>">
                <i data-feather="<?= $f['icon'] ?>" class="w-4 h-4"></i>
                <?= htmlspecialchars($f['label']) ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="mx-4 md:mx-0 bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="bg-stone-50 border-b border-gray-100 px-7 py-4">
            <p class="text-sm font-semibold text-stone-700">Fill in your listing details below</p>
            <p class="text-xs text-stone-400 mt-0.5">All fields are required unless marked optional.</p>
        </div>

        <form action="post_ad_form.php?cat=<?= $slug ?>" method="POST" enctype="multipart/form-data" class="p-7">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($form['fields'] as $field):
                    $isWide = in_array($field['type'], ['textarea','file','text']) && $field['name'] === 'title';
                    $isFull = in_array($field['type'], ['textarea','file']);
                    $colClass = $isFull ? 'md:col-span-2' : '';
                ?>
                <div class="flex flex-col gap-1.5 <?= $colClass ?>">
                    <label for="<?= $field['name'] ?>" class="text-sm font-bold text-stone-800">
                        <?= htmlspecialchars($field['label']) ?>
                    </label>

                    <?php if($field['type'] === 'select'): ?>
                    <select name="<?= $field['name'] ?>" id="<?= $field['name'] ?>" class="form-input">
                        <option value="">— Select —</option>
                        <?php foreach($field['options'] as $opt): ?>
                        <option value="<?= $opt ?>"><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>

                    <?php elseif($field['type'] === 'textarea'): ?>
                    <textarea name="<?= $field['name'] ?>" id="<?= $field['name'] ?>"
                        class="form-input" placeholder="<?= htmlspecialchars($field['placeholder']) ?>"></textarea>

                    <?php elseif($field['type'] === 'file'): ?>
                    <label class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-gray-200 rounded p-6 cursor-pointer hover:border-[#FF7F11] transition bg-stone-50">
                        <i data-feather="upload-cloud" class="w-8 h-8 text-stone-300"></i>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-stone-600">Click to upload photos</p>
                            <p class="text-xs text-stone-400">PNG, JPG up to 10MB each — max 10 photos</p>
                        </div>
                        <input type="file" name="photos[]" id="<?= $field['name'] ?>"
                            class="hidden" accept="image/*" multiple>
                    </label>

                    <?php else: ?>
                    <input type="<?= $field['type'] ?>" name="<?= $field['name'] ?>" id="<?= $field['name'] ?>"
                        class="form-input" placeholder="<?= htmlspecialchars($field['placeholder']) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Terms & Submit -->
            <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" class="mt-0.5 accent-[#FF7F11]" required>
                    <span class="text-xs text-stone-500">
                        I confirm this listing is accurate and I agree to Rondera's
                        <a href="#" class="underline hover:text-[#FF7F11]">Terms of Use</a>.
                    </span>
                </label>
                <div class="flex gap-3 flex-shrink-0 w-full md:w-auto">
                    <a href="post_ad.php" class="flex-1 md:flex-none text-center px-6 py-2.5 rounded border border-gray-200 text-sm font-bold text-stone-600 hover:bg-stone-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 md:flex-none justify-center px-8 py-2.5 rounded bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold transition shadow-sm flex items-center gap-2">
                        <i data-feather="send" class="w-4 h-4"></i> Publish Ad
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
