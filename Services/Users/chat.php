<?php
// chat.php
$title = 'Messages';
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}
?>

<style>
    .chat-container {
        height: calc(100vh - 200px);
        min-height: 520px;
    }

    .msg-bubble-out {
        border-radius: 18px 18px 4px 18px;
    }

    .msg-bubble-in {
        border-radius: 18px 18px 18px 4px;
    }

    .conv-item:hover,
    .conv-item.active {
        background: #fff7f0;
    }

    .conv-item.active {
        border-left: 3px solid #FF7F11;
    }

    #message-input:focus {
        outline: none;
    }

    .messages-area::-webkit-scrollbar {
        width: 4px;
    }

    .messages-area::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 4px;
    }
</style>

<div class="py-4">
    <h1 class="text-2xl md:text-3xl font-extrabold text-stone-900 mb-5 tracking-tight">Messages</h1>

    <div class="flex chat-container border border-gray-200 rounded-2xl overflow-hidden shadow-sm bg-white">

        <!-- LEFT: Conversations List -->
        <div class="w-full max-w-xs flex-shrink-0 border-r border-gray-100 flex flex-col hidden md:flex">

            <!-- Search bar -->
            <div class="p-4 border-b border-gray-100">
                <div class="relative">
                    <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400"></i>
                    <input type="text" placeholder="Search messages…"
                        class="w-full pl-9 pr-4 py-2.5 bg-stone-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#FF7F11]/30 transition">
                </div>
            </div>

            <!-- Conversation items -->
            <div class="overflow-y-auto flex-1">

                <!-- Active conversation -->
                <div class="conv-item active flex items-center gap-3 px-4 py-3.5 cursor-pointer transition" onclick="openChat(this, 'Jean Pierre')">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-[#FF7F11] flex items-center justify-center font-bold text-white text-base">JP</div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-stone-900 text-sm truncate">Jean Pierre</span>
                            <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2">10:24</span>
                        </div>
                        <p class="text-xs text-stone-500 truncate">Is the car still available?</p>
                    </div>
                    <span class="w-5 h-5 bg-[#FF7F11] rounded-full text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">2</span>
                </div>

                <div class="conv-item flex items-center gap-3 px-4 py-3.5 cursor-pointer transition" onclick="openChat(this, 'Marie Noel')">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center font-bold text-[#FF7F11] text-base">MN</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-stone-900 text-sm truncate">Marie Noel</span>
                            <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2">Yesterday</span>
                        </div>
                        <p class="text-xs text-stone-400 truncate">Thanks! I'll come see it tomorrow.</p>
                    </div>
                </div>

                <div class="conv-item flex items-center gap-3 px-4 py-3.5 cursor-pointer transition" onclick="openChat(this, 'Alexis B.')">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-stone-200 flex items-center justify-center font-bold text-stone-600 text-base">AB</div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-stone-900 text-sm truncate">Alexis B.</span>
                            <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2">Mon</span>
                        </div>
                        <p class="text-xs text-stone-400 truncate">What's the lowest you can go?</p>
                    </div>
                </div>

                <div class="conv-item flex items-center gap-3 px-4 py-3.5 cursor-pointer transition" onclick="openChat(this, 'Claudette M.')">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center font-bold text-[#FF7F11] text-base">CM</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-stone-900 text-sm truncate">Claudette M.</span>
                            <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2">Sun</span>
                        </div>
                        <p class="text-xs text-stone-400 truncate">Can I visit the property on Friday?</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- RIGHT: Message Thread -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Chat Header -->
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-white flex-shrink-0">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-[#FF7F11] flex items-center justify-center font-bold text-white">JP</div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white"></span>
                </div>
                <div>
                    <h2 class="font-bold text-stone-900 text-sm" id="chat-name">Jean Pierre</h2>
                    <p class="text-xs text-green-500 font-medium">Online</p>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <!-- Listing preview pill -->
                    <div class="hidden md:flex items-center gap-2 bg-stone-50 border border-gray-200 rounded-full px-3 py-1.5 text-xs text-stone-600">
                        <i data-feather="truck" class="w-3.5 h-3.5 text-[#FF7F11]"></i>
                        <span class="font-semibold">Toyota Corolla 2019</span>
                        <span class="text-stone-400">· $12,500</span>
                    </div>
                    <button class="p-2 hover:bg-stone-50 rounded-full transition">
                        <i data-feather="more-vertical" class="w-4 h-4 text-stone-400"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-area flex-1 overflow-y-auto px-5 py-5 flex flex-col gap-4 bg-stone-50/40" id="messages-area">

                <!-- Date separator -->
                <div class="flex items-center gap-3 my-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] text-stone-400 font-medium px-2">Today</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Incoming message -->
                <div class="flex items-end gap-2 max-w-[75%]">
                    <div class="w-7 h-7 rounded-full bg-[#FF7F11] flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold mb-1">JP</div>
                    <div>
                        <div class="msg-bubble-in bg-white border border-gray-200 shadow-sm px-4 py-2.5 text-sm text-stone-800">
                            Hi! I saw your listing for the Toyota Corolla. Is it still available?
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1 ml-1">10:18 AM</p>
                    </div>
                </div>

                <!-- Outgoing message -->
                <div class="flex items-end gap-2 max-w-[75%] self-end flex-row-reverse">
                    <div class="w-7 h-7 rounded-full bg-black flex-shrink-0 flex items-center justify-center text-[#FF7F11] text-[10px] font-bold mb-1">Me</div>
                    <div>
                        <div class="msg-bubble-out bg-[#FF7F11] px-4 py-2.5 text-sm text-white shadow-sm">
                            Yes, it is! Just had it serviced last week. Are you interested in viewing it?
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1 mr-1 text-right">10:20 AM · <i class="inline text-[#FF7F11]">✓✓</i></p>
                    </div>
                </div>

                <!-- Incoming -->
                <div class="flex items-end gap-2 max-w-[75%]">
                    <div class="w-7 h-7 rounded-full bg-[#FF7F11] flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold mb-1">JP</div>
                    <div>
                        <div class="msg-bubble-in bg-white border border-gray-200 shadow-sm px-4 py-2.5 text-sm text-stone-800">
                            Yes, I'd love to! Is the car still available?
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1 ml-1">10:24 AM</p>
                    </div>
                </div>

                <!-- Typing indicator -->
                <div class="flex items-end gap-2 max-w-[75%]" id="typing-indicator">
                    <div class="w-7 h-7 rounded-full bg-[#FF7F11] flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold">JP</div>
                    <div class="msg-bubble-in bg-white border border-gray-200 shadow-sm px-4 py-3 flex gap-1 items-center">
                        <span class="w-2 h-2 bg-stone-300 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-2 h-2 bg-stone-300 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-2 h-2 bg-stone-300 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>

            </div>

            <!-- Message Input -->
            <div class="px-4 py-3.5 border-t border-gray-100 bg-white flex-shrink-0">
                <div class="flex items-center gap-3 bg-stone-50 border border-gray-200 rounded-full px-4 py-2.5">
                    <button class="text-stone-400 hover:text-[#FF7F11] transition flex-shrink-0">
                        <i data-feather="paperclip" class="w-4 h-4"></i>
                    </button>
                    <input id="message-input" type="text" placeholder="Type a message…"
                        class="flex-1 bg-transparent text-sm text-stone-800 placeholder-stone-400 min-w-0"
                        onkeydown="if(event.key==='Enter') sendMessage()">
                    <button class="text-stone-400 hover:text-[#FF7F11] transition flex-shrink-0">
                        <i data-feather="smile" class="w-4 h-4"></i>
                    </button>
                    <button onclick="sendMessage()"
                        class="w-8 h-8 bg-[#FF7F11] hover:bg-[#e06c09] rounded-full flex items-center justify-center flex-shrink-0 transition shadow-sm">
                        <i data-feather="send" class="w-4 h-4 text-white"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function sendMessage() {
        const input = document.getElementById('message-input');
        const text = input.value.trim();
        if (!text) return;

        const area = document.getElementById('messages-area');
        const typing = document.getElementById('typing-indicator');

        // Create outgoing bubble
        const msg = document.createElement('div');
        msg.className = 'flex items-end gap-2 max-w-[75%] self-end flex-row-reverse';
        msg.innerHTML = `
        <div class="w-7 h-7 rounded-full bg-black flex-shrink-0 flex items-center justify-center text-[#FF7F11] text-[10px] font-bold mb-1">Me</div>
        <div>
            <div class="msg-bubble-out bg-[#FF7F11] px-4 py-2.5 text-sm text-white shadow-sm">${text}</div>
            <p class="text-[10px] text-stone-400 mt-1 mr-1 text-right">Just now · <span class="text-[#FF7F11]">✓✓</span></p>
        </div>`;

        area.insertBefore(msg, typing);
        input.value = '';
        area.scrollTop = area.scrollHeight;
    }

    function openChat(el, name) {
        document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('chat-name').textContent = name;
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>