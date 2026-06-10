<?php
// for_businesses.php
$title = 'For Businesses';
ob_start();
session_start();

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

try {
    $stmt = $pdo->query("SELECT * FROM business_solutions ORDER BY id ASC");
    $solutions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $solutions = [];
}
?>

<div class="py-12 md:py-20 -mx-4 md:-mx-10 px-4 md:px-10">
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
        <?php foreach ($solutions as $sol): ?>
        <div class="bg-white rounded-lg p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex items-center gap-4 mb-4">
                <i data-feather="<?= htmlspecialchars($sol['icon'] ?: 'briefcase') ?>" class="text-[#FF7F11] w-7 h-7"></i>
                <h3 class="text-2xl font-bold text-stone-900"><?= htmlspecialchars($sol['title']) ?></h3>
            </div>
            <p class="text-stone-500 mb-6 flex-grow"><?= htmlspecialchars($sol['description']) ?></p>
            <div class="flex gap-4 items-center">
                <button onclick='showSolutionDetails(<?= json_encode($sol) ?>)' 
                    class="px-6 py-2 rounded-full bg-[#FF7F11] text-white text-sm font-bold hover:bg-[#e06c09] transition shadow-sm">
                    Read more
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Simple Modal -->
<div id="solution-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-lg bg-white rounded-2xl shadow-2xl p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center">
                <i id="modal-icon" data-feather="briefcase" class="text-[#FF7F11]"></i>
            </div>
            <h2 id="modal-title" class="text-2xl font-extrabold text-stone-900"></h2>
        </div>
        <p id="modal-details" class="text-stone-600 leading-relaxed mb-8"></p>
        <div class="flex justify-end">
            <button onclick="closeModal()" class="px-6 py-2.5 bg-stone-900 text-white rounded-full font-bold text-sm hover:bg-black transition">
                Got it
            </button>
        </div>
    </div>
</div>

<script>
function showSolutionDetails(sol) {
    const modal = document.getElementById('solution-modal');
    document.getElementById('modal-title').textContent = sol.title;
    document.getElementById('modal-details').textContent = sol.details || 'Contact us for more information about this solution.';
    const icon = document.getElementById('modal-icon');
    icon.setAttribute('data-feather', sol.icon || 'briefcase');
    
    modal.classList.remove('hidden');
    if (typeof feather !== 'undefined') feather.replace();
}

function closeModal() {
    document.getElementById('solution-modal').classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
