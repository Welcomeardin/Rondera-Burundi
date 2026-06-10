<?php
$title = 'Messages';
ob_start();
session_start();
require_once __DIR__ . '/includes/marketplace_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . loginRedirectUrl('chat.php'));
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

$userId = (int) $_SESSION['user_id'];
$myInitials = userInitials(trim(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')) ?: 'Me');

$activeAdId = (int) ($_GET['ad_id'] ?? 0);
$activeOtherUserId = (int) ($_GET['user_id'] ?? 0);
$orderSuccess = isset($_GET['order']) && $_GET['order'] === 'success';

if ($activeAdId > 0 && $activeOtherUserId <= 0) {
    $product = getProductById($pdo, $activeAdId);
    if ($product && !empty($product['seller_id']) && (int) $product['seller_id'] !== $userId) {
        $activeOtherUserId = (int) $product['seller_id'];
    }
}

$conversations = getConversations($pdo, $userId);

if ($activeOtherUserId > 0) {
    $hasConversation = false;
    foreach ($conversations as $conversation) {
        if ((int) $conversation['other_user_id'] === $activeOtherUserId) {
            $hasConversation = true;
            break;
        }
    }

    if (!$hasConversation) {
        $product = $activeAdId > 0 ? getProductById($pdo, $activeAdId) : null;
        $userStmt = $pdo->prepare('SELECT full_name FROM users WHERE id = ? LIMIT 1');
        $userStmt->execute([$activeOtherUserId]);
        $otherUser = $userStmt->fetch();

        array_unshift($conversations, [
            'ad_id' => $activeAdId,
            'other_user_id' => $activeOtherUserId,
            'other_user_name' => $otherUser['full_name'] ?? 'User',
            'other_user_initials' => userInitials($otherUser['full_name'] ?? 'User'),
            'last_message' => 'Start the conversation…',
            'last_message_at' => date('Y-m-d H:i:s'),
            'unread_count' => 0,
            'listing_count' => 1,
            'product_title' => $product['title'] ?? 'Listing',
            'product_price' => $product['price_display'] ?? '',
            'product_image' => $product['img'] ?? '',
        ]);
    }
}

if ($activeOtherUserId <= 0 && !empty($conversations)) {
    $activeOtherUserId = (int) $conversations[0]['other_user_id'];
    $activeAdId = (int) $conversations[0]['ad_id'];
}

if ($activeOtherUserId > 0 && $activeAdId <= 0) {
    $activeAdId = getLatestAdIdInThread($pdo, $userId, $activeOtherUserId);
}

$activeProduct = $activeAdId > 0 ? getProductById($pdo, $activeAdId) : null;
$activeMessages = [];

if ($activeOtherUserId > 0) {
    $activeMessages = getThreadMessages($pdo, $userId, $activeOtherUserId);
}

$activeOtherName = 'Select a conversation';
$activeOtherInitials = '??';

if ($activeOtherUserId > 0) {
    $userStmt = $pdo->prepare('SELECT full_name FROM users WHERE id = ? LIMIT 1');
    $userStmt->execute([$activeOtherUserId]);
    $activeOther = $userStmt->fetch();
    $activeOtherName = $activeOther['full_name'] ?? 'User';
    $activeOtherInitials = userInitials($activeOtherName);
}
?>

<style>
    .chat-container {
        height: calc(100vh - 140px);
        min-height: 500px;
    }

    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 120px);
        }
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

    <?php if ($orderSuccess): ?>
        <div class="mb-4 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold border border-green-200">
            Your order was placed successfully. Continue the conversation with the seller below.
        </div>
    <?php endif; ?>

    <div class="flex chat-container border border-gray-200 rounded-2xl overflow-hidden shadow-sm bg-white relative">

        <!-- LEFT: Conversations List -->
        <div class="w-full max-w-xs flex-shrink-0 border-r border-gray-100 flex flex-col md:flex" id="conversations-panel">

            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <button onclick="toggleMobileViews('conv')" class="md:hidden p-1.5 hover:bg-gray-100 rounded-lg transition">
                        <i data-feather="arrow-left" class="w-5 h-5 text-stone-700"></i>
                    </button>
                    <h2 class="font-bold text-stone-900">Conversations</h2>
                </div>
                <div class="relative">
                    <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400"></i>
                    <input type="text" id="search-conversations" placeholder="Search messages…"
                        class="w-full pl-9 pr-4 py-2.5 bg-stone-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#FF7F11]/30 transition">
                </div>
            </div>

            <div class="overflow-y-auto flex-1" id="conversations-list">
                <?php if (empty($conversations)): ?>
                    <div class="p-6 text-center text-sm text-stone-400">
                        No conversations yet.<br>
                        <a href="index.php" class="text-[#FF7F11] font-semibold hover:underline">Browse listings</a> to start chatting.
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conversation):
                        $isActive = (int) $conversation['other_user_id'] === $activeOtherUserId;
                    ?>
                        <div class="conv-item <?= $isActive ? 'active' : '' ?> flex items-center gap-3 px-4 py-3.5 cursor-pointer transition"
                            data-ad-id="<?= (int) $conversation['ad_id'] ?>"
                            data-user-id="<?= (int) $conversation['other_user_id'] ?>"
                            onclick="openConversation(<?= (int) $conversation['other_user_id'] ?>, <?= (int) $conversation['ad_id'] ?>, this)">
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 rounded-full bg-[#FF7F11] flex items-center justify-center font-bold text-white text-base">
                                    <?= htmlspecialchars($conversation['other_user_initials']) ?>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <span class="font-bold text-stone-900 text-sm truncate"><?= htmlspecialchars($conversation['other_user_name']) ?></span>
                                    <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2"><?= formatMessageTime($conversation['last_message_at']) ?></span>
                                </div>
                                <p class="text-xs text-stone-500 truncate"><?= htmlspecialchars($conversation['last_message']) ?></p>
                                <p class="text-[10px] text-stone-400 truncate mt-0.5"><?= htmlspecialchars($conversation['product_title']) ?></p>
                            </div>
                            <?php if ((int) $conversation['unread_count'] > 0): ?>
                                <span class="w-5 h-5 bg-[#FF7F11] rounded-full text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">
                                    <?= (int) $conversation['unread_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Message Thread -->
        <div class="flex-1 flex flex-col min-w-0 md:flex" id="chat-thread">

            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-white flex-shrink-0">
                <button onclick="toggleMobileViews('thread')" class="md:hidden p-1.5 hover:bg-gray-100 rounded-lg transition">
                    <i data-feather="arrow-left" class="w-5 h-5 text-stone-700"></i>
                </button>
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-[#FF7F11] flex items-center justify-center font-bold text-white" id="chat-avatar">
                        <?= htmlspecialchars($activeOtherInitials) ?>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-stone-900 text-sm" id="chat-name"><?= htmlspecialchars($activeOtherName) ?></h2>
                    <p class="text-xs text-stone-400 font-medium" id="chat-status">About this listing</p>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <?php if ($activeProduct): ?>
                        <a href="product.php?id=<?= (int) $activeAdId ?>" id="product-pill"
                            class="hidden md:flex items-center gap-2 bg-stone-50 border border-gray-200 rounded-full px-3 py-1.5 text-xs text-stone-600 hover:bg-stone-100 transition">
                            <img src="<?= htmlspecialchars($activeProduct['img']) ?>" alt="" class="w-7 h-7 rounded-full object-cover flex-shrink-0" id="product-pill-image">
                            <span class="font-semibold truncate max-w-[140px]" id="product-pill-title"><?= htmlspecialchars($activeProduct['title']) ?></span>
                            <span class="text-stone-400" id="product-pill-price">· <?= htmlspecialchars($activeProduct['price_display']) ?></span>
                        </a>
                    <?php else: ?>
                        <div id="product-pill" class="hidden md:flex items-center gap-2 bg-stone-50 border border-gray-200 rounded-full px-3 py-1.5 text-xs text-stone-600"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="messages-area flex-1 overflow-y-auto px-5 py-5 flex flex-col gap-4 bg-stone-50/40" id="messages-area">
                <?php if ($activeOtherUserId <= 0): ?>
                    <div class="flex-1 flex items-center justify-center text-sm text-stone-400">
                        Select a conversation or start one from a product page.
                    </div>
                <?php elseif (empty($activeMessages)): ?>
                    <div class="flex items-center gap-3 my-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] text-stone-400 font-medium px-2">New conversation</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <div class="text-center text-sm text-stone-400 py-8">
                        Say hello about <strong class="text-stone-600"><?= htmlspecialchars($activeProduct['title'] ?? 'this listing') ?></strong>
                    </div>
                <?php else: ?>
                    <?php
                    $lastDate = '';
                    $lastAdId = 0;
                    foreach ($activeMessages as $msg):
                        $msgDate = date('Y-m-d', strtotime($msg['created_at']));
                        if ($msgDate !== $lastDate):
                            $lastDate = $msgDate;
                            $label = ($msgDate === date('Y-m-d')) ? 'Today' : (($msgDate === date('Y-m-d', strtotime('-1 day'))) ? 'Yesterday' : date('M j, Y', strtotime($msg['created_at'])));
                    ?>
                        <div class="flex items-center gap-3 my-1">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-[10px] text-stone-400 font-medium px-2"><?= $label ?></span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                    <?php endif;

                        $msgAdId = (int) ($msg['ad_id'] ?? 0);
                        if ($msgAdId > 0 && $msgAdId !== $lastAdId):
                            $lastAdId = $msgAdId;
                            $msgProduct = getProductById($pdo, $msgAdId);
                    ?>
                        <div class="flex items-center gap-3 my-2">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <a href="product.php?id=<?= $msgAdId ?>" class="text-[10px] text-[#FF7F11] font-semibold px-2 hover:underline truncate max-w-[60%]">
                                <?= htmlspecialchars($msgProduct['title'] ?? $msg['ad_title'] ?? 'Listing') ?>
                            </a>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                    <?php endif;

                        $isMine = (int) $msg['sender_id'] === $userId;
                        $initials = $isMine ? $myInitials : userInitials($msg['sender_name'] ?? '');
                        $isOrderMsg = str_contains($msg['message'], 'placed an order');
                    ?>
                        <div class="flex items-end gap-2 max-w-[85%] md:max-w-[75%] <?= $isMine ? 'self-end flex-row-reverse' : '' ?>">
                            <div class="w-7 h-7 rounded-full <?= $isMine ? 'bg-black text-[#FF7F11]' : 'bg-[#FF7F11] text-white' ?> flex-shrink-0 flex items-center justify-center text-[10px] font-bold mb-1">
                                <?= htmlspecialchars($initials) ?>
                            </div>
                            <div>
                                <div class="<?= $isMine ? 'msg-bubble-out bg-[#FF7F11] text-white' : 'msg-bubble-in bg-white border border-gray-200 text-stone-800' ?> <?= $isOrderMsg ? 'ring-2 ring-green-200' : '' ?> shadow-sm px-4 py-2.5 text-sm">
                                    <?php if ($isOrderMsg): ?><span class="text-[10px] font-bold opacity-80 block mb-1">🛒 Order</span><?php endif; ?>
                                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                </div>
                                <p class="text-[10px] text-stone-400 mt-1 <?= $isMine ? 'mr-1 text-right' : 'ml-1' ?>">
                                    <?= formatMessageTime($msg['created_at']) ?>
                                    <?php if ($isMine): ?> · <span class="text-[#FF7F11]">✓✓</span><?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="px-4 py-3.5 border-t border-gray-100 bg-white flex-shrink-0">
                <?php if ($activeOtherUserId > 0): ?>
                    <div class="flex items-center gap-3 bg-stone-50 border border-gray-200 rounded-full px-4 py-2.5">
                        <input id="message-input" type="text" placeholder="Type a message…"
                            class="flex-1 bg-transparent text-sm text-stone-800 placeholder-stone-400 min-w-0"
                            onkeydown="if(event.key==='Enter') sendMessage()">
                        <button onclick="sendMessage()"
                            class="w-8 h-8 bg-[#FF7F11] hover:bg-[#e06c09] rounded-full flex items-center justify-center flex-shrink-0 transition shadow-sm">
                            <i data-feather="send" class="w-4 h-4 text-white"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <p class="text-center text-sm text-stone-400 py-2">Choose a conversation to start messaging.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
    const currentUserId = <?= $userId ?>;
    const myInitials = <?= json_encode($myInitials) ?>;
    let activeAdId = <?= $activeAdId ?>;
    let activeOtherUserId = <?= $activeOtherUserId ?>;

    function toggleMobileViews(show) {
        const convPanel = document.getElementById('conversations-panel');
        const threadPanel = document.getElementById('chat-thread');
        
        if (show === 'conv') {
            convPanel.classList.remove('hidden');
            threadPanel.classList.add('hidden');
        } else {
            convPanel.classList.add('hidden');
            threadPanel.classList.remove('hidden');
        }
    }

    function setupMobileViews() {
        if (window.innerWidth < 768) { // md breakpoint
            if (!activeOtherUserId) {
                toggleMobileViews('conv');
            } else {
                toggleMobileViews('thread');
            }
        } else {
            document.getElementById('conversations-panel').classList.remove('hidden');
            document.getElementById('chat-thread').classList.remove('hidden');
        }
    }
    window.addEventListener('resize', setupMobileViews);
    window.addEventListener('load', setupMobileViews);

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTimeLabel(datetime) {
        const date = new Date(datetime.replace(' ', 'T'));
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const msgDay = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        if (msgDay.getTime() === today.getTime()) {
            return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        }
        return 'Just now';
    }

    function renderMessages(messages) {
        const area = document.getElementById('messages-area');
        area.innerHTML = '';

        if (!messages.length) {
            area.innerHTML = `
                <div class="flex items-center gap-3 my-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] text-stone-400 font-medium px-2">New conversation</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>
                <div class="text-center text-sm text-stone-400 py-8">Say hello to start chatting.</div>`;
            return;
        }

        let lastAdId = 0;
        messages.forEach(msg => {
            const msgAdId = parseInt(msg.ad_id, 10) || 0;
            if (msgAdId > 0 && msgAdId !== lastAdId) {
                lastAdId = msgAdId;
                const divider = document.createElement('div');
                divider.className = 'flex items-center gap-3 my-2';
                const title = msg.ad_title || 'Listing';
                divider.innerHTML = `
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <a href="product.php?id=${msgAdId}" class="text-[10px] text-[#FF7F11] font-semibold px-2 hover:underline truncate max-w-[60%]">${escapeHtml(title)}</a>
                    <div class="flex-1 h-px bg-gray-200"></div>`;
                area.appendChild(divider);
            }

            const isMine = parseInt(msg.sender_id, 10) === currentUserId;
            const initials = isMine ? myInitials : (msg.sender_name || 'U').split(' ').map(p => p[0]).join('').slice(0, 2).toUpperCase();
            const isOrderMsg = (msg.message || '').includes('placed an order');
            const bubble = document.createElement('div');
            bubble.className = `flex items-end gap-2 max-w-[85%] md:max-w-[75%] ${isMine ? 'self-end flex-row-reverse' : ''}`;
            bubble.innerHTML = `
                <div class="w-7 h-7 rounded-full ${isMine ? 'bg-black text-[#FF7F11]' : 'bg-[#FF7F11] text-white'} flex-shrink-0 flex items-center justify-center text-[10px] font-bold mb-1">${escapeHtml(initials)}</div>
                <div>
                    <div class="${isMine ? 'msg-bubble-out bg-[#FF7F11] text-white' : 'msg-bubble-in bg-white border border-gray-200 text-stone-800'} ${isOrderMsg ? 'ring-2 ring-green-200' : ''} shadow-sm px-4 py-2.5 text-sm">
                        ${isOrderMsg ? '<span class="text-[10px] font-bold opacity-80 block mb-1">🛒 Order</span>' : ''}
                        ${escapeHtml(msg.message).replace(/\n/g, '<br>')}
                    </div>
                    <p class="text-[10px] text-stone-400 mt-1 ${isMine ? 'mr-1 text-right' : 'ml-1'}">${formatTimeLabel(msg.created_at)}${isMine ? ' · <span class="text-[#FF7F11]">✓✓</span>' : ''}</p>
                </div>`;
            area.appendChild(bubble);
        });

        area.scrollTop = area.scrollHeight;
    }

    function updateProductPill(product) {
        const pill = document.getElementById('product-pill');
        if (!pill || !product) return;

        pill.href = `product.php?id=${activeAdId}`;
        pill.innerHTML = `
            <img src="${escapeHtml(product.img)}" alt="" class="w-7 h-7 rounded-full object-cover flex-shrink-0" id="product-pill-image">
            <span class="font-semibold truncate max-w-[140px]" id="product-pill-title">${escapeHtml(product.title)}</span>
            <span class="text-stone-400" id="product-pill-price">· ${escapeHtml(product.price_display || '')}</span>`;
        pill.classList.remove('hidden');
    }

    async function openConversation(userId, adId, el) {
        activeOtherUserId = userId;
        activeAdId = adId;

        document.querySelectorAll('.conv-item').forEach(item => item.classList.remove('active'));
        if (el) el.classList.add('active');

        const params = new URLSearchParams({ action: 'messages', user_id: userId });
        if (adId > 0) params.set('ad_id', adId);
        const response = await fetch('chat_api.php?' + params.toString());
        const data = await response.json();

        if (!data.success) return;

        if (data.ad_id) activeAdId = data.ad_id;
        document.getElementById('chat-name').textContent = data.other_user.name;
        document.getElementById('chat-avatar').textContent = data.other_user.initials;
        if (data.product) updateProductPill(data.product);
        renderMessages(data.messages);

        if (window.innerWidth < 768) toggleMobileViews('thread');

        const url = activeAdId > 0
            ? `chat.php?user_id=${userId}&ad_id=${activeAdId}`
            : `chat.php?user_id=${userId}`;
        history.replaceState(null, '', url);
    }

    async function sendMessage() {
        const input = document.getElementById('message-input');
        const text = input.value.trim();
        if (!text || !activeOtherUserId) return;

        const formData = new FormData();
        formData.append('action', 'send');
        formData.append('ad_id', activeAdId);
        formData.append('receiver_id', activeOtherUserId);
        formData.append('message', text);

        const response = await fetch('chat_api.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (!data.success) return;

        const area = document.getElementById('messages-area');
        if (area.querySelector('.text-center.text-sm.text-stone-400')) {
            area.innerHTML = '';
        }

        const msg = data.message;
        const bubble = document.createElement('div');
        bubble.className = 'flex items-end gap-2 max-w-[85%] md:max-w-[75%] self-end flex-row-reverse';
        bubble.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-black flex-shrink-0 flex items-center justify-center text-[#FF7F11] text-[10px] font-bold mb-1">${escapeHtml(myInitials)}</div>
            <div>
                <div class="msg-bubble-out bg-[#FF7F11] px-4 py-2.5 text-sm text-white shadow-sm">${escapeHtml(msg.message)}</div>
                <p class="text-[10px] text-stone-400 mt-1 mr-1 text-right">Just now · <span class="text-[#FF7F11]">✓✓</span></p>
            </div>`;
        area.appendChild(bubble);
        input.value = '';
        area.scrollTop = area.scrollHeight;
    }

    document.getElementById('search-conversations')?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#conversations-list .conv-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });
    });

    window.addEventListener('load', () => {
        const area = document.getElementById('messages-area');
        if (area) area.scrollTop = area.scrollHeight;
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/template/global.php';
?>
