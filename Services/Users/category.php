<?php
$title = 'Browse Category';
ob_start();
session_start();

// All categories definition
$categories = [
    'property-rentals' => [
        'label' => 'Property Rentals',
        'icon'  => 'home',
        'desc'  => 'Houses, apartments, rooms & short stays for rent',
        'listings' => [
            ['id'=>1, 'title'=>'Unique & Charming Holiday Home by Borrevannet', 'location'=>'Nykirke', 'price'=>'3,000,000 kr', 'badge'=>'Recommended', 'img'=>'https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=600&auto=format&fit=crop&q=80'],
            ['id'=>4, 'title'=>'Charming 3-Story Detached House with Garden', 'location'=>'Oslo / Skjetten', 'price'=>'19,000,000 kr', 'badge'=>'Hot Offer', 'img'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&auto=format&fit=crop&q=80'],
            ['id'=>1, 'title'=>'Modern Studio Apartment — City Center', 'location'=>'Bujumbura', 'price'=>'450,000 kr', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&auto=format&fit=crop&q=80'],
            ['id'=>1, 'title'=>'Cozy 2-Bedroom Flat Near University', 'location'=>'Gitega', 'price'=>'280,000 kr', 'badge'=>'Popular', 'img'=>'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&auto=format&fit=crop&q=80'],
        ],
    ],
    'property-sales' => [
        'label' => 'Property Sales',
        'icon'  => 'home',
        'desc'  => 'Homes, villas & commercial buildings for sale',
        'listings' => [
            ['id'=>2, 'title'=>'Large and Beautiful Smallholding of approx. 11.6 Acres', 'location'=>'Spongdal', 'price'=>'10,490,000 kr', 'badge'=>'Featured', 'img'=>'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=600&auto=format&fit=crop&q=80'],
            ['id'=>4, 'title'=>'Charming 3-Story Detached House with Garden', 'location'=>'Oslo / Skjetten', 'price'=>'19,000,000 kr', 'badge'=>'Hot Offer', 'img'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&auto=format&fit=crop&q=80'],
            ['id'=>2, 'title'=>'Newly Built Villa with Pool & Garden', 'location'=>'Bujumbura', 'price'=>'55,000,000 kr', 'badge'=>'New', 'img'=>'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=600&auto=format&fit=crop&q=80'],
            ['id'=>2, 'title'=>'Downtown Commercial Space — 320 m²', 'location'=>'Gitega', 'price'=>'12,000,000 kr', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&auto=format&fit=crop&q=80'],
        ],
    ],
    'lands-plots' => [
        'label' => 'Lands & Plots',
        'icon'  => 'map',
        'desc'  => 'Serviced plots, agricultural & residential land',
        'listings' => [
            ['id'=>1, 'title'=>'Prime Residential Plot — 800 m²', 'location'=>'Bujumbura North', 'price'=>'3,500,000 kr', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=600&auto=format&fit=crop&q=80'],
            ['id'=>1, 'title'=>'Agricultural Land — 5 Acres Near River', 'location'=>'Rumonge', 'price'=>'1,800,000 kr', 'badge'=>'Popular', 'img'=>'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&auto=format&fit=crop&q=80'],
            ['id'=>1, 'title'=>'Commercial Plot on Main Road — 1,500 m²', 'location'=>'Ngozi', 'price'=>'8,200,000 kr', 'badge'=>'Featured', 'img'=>'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&auto=format&fit=crop&q=80'],
            ['id'=>1, 'title'=>'Lake View Hillside Plot — 600 m²', 'location'=>'Cibitoke', 'price'=>'2,100,000 kr', 'badge'=>'Hot Offer', 'img'=>'https://images.unsplash.com/photo-1448630360428-65456885c650?w=600&auto=format&fit=crop&q=80'],
        ],
    ],
    'vehicles-sale' => [
        'label' => 'Vehicles for Sale',
        'icon'  => 'truck',
        'desc'  => 'Cars, motorcycles, trucks & more for sale',
        'listings' => [
            ['id'=>3, 'title'=>'Toyota RAV4 Plug-In Hybrid — Low Mileage', 'location'=>'Oslo', 'price'=>'359,900 kr', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?w=600&auto=format&fit=crop&q=80'],
            ['id'=>7, 'title'=>'Sunlight T68 Motorhome — Perfect Family Camper', 'location'=>'Jessheim', 'price'=>'599,000 kr', 'badge'=>'Weekly Deal', 'img'=>'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=600&auto=format&fit=crop&q=80'],
            ['id'=>8, 'title'=>'Honda CB 350 F Vintage Motorcycle', 'location'=>'Kvelde', 'price'=>'47,000 kr', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&auto=format&fit=crop&q=80'],
            ['id'=>3, 'title'=>'Toyota Land Cruiser V8 — Full Option', 'location'=>'Bujumbura', 'price'=>'4,200,000 kr', 'badge'=>'Popular', 'img'=>'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=80'],
        ],
    ],
    'vehicles-rent' => [
        'label' => 'Vehicles for Rent',
        'icon'  => 'key',
        'desc'  => 'Daily, weekly & long-term vehicle hire',
        'listings' => [
            ['id'=>3, 'title'=>'Toyota Hiace — Daily/Weekly Rental', 'location'=>'Bujumbura', 'price'=>'85,000 kr/day', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&auto=format&fit=crop&q=80'],
            ['id'=>3, 'title'=>'Land Rover Defender — Adventure Rental', 'location'=>'Bujumbura', 'price'=>'120,000 kr/day', 'badge'=>'Popular', 'img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&auto=format&fit=crop&q=80'],
            ['id'=>3, 'title'=>'Honda Motorcycle — City Courier Rental', 'location'=>'Ngozi', 'price'=>'20,000 kr/day', 'badge'=>'Budget Pick', 'img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&auto=format&fit=crop&q=80'],
            ['id'=>3, 'title'=>'Minibus 18-Seater — Group & Event Hire', 'location'=>'Gitega', 'price'=>'200,000 kr/day', 'badge'=>'Featured', 'img'=>'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&auto=format&fit=crop&q=80'],
        ],
    ],
];

$current = isset($_GET['cat']) && isset($categories[$_GET['cat']]) ? $_GET['cat'] : 'property-rentals';
$cat = $categories[$current];
?>

<div class="py-4">

    <!-- Category Tabs (horizontal nav with active underline) -->
    <div class="border-b border-gray-200 mb-8 -mx-4 md:-mx-10 px-4 md:px-10 overflow-x-auto">
        <nav class="flex gap-0 min-w-max">
            <?php foreach($categories as $slug => $c): ?>
            <a href="category.php?cat=<?= $slug ?>"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold whitespace-nowrap border-b-2 transition-all
                    <?= $slug === $current
                        ? 'border-[#FF7F11] text-[#FF7F11]'
                        : 'border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300' ?>">
                <i data-feather="<?= $c['icon'] ?>" class="w-4 h-4"></i>
                <?= htmlspecialchars($c['label']) ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Category Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                    <i data-feather="<?= $cat['icon'] ?>" class="w-5 h-5 text-[#FF7F11]"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-stone-900 tracking-tight">
                    <?= htmlspecialchars($cat['label']) ?>
                </h1>
            </div>
            <p class="text-sm text-stone-500 ml-13"><?= htmlspecialchars($cat['desc']) ?> · <span class="font-semibold text-stone-700"><?= count($cat['listings']) ?> listings</span></p>
        </div>

        <!-- Sort / Filter bar -->
        <div class="flex items-center gap-2">
            <select class="border border-gray-200 rounded-full px-4 py-2 text-sm text-stone-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#FF7F11]/30">
                <option>Sort: Newest first</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Most Popular</option>
            </select>
            <button class="flex items-center gap-1.5 border border-gray-200 rounded-full px-4 py-2 text-sm text-stone-700 bg-white hover:border-[#FF7F11] transition">
                <i data-feather="sliders" class="w-4 h-4"></i> Filters
            </button>
        </div>
    </div>

    <!-- Listings Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach($cat['listings'] as $listing): ?>
        <a href="product.php?id=<?= $listing['id'] ?>"
            class="product-card bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm relative group block hover:shadow-md transition-shadow">
            <div class="relative h-48 bg-gray-100">
                <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    src="<?= $listing['img'] ?>" alt="<?= htmlspecialchars($listing['title']) ?>">
                <button onclick="event.preventDefault()" class="absolute top-3 right-3 bg-white/90 hover:bg-white text-stone-600 hover:text-red-500 backdrop-blur-sm p-2 rounded-full shadow-sm transition-all hover:scale-110 flex items-center justify-center z-10">
                    <i data-feather="heart" class="w-4 h-4"></i>
                </button>
                <?php if($listing['badge']): ?>
                <div class="absolute bottom-2 left-2 bg-white/90 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-800">
                    🏷️ <?= htmlspecialchars($listing['badge']) ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <p class="text-xs text-stone-500 uppercase tracking-wide flex items-center gap-1">
                    <i data-feather="map-pin" class="w-3 h-3"></i> <?= htmlspecialchars($listing['location']) ?>
                </p>
                <h4 class="font-bold text-stone-800 text-sm line-clamp-2 mt-1"><?= htmlspecialchars($listing['title']) ?></h4>
                <p class="text-xl font-bold text-stone-900 mt-2"><?= htmlspecialchars($listing['price']) ?></p>
                <div class="flex mt-3 gap-2">
                    <span class="text-[10px] bg-green-50 text-green-700 font-semibold px-2 py-0.5 rounded-full">✔ Ready</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Back to all categories -->
    <div class="mt-10 text-center">
        <a href="index.php" class="inline-flex items-center gap-2 text-sm text-stone-500 hover:text-stone-900 transition">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back to marketplace
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
