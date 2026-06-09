<?php
// post_ad.php
$title = 'Post an Ad';
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}
?>

<div class=" -mx-4 md:-mx-10 px-4 md:px-10 -mt-6 pt-14 pb-14">
    <!-- Hero Section -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-bold text-stone-900 tracking-tight">Post an ad</h1>
            <p class="text-stone-500 mt-3 text-lg">Choose a category to get started — your ad will reach thousands of buyers.</p>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Property rentals -->
        <div class="bg-white rounded-xl p-7 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i data-feather="home" class="text-[#FF7F11] w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-stone-900">Property rentals</h3>
                    <p class="text-sm text-stone-400">Houses, apartments, rooms & short stays</p>
                </div>
            </div>
            <p class="text-stone-500 text-sm leading-relaxed">List your rental property and connect with tenants looking for short or long-term accommodation.</p>
            <a href="post_ad_form.php?cat=property-rentals" class="mt-auto inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-5 py-2.5 rounded-full transition w-fit shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>

        <!-- Property sales -->
        <div class="bg-white rounded-xl p-7 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i data-feather="home" class="text-[#FF7F11] w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-stone-900">Property sales</h3>
                    <p class="text-sm text-stone-400">Homes, villas, commercial buildings</p>
                </div>
            </div>
            <p class="text-stone-500 text-sm leading-relaxed">Advertise your property for sale and reach motivated buyers across Burundi and beyond.</p>
            <a href="post_ad_form.php?cat=property-sales" class="mt-auto inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-5 py-2.5 rounded-full transition w-fit shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>

        <!-- Lands & Plots -->
        <div class="bg-white rounded-xl p-7 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i data-feather="map" class="text-[#FF7F11] w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-stone-900">Lands &amp; Plots</h3>
                    <p class="text-sm text-stone-400">Serviced plots, agricultural & residential land</p>
                </div>
            </div>
            <p class="text-stone-500 text-sm leading-relaxed">Sell or lease your land and reach developers, investors, and individuals looking to build.</p>
            <a href="post_ad_form.php?cat=lands-plots" class="mt-auto inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-5 py-2.5 rounded-full transition w-fit shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>

        <!-- Vehicles for Sale -->
        <div class="bg-white rounded-xl p-7 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i data-feather="truck" class="text-[#FF7F11] w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-stone-900">Vehicles for Sale</h3>
                    <p class="text-sm text-stone-400">Cars, motorcycles, trucks & more</p>
                </div>
            </div>
            <p class="text-stone-500 text-sm leading-relaxed">List your vehicle for sale and connect with serious buyers looking for their next ride.</p>
            <a href="post_ad_form.php?cat=vehicles-sale" class="mt-auto inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-5 py-2.5 rounded-full transition w-fit shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>

        <!-- Vehicles for rent -->
        <div class="bg-white rounded-xl p-7 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i data-feather="key" class="text-[#FF7F11] w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-stone-900">Vehicles for rent</h3>
                    <p class="text-sm text-stone-400">Daily, weekly & long-term hire</p>
                </div>
            </div>
            <p class="text-stone-500 text-sm leading-relaxed">Offer your vehicle for hire and attract customers looking for flexible and affordable transport.</p>
            <a href="post_ad_form.php?cat=vehicles-rent" class="mt-auto inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white text-sm font-bold px-5 py-2.5 rounded-full transition w-fit shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Post an ad
            </a>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>