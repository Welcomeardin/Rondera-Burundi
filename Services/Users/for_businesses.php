<?php
// for_businesses.php
$title = 'For Businesses';
ob_start();
session_start();
?>

<div class="py-12 md:py-20 bg-stone-50/50 -mx-4 md:-mx-10 px-4 md:px-10">
    <!-- Hero Section -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-16 gap-12">
        <div class="md:w-1/2">
            <h1 class="text-4xl md:text-5xl font-extrabold text-stone-900 mb-6 tracking-tight">Explore our business solutions</h1>
            <p class="text-lg text-stone-600 mb-8 max-w-lg leading-relaxed">
                See our business tools. Here you can find information and inspiration on how you as a business customer can create growth in your company.
            </p>
            <img src="../../uploads/logos/Ro-Bu.png" alt="Rondera" class="h-10 opacity-90" />
        </div>
        <div class="md:w-1/2 relative h-[300px] md:h-[400px]">
            <div class="grid grid-cols-2 gap-4 h-full">
                <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=400" class="rounded-lg object-cover w-full h-full shadow-sm" alt="Business 1">
                <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&q=80&w=400" class="rounded-lg object-cover w-full h-full shadow-sm" alt="Business 2">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=400" class="rounded-lg object-cover w-full h-full shadow-sm" alt="Business 3">
                <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&q=80&w=400" class="rounded-lg object-cover w-full h-full shadow-sm" alt="Business 4">
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Property rentals -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="home" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900">Property rentals</h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow">For real estate agents and property managers</p>
            <div class="flex gap-4 items-center">
                <a href="../Authantification/login.php" class="px-5 py-2 rounded-full border border-gray-300 text-sm font-semibold text-[#0063fb] hover:bg-gray-50 transition">Log in</a>
                <a href="#" class="text-sm font-semibold text-[#0063fb] hover:underline">Read more</a>
            </div>
        </div>

        <!-- Property sales -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="home" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900">Property sales</h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow">For real estate agents and brokerages</p>
            <div class="flex gap-4 items-center mt-auto">
                <a href="../Authantification/login.php" class="px-5 py-2 rounded-full border border-gray-300 text-sm font-semibold text-[#0063fb] hover:bg-gray-50 transition">Log in</a>
                <a href="#" class="text-sm font-semibold text-[#0063fb] hover:underline">Read more</a>
            </div>
        </div>

        <!-- Lands & Plots -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="map" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900">Lands &amp; Plots</h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow">For land developers and agents</p>
            <div class="flex gap-4 items-center mt-auto">
                <a href="../Authantification/login.php" class="px-5 py-2 rounded-full border border-gray-300 text-sm font-semibold text-[#0063fb] hover:bg-gray-50 transition">Log in</a>
                <a href="#" class="text-sm font-semibold text-[#0063fb] hover:underline">Read more</a>
            </div>
        </div>

        <!-- Vehicles for Sale -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="truck" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900">Vehicles for Sale</h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow">For car dealers and professional sellers</p>
            <div class="flex gap-4 items-center mt-auto">
                <a href="../Authantification/login.php" class="px-5 py-2 rounded-full border border-gray-300 text-sm font-semibold text-[#0063fb] hover:bg-gray-50 transition">Log in</a>
                <a href="#" class="text-sm font-semibold text-[#0063fb] hover:underline">Read more</a>
            </div>
        </div>

        <!-- Vehicles for rent -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="key" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900">Vehicles for rent</h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow">For car rental agencies and fleet managers</p>
            <div class="flex gap-4 items-center mt-auto">
                <a href="../Authantification/login.php" class="px-5 py-2 rounded-full border border-gray-300 text-sm font-semibold text-[#0063fb] hover:bg-gray-50 transition">Log in</a>
                <a href="#" class="text-sm font-semibold text-[#0063fb] hover:underline">Read more</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>