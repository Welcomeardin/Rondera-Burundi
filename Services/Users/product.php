<?php
$title = 'Product Details';
ob_start();
session_start();

// Static product data keyed by ID
$products = [
    1 => [
        'title' => 'Unique & Charming Holiday Home by Borrevannet',
        'price' => '3,000,000 kr',
        'total_price' => '3,076,090 kr',
        'location' => 'Nykirke',
        'category' => 'Property Rentals',
        'badge' => '🏷️ Recommended',
        'img' => 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=800&auto=format&fit=crop&q=80',
        'desc' => 'A stunning holiday home nestled by the shores of Borrevannet. Enjoy the peaceful surroundings, a private dock, and beautiful sunsets. Perfect for families or as a weekend getaway. The property includes a fully equipped kitchen, 3 bedrooms, and a large terrace overlooking the lake.',
        'specs' => ['Bedrooms' => '3', 'Bathrooms' => '2', 'Size' => '120 m²', 'Year Built' => '2005', 'Condition' => 'Excellent', 'Type' => 'Holiday Home'],
        'owner' => ['name' => 'Jean Pierre K.', 'since' => 'Member since 2021', 'ads' => '4 active ads', 'rating' => '4.8'],
    ],
    2 => [
        'title' => 'Large and Beautiful Smallholding of approx. 11.6 Acres',
        'price' => '10,490,000 kr',
        'total_price' => '10,753,340 kr',
        'location' => 'Spongdal',
        'category' => 'Property Sales',
        'badge' => '🏷️ Featured',
        'img' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&auto=format&fit=crop&q=80',
        'desc' => 'A rare opportunity to acquire a large, beautiful smallholding spanning 11.6 acres. Includes a main farmhouse, two outbuildings, fertile land, and a private stream. Ideal for farming, agro-tourism, or as a private retreat.',
        'specs' => ['Land Area' => '11.6 acres', 'Buildings' => '3', 'Water Supply' => 'Private stream', 'Year Built' => '1980', 'Condition' => 'Good', 'Type' => 'Smallholding'],
        'owner' => ['name' => 'Marie Noel', 'since' => 'Member since 2019', 'ads' => '7 active ads', 'rating' => '4.9'],
    ],
    3 => [
        'title' => 'Toyota RAV4 Plug-In Hybrid — Low Mileage',
        'price' => '359,900 kr',
        'total_price' => '359,900 kr',
        'location' => 'Oslo',
        'category' => 'Vehicles for Sale',
        'badge' => '',
        'img' => 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Well-maintained Toyota RAV4 Plug-In Hybrid. Very low mileage for its year with full service history. Comes with winter wheels, tow bar, panoramic roof, and heated front seats. Non-smoker vehicle, always garaged.',
        'specs' => ['Year' => '2021', 'Mileage' => '45,000 km', 'Fuel' => 'Plug-In Hybrid', 'Gearbox' => 'Automatic', 'Color' => 'Lunar Rock', 'Seats' => '5'],
        'owner' => ['name' => 'Alexis B.', 'since' => 'Member since 2020', 'ads' => '2 active ads', 'rating' => '4.7'],
    ],
    4 => [
        'title' => 'Charming 3-Story Detached House with Garden',
        'price' => '19,000,000 kr',
        'total_price' => '19,000,000 kr',
        'location' => 'Oslo / Skjetten',
        'category' => 'Property Sales',
        'badge' => '🏷️ Hot Offer',
        'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Beautiful 3-story detached house in a quiet residential area. Features a large garden, a double garage, and a modern open-plan kitchen. Walking distance to schools and public transport.',
        'specs' => ['Bedrooms' => '5', 'Bathrooms' => '3', 'Size' => '210 m²', 'Year Built' => '2002', 'Garage' => 'Double', 'Type' => 'Detached House'],
        'owner' => ['name' => 'Claudette M.', 'since' => 'Member since 2018', 'ads' => '1 active ad', 'rating' => '5.0'],
    ],
    5 => [
        'title' => 'Cinderella Ecocat Incineration Toilet — Like New',
        'price' => '5,500 kr',
        'total_price' => '5,500 kr',
        'location' => 'Tranby',
        'category' => 'Marketplace',
        'badge' => '',
        'img' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Barely used Cinderella Ecocat incineration toilet. Fully cleaned, serviced, and ready for use. Perfect for cabins, boats, or any off-grid setup. Original box and manual included.',
        'specs' => ['Brand' => 'Cinderella', 'Model' => 'Ecocat', 'Condition' => 'Like New', 'Fuel' => 'Electric', 'Capacity' => '3–4 persons'],
        'owner' => ['name' => 'Bernard T.', 'since' => 'Member since 2022', 'ads' => '3 active ads', 'rating' => '4.6'],
    ],
    6 => [
        'title' => 'MacBook Air M2 — Space Gray, 256GB SSD',
        'price' => '5,990 kr',
        'total_price' => '5,990 kr',
        'location' => 'Bergen',
        'category' => 'Electronics',
        'badge' => '🏷️ Popular',
        'img' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Selling my MacBook Air M2 in Space Gray. In excellent condition with a battery health of 98%. Comes with original charger and box. No scratches or dents. Reason for selling: upgraded to a Pro.',
        'specs' => ['Chip' => 'Apple M2', 'RAM' => '8GB', 'Storage' => '256GB SSD', 'Screen' => '13.6"', 'Battery' => '98%', 'OS' => 'macOS Sonoma'],
        'owner' => ['name' => 'Sophie L.', 'since' => 'Member since 2023', 'ads' => '5 active ads', 'rating' => '4.9'],
    ],
    7 => [
        'title' => 'Sunlight T68 Motorhome — Perfect Family Camper',
        'price' => '599,000 kr',
        'total_price' => '599,000 kr',
        'location' => 'Jessheim',
        'category' => 'Vehicles for Sale',
        'badge' => '🏷️ Weekly Deal',
        'img' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Sunlight T68 fully-equipped motorhome in great condition. Includes solar panels, awning, GPS, reverse camera, and a full kitchen. Perfect for long family road trips across Europe. Serviced annually.',
        'specs' => ['Year' => '2019', 'Mileage' => '68,000 km', 'Berths' => '4', 'Length' => '6.9m', 'Solar' => 'Yes', 'Fuel' => 'Diesel'],
        'owner' => ['name' => 'Patrick H.', 'since' => 'Member since 2017', 'ads' => '2 active ads', 'rating' => '4.8'],
    ],
    8 => [
        'title' => 'Honda CB 350 F Vintage Motorcycle',
        'price' => '47,000 kr',
        'total_price' => '47,000 kr',
        'location' => 'Kvelde',
        'category' => 'Vehicles for Sale',
        'badge' => '',
        'img' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&auto=format&fit=crop&q=80',
        'desc' => 'Classic Honda CB 350 F in excellent condition for its age. Recently restored with new carburetors, chain, and a fresh paint job. A true collector\'s piece that still runs beautifully.',
        'specs' => ['Year' => '1973', 'Engine' => '350cc', 'Color' => 'Candy Red', 'Condition' => 'Excellent', 'Km' => '28,000 km', 'Type' => 'Classic'],
        'owner' => ['name' => 'Erik V.', 'since' => 'Member since 2016', 'ads' => '6 active ads', 'rating' => '4.7'],
    ],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$p = $products[$id] ?? $products[1];
?>

<div class="py-6 max-w-5xl mx-auto">

    <!-- Back link -->
    <a href="index.php" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-stone-900 mb-6 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to listings
    </a>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- LEFT: Images + Details -->
        <div class="flex-1 min-w-0">
            <!-- Main Image -->
            <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-4">
                <img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="w-full h-72 md:h-96 object-cover">
            </div>

            <!-- Thumbnail strip (same image repeated for demo) -->
            <div class="flex gap-2 mb-6">
                <?php for($i=0;$i<4;$i++): ?>
                <div class="h-16 w-24 rounded-lg overflow-hidden border <?= $i===0?'border-[#FF7F11] ring-2 ring-[#FF7F11]/30':'border-gray-200' ?> flex-shrink-0 cursor-pointer">
                    <img src="<?= $p['img'] ?>" class="w-full h-full object-cover" alt="">
                </div>
                <?php endfor; ?>
            </div>

            <!-- Title & Badge -->
            <?php if($p['badge']): ?>
            <span class="inline-block bg-orange-50 text-[#FF7F11] text-xs font-bold px-3 py-1 rounded-full mb-3"><?= $p['badge'] ?></span>
            <?php endif; ?>
            <h1 class="text-2xl md:text-3xl font-extrabold text-stone-900 mb-1 tracking-tight"><?= htmlspecialchars($p['title']) ?></h1>
            <p class="text-sm text-stone-400 mb-4 flex items-center gap-1">
                <i data-feather="map-pin" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($p['location']) ?>
                &nbsp;·&nbsp; <span class="text-stone-400"><?= htmlspecialchars($p['category']) ?></span>
            </p>

            <!-- Description -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-3 text-base">About this listing</h2>
                <p class="text-stone-600 text-sm leading-relaxed"><?= htmlspecialchars($p['desc']) ?></p>
            </div>

            <!-- Specifications -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-4 text-base">Specifications</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach($p['specs'] as $key => $val): ?>
                    <div class="bg-stone-50 rounded-xl p-3">
                        <p class="text-xs text-stone-400 mb-0.5"><?= $key ?></p>
                        <p class="text-sm font-bold text-stone-800"><?= $val ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: Price + Owner + Actions -->
        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4">

            <!-- Price Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <p class="text-3xl font-extrabold text-stone-900"><?= $p['price'] ?></p>
                <?php if($p['price'] !== $p['total_price']): ?>
                <p class="text-xs text-stone-400 mt-0.5">Total incl. fees: <?= $p['total_price'] ?></p>
                <?php endif; ?>

                <div class="mt-4 flex flex-col gap-2.5">
                    <a href="../Authantification/login.php"
                        class="w-full flex items-center justify-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i> Make an Order
                    </a>
                    <a href="chat.php"
                        class="w-full flex items-center justify-center gap-2 bg-black hover:bg-stone-800 text-white font-bold py-3 rounded-full transition shadow-sm text-sm">
                        <i data-feather="message-circle" class="w-4 h-4"></i> Send a Message
                    </a>
                    <button class="w-full flex items-center justify-center gap-2 border-2 border-gray-200 hover:border-red-300 text-stone-600 hover:text-red-500 font-bold py-2.5 rounded-full transition text-sm">
                        <i data-feather="heart" class="w-4 h-4"></i> Save to Favorites
                    </button>
                </div>

                <div class="border-t border-gray-100 mt-5 pt-4 flex items-center gap-1.5 text-xs text-stone-400">
                    <i data-feather="shield" class="w-3.5 h-3.5 text-green-500"></i>
                    <span>Secure transactions on Rondera</span>
                </div>
            </div>

            <!-- Owner Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-4 text-sm">Seller Information</h2>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#FF7F11] flex items-center justify-center text-white font-extrabold text-lg flex-shrink-0">
                        <?= strtoupper(substr($p['owner']['name'], 0, 2)) ?>
                    </div>
                    <div>
                        <p class="font-bold text-stone-900 text-sm"><?= htmlspecialchars($p['owner']['name']) ?></p>
                        <p class="text-xs text-stone-400"><?= $p['owner']['since'] ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center">
                    <div class="bg-stone-50 rounded-xl p-2.5">
                        <p class="text-lg font-extrabold text-[#FF7F11]"><?= $p['owner']['rating'] ?></p>
                        <p class="text-[10px] text-stone-400">Rating</p>
                    </div>
                    <div class="bg-stone-50 rounded-xl p-2.5">
                        <p class="text-lg font-extrabold text-stone-900"><?= explode(' ', $p['owner']['ads'])[0] ?></p>
                        <p class="text-[10px] text-stone-400">Active Ads</p>
                    </div>
                </div>
                <a href="chat.php" class="mt-4 w-full flex items-center justify-center gap-2 text-sm font-bold text-[#FF7F11] hover:underline">
                    <i data-feather="message-square" class="w-4 h-4"></i> Contact Seller
                </a>
            </div>

            <!-- Location Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-3 text-sm flex items-center gap-2">
                    <i data-feather="map-pin" class="w-4 h-4 text-[#FF7F11]"></i> Location
                </h2>
                <p class="text-sm text-stone-700 font-semibold"><?= htmlspecialchars($p['location']) ?></p>
                <div class="mt-3 h-28 bg-stone-100 rounded-xl flex items-center justify-center text-stone-400 text-xs">
                    <i data-feather="map" class="w-5 h-5 mr-1"></i> Map preview
                </div>
            </div>

            <!-- Safety Tips -->
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 text-xs text-stone-600 leading-relaxed">
                <p class="font-bold text-stone-800 mb-1 flex items-center gap-1.5">
                    <i data-feather="alert-circle" class="w-3.5 h-3.5 text-[#FF7F11]"></i> Safety Tips
                </p>
                Never pay in advance before seeing the item. Meet in a public place when possible. Report suspicious listings.
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
