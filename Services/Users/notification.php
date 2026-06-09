<?php
// notification.php
$title = 'Notifications';
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}
?>

<div class="py-8">
    <!-- Page Header -->
    <h1 class="text-3xl md:text-4xl font-extrabold text-stone-900 mb-6 tracking-tight">Notifications</h1>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Left Main Content -->
        <div class="flex-1 min-w-0">

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-8">
                <nav class="flex gap-0 -mb-px" id="notif-tabs">
                    <button onclick="switchTab('check')" id="tab-check"
                        class="tab-btn px-6 py-3 text-sm font-bold border-b-2 border-[#FF7F11] text-[#FF7F11] transition-all">
                        Check this
                    </button>
                    <button onclick="switchTab('saved')" id="tab-saved"
                        class="tab-btn px-6 py-3 text-sm font-bold border-b-2 border-transparent text-stone-500 hover:text-stone-800 transition-all">
                        Saved searches
                    </button>
                </nav>
            </div>

            <!-- Tab: Check this (default active) -->
            <div id="panel-check">
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-16 text-center max-w-md mx-auto">
                    <!-- SVG Illustration -->
                    <div class="relative mb-8 w-56 h-44">
                        <!-- Monitor -->
                        <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
                            <div class="w-36 h-24 bg-[#1e1e1e] rounded-lg flex items-center justify-center shadow-lg">
                                <div class="w-28 h-16 bg-white rounded flex flex-col items-center justify-center p-2 gap-1">
                                    <div class="w-8 h-8 bg-[#FF7F11] rounded flex items-center justify-center">
                                        <i data-feather="tag" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div class="w-full h-1.5 bg-gray-200 rounded"></div>
                                    <div class="w-3/4 h-1.5 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                            <!-- Monitor Stand -->
                            <div class="w-4 h-3 bg-[#1e1e1e] mx-auto"></div>
                            <div class="w-12 h-1.5 bg-stone-300 rounded mx-auto"></div>
                        </div>
                        <!-- Phone -->
                        <div class="absolute bottom-16 left-2 -rotate-12">
                            <div class="w-14 h-20 bg-[#1e1e1e] rounded-xl flex items-center justify-center shadow-md">
                                <div class="w-10 h-14 bg-white rounded-lg flex flex-col items-center justify-center p-1 gap-1">
                                    <div class="w-5 h-5 bg-[#FF7F11]/20 rounded flex items-center justify-center">
                                        <i data-feather="heart" class="w-3 h-3 text-[#FF7F11]"></i>
                                    </div>
                                    <div class="w-full h-1 bg-gray-200 rounded"></div>
                                    <div class="w-2/3 h-1 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Chat Bubble -->
                        <div class="absolute top-0 right-2">
                            <div class="w-14 h-14 bg-[#FF7F11] rounded-full flex items-center justify-center shadow-lg">
                                <i data-feather="bell" class="w-7 h-7 text-white"></i>
                            </div>
                            <div class="w-4 h-4 bg-[#FF7F11] rotate-45 -mt-2 ml-5 rounded-sm"></div>
                        </div>
                        <!-- Keyboard -->
                        <div class="absolute bottom-0 left-4 w-28 h-5 bg-stone-300 rounded-sm grid grid-cols-6 gap-0.5 p-1">
                            <?php for($i=0;$i<12;$i++): ?>
                            <div class="bg-white rounded-sm"></div>
                            <?php endfor; ?>
                        </div>
                        <!-- Mouse -->
                        <div class="absolute bottom-1 right-4">
                            <div class="w-8 h-12 bg-stone-300 rounded-full flex flex-col items-center pt-1 gap-0.5">
                                <div class="w-0.5 h-3 bg-stone-400 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <p class="text-base font-extrabold text-stone-900 mb-2">Stay up to date with new things happening!</p>
                    <p class="text-sm text-stone-500 leading-relaxed">
                        Here you will get notifications about your ads, favorites, and news from Rondera.
                    </p>

                    <a href="../Authantification/login.php"
                        class="mt-8 inline-flex items-center gap-2 bg-[#FF7F11] hover:bg-[#e06c09] text-white font-bold px-7 py-3 rounded-full transition shadow-sm text-sm">
                        <i data-feather="log-in" class="w-4 h-4"></i>
                        Log in to see notifications
                    </a>
                </div>
            </div>

            <!-- Tab: Saved searches -->
            <div id="panel-saved" class="hidden">
                <div class="flex flex-col items-center justify-center py-16 text-center max-w-md mx-auto">
                    <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mb-6 shadow-md">
                        <i data-feather="search" class="w-7 h-7 text-[#FF7F11]"></i>
                    </div>
                    <p class="text-base font-extrabold text-stone-900 mb-2">No saved searches yet</p>
                    <p class="text-sm text-stone-500 leading-relaxed">
                        As you save searches on Rondera, they will show up here so you never miss a listing.
                    </p>
                    <a href="index.php"
                        class="mt-8 inline-flex items-center gap-2 bg-black hover:bg-stone-800 text-white font-bold px-7 py-3 rounded-full transition shadow-sm text-sm">
                        <i data-feather="search" class="w-4 h-4"></i>
                        Start searching
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Sidebar: Saved Searches Summary -->
        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-[#FF7F11] flex items-center justify-center">
                        <i data-feather="bookmark" class="w-4 h-4 text-white"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-stone-900">Saved searches</h2>
                </div>
                <p class="text-sm text-stone-500 leading-relaxed">
                    As you save searches on Rondera, they will show up here.
                </p>
                <div class="border-t border-gray-100 mt-5 pt-4">
                    <div class="flex items-center gap-2 text-xs text-stone-400">
                        <i data-feather="info" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>Log in to save and manage your searches.</span>
                    </div>
                    <a href="../Authantification/login.php"
                        class="mt-4 w-full flex items-center justify-center gap-2 border-2 border-black text-black hover:bg-black hover:text-white font-bold px-4 py-2.5 rounded-full transition text-sm">
                        <i data-feather="log-in" class="w-4 h-4"></i>
                        Log in
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tab) {
    // Update panels
    document.getElementById('panel-check').classList.toggle('hidden', tab !== 'check');
    document.getElementById('panel-saved').classList.toggle('hidden', tab !== 'saved');

    // Update tab buttons
    const tabs = ['check', 'saved'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.classList.remove('border-transparent', 'text-stone-500');
            btn.classList.add('border-[#FF7F11]', 'text-[#FF7F11]');
        } else {
            btn.classList.add('border-transparent', 'text-stone-500');
            btn.classList.remove('border-[#FF7F11]', 'text-[#FF7F11]');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>