<script>
    window.BASE_URL = "/";
    window.CURRENT_MATK = "<?= $_SESSION['MaTK'] ?>";
</script>
<?php
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* ================= AI FLOAT ================= */
#ai-float{
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #0a8f3c, #0fc15c);
    border-radius: 50%;
    box-shadow: 0 8px 20px rgba(0,0,0,.3);
    cursor: grab;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
}
#ai-float i{
    font-size: 34px;
    color: white;
}
/* ================= AI CHAT BOX ================= */
#ai-chat-box{
    position: fixed;
    bottom: 120px;
    right: 40px;
    width: 330px;
    height: 420px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,.35);
    display: none;
    flex-direction: column;
    z-index: 99999;
}
.ai-header{
    background: #0a8f3c;
    color: white;
    padding: 12px;
    font-weight: bold;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
}
.ai-body{
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #fff;
}
.ai-msg {
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 10px;
    font-size: 14px;
    /* --- BỔ SUNG 2 DÒNG NÀY ĐỂ HIỆN CHỮ CHUẨN --- */
    width: fit-content;    /* Giúp bong bóng co lại vừa khít với nội dung chữ */
    max-width: 85%;       /* Không cho bong bóng tràn quá chiều ngang khung chat */
    word-wrap: break-word; /* Tự xuống dòng nếu gặp nội dung quá dài */
}

.ai-msg.ai { 
    background: #e2ffe9; 
    margin-right: auto;    /* Đẩy toàn bộ bong bóng chat AI sang bên trái */
}

.ai-msg.user { 
    background: #d9e8ff; 
    margin-left: auto;     /* Đẩy toàn bộ bong bóng chat User sang bên phải */
    text-align: left;      /* Giữ chữ bên trong căn trái cho dễ đọc nội dung */
}

.ai-input {
    display: flex;
    padding: 8px;
    gap: 6px;
    border-top: 1px solid #ddd; 
    background: #fff;
    border-radius: 0 0 15px 15px; 
    
}

.ai-input input {
    flex: 1;
    padding: 8px 12px;     
    border-radius: 20px;      
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
}

.ai-input button {
    background: #0a8f3c;
    color: white;
    border: none;
    padding: 8px 14px;       
    border-radius: 20px;      
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.ai-input button:hover {
    background: #087233;      
}
/* ================= MESSENGER FLOAT ================= */
#messenger-float{
    position: fixed;
    bottom: 40px;
    right: 120px; /* tách khỏi AI */
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #0a8f3c, #0fc15c);
    border-radius: 50%;
    box-shadow: 0 8px 20px rgba(0,0,0,.3);
    cursor: pointer;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
}
#messenger-float i{
    font-size: 34px;
    color: white;
}
/* ===== UNREAD BADGE ===== */
.messenger-badge{
    position: absolute;
    top: 6px;
    right: 6px;
    background: red;
    color: white;
    font-size: 12px;
    font-weight: bold;
    min-width: 25px;
    height: 25px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    line-height: 18px;
}
/* ================= MESSENGER BOX ================= */
#messenger-box{
    position: fixed;
    bottom: 120px;
    right: 40px;
    width: 330px;      
    height: 420px;     
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,.35);
    display: none;
    transition: right .3s ease;
    flex-direction: column;
    z-index: 99999;
    overflow: hidden;
}
#messenger-box.shift-left{
    right: 380px;
}
/* ================= HEADER ================= */
.messenger-header{
    background: #0a8f3c;
    color: white;
    padding: 12px 14px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
/* ================= USER LIST ================= */
.messenger-user-list{
    background: #f5f6f7;
    padding: 6px;
    border-bottom: 1px solid #ddd;
}
.messenger-user{
    display: flex;
    gap: 10px;
    padding: 6px;
    border-radius: 8px;
    cursor: pointer;
    align-items: center;
}
.messenger-user:hover{
    background: #e4e6eb;
}
.messenger-user.active{
    background: #dbeeff;
}
.messenger-user img{
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}
.messenger-user .name{
    font-size: 14px;
    font-weight: bold;
}
.messenger-user .status{
    font-size: 12px;
    color: green;
}
.messenger-user .status.offline{
    color: green;
}
/* ================= CHAT ================= */
.messenger-chat{
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f0f2f5;
}
/* ================= MESSAGES ================= */
.messenger-messages{
    flex: 1;
    padding: 10px;
    overflow-y: auto;
}
.msg-row{
    display: flex;
    margin-bottom: 8px;
    align-items: flex-end;
}
.msg-row.other{
    justify-content: flex-start;
}
.msg-row.me{
    justify-content: flex-end;
}
.msg-content{
    display: flex;
    flex-direction: column;
    max-width: 80%;
    min-width: 60px; 
}

.msg-row.other .msg-content{
    align-items: flex-start;
}

.msg-row.me .msg-content{
    align-items: flex-end;
}

.sender-name{
    font-size: 12px;
    color: #555;
    margin-bottom: 2px;
    margin-left: 6px;
    font-weight: bold;
}
.msg-row img{
    width: 28px;
    height: 28px;
    border-radius: 50%;
    margin-right: 6px;
}
.msg-bubble{
    padding: 8px 12px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.4;
}
.msg-row.other .msg-bubble{
    background: #e2ffe9;;
    color: #000;
    border-bottom-left-radius: 4px;
}
.msg-row.me .msg-bubble{
    background: #0a8f3c;
    color: white;
    border-bottom-right-radius: 4px;
}
.msg-time{
    font-size: 11px;
    color: #888;
    margin-top: 2px;
    padding: 0 6px;
}
.msg-row.me .msg-time{
    text-align: right;
}
.time-divider{
    text-align: center;
    font-size: 12px;
    color: #777;
    margin: 12px 0;
}
/* ================= INPUT ================= */
.messenger-input{
    display: flex;
    padding: 8px;
    gap: 6px;
    border-top: 1px solid #ddd;
    background: #fff;
}
.messenger-input input{
    flex: 1;
    padding: 8px 10px;
    border-radius: 20px;
    border: 1px solid #ccc;
    outline: none;
}
.messenger-input button{
    background: #0a8f3c;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 20px;
    cursor: pointer;
}
.messenger-input button:hover{
    background: #087233;
}
</style>
<div id="ai-float">
    <i class="fa-solid fa-robot"></i>
</div>
<div id="ai-chat-box">
    <div class="ai-header">
        🤖 Trợ lý AI Bách Hóa Xanh
        <span id="ai-close">✖</span>
    </div>
    <div class="ai-body" id="ai-messages">
        <div class="ai-msg ai">Xin chào 👋 Tôi có thể hỗ trợ bạn về kho, phiếu nhập/xuất, thống kê…</div>
    </div>
    <div class="ai-input">
        <input type="text" id="ai-text" placeholder="Nhập câu hỏi...">
        <button onclick="sendAI()">Gửi</button>
    </div>
</div>
<div id="messenger-float">
    <i class="fa-brands fa-facebook-messenger"></i>
    <span class="messenger-badge" id="messenger-badge">0</span>
</div>
<div id="messenger-box">
    <!-- HEADER -->
    <div class="messenger-header">
        💬 Messenger nội bộ
        <span id="messenger-close">✖</span>
    </div>
    <!-- BODY CHAT -->
   <div class="messenger-messages" id="messenger-messages">
    <div class="msg-row other">
        <div class="msg-bubble">      
        </div>
    </div>
    <div class="msg-row me">
        <div class="msg-content">
            <div class="msg-bubble">
            </div>
        </div>
    </div>
</div>
    <!-- INPUT -->
    <div class="messenger-input">
        <input type="text" id="messenger-text" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessenger()">Gửi</button>
    </div>
</div>
<script>
(() => {
    const aiFloat = document.getElementById('ai-float');
    const aiChat  = document.getElementById('ai-chat-box');
    const aiClose = document.getElementById('ai-close');
    const aiInput = document.getElementById('ai-text');

    let isDragging = false;
    let moved = false;
    let offsetX = 0, offsetY = 0;
    let startX = 0, startY = 0;


    aiFloat.addEventListener('mousedown', e => {
        isDragging = true;
        moved = false;

        startX = e.clientX;
        startY = e.clientY;

        offsetX = e.clientX - aiFloat.offsetLeft;
        offsetY = e.clientY - aiFloat.offsetTop;
    });
    document.addEventListener('mousemove', e => {
        if (!isDragging) return;
        if (Math.abs(e.clientX - startX) > 5 || Math.abs(e.clientY - startY) > 5) {
            moved = true;
        }
        if (moved) {
            aiFloat.style.left = (e.clientX - offsetX) + 'px';
            aiFloat.style.top  = (e.clientY - offsetY) + 'px';
        }
    });
    document.addEventListener('mouseup', () => {
        isDragging = false;
    });
    aiFloat.addEventListener('click', () => {
       if (moved) return;

    const isOpen = aiChat.style.display === 'flex';

    if (isOpen) {
        aiChat.style.display = 'none';
        localStorage.setItem('ai_open', '0');

        document.getElementById('messenger-box')
            .classList.remove('shift-left');
    } else {
        aiChat.style.display = 'flex';
        localStorage.setItem('ai_open', '1');

        document.getElementById('messenger-box')
            .classList.add('shift-left');
    }
    });
    aiClose.addEventListener('click', () => {
        aiChat.style.display = 'none';
        localStorage.setItem('ai_open', '0');
        const messengerBox = document.getElementById('messenger-box');
        messengerBox.classList.remove('shift-left');
    });
    window.sendAI = function () {
    const aiInput = document.getElementById('ai-text');
    const msg = aiInput.value.trim();
    const box = document.getElementById('ai-messages');

    if (!msg) return;

    // 1. Thêm tin nhắn của User
    const userMsg = document.createElement('div');
    userMsg.className = 'ai-msg user';
    userMsg.textContent = msg;
    box.appendChild(userMsg);

    // Xóa input ngay lập tức
    aiInput.value = '';

    // Hàm cuộn dùng chung để tái sử dụng
    const scrollToBottom = () => {
        setTimeout(() => {
            box.scrollTop = box.scrollHeight;
        }, 50);
    };

    scrollToBottom();

    // 2. Hiển thị Loading (Dùng ID duy nhất để tránh xung đột nếu nhấn gửi nhanh)
    const loadingId = 'loading-' + Date.now();
    const loadingMsg = document.createElement('div');
    loadingMsg.className = 'ai-msg ai';
    loadingMsg.id = loadingId;
    loadingMsg.textContent = '🤖 Lora đang suy nghĩ...';
    box.appendChild(loadingMsg);
    
    scrollToBottom();

    // 3. Gọi API
    fetch("https://lora-ai-9ti1.onrender.com/api/chat", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text: msg })
    })
    .then(res => res.json())
    .then(data => {
        // Xóa đúng cái loading vừa tạo
        const loading = document.getElementById(loadingId);
        if (loading) loading.remove();

        const aiMsg = document.createElement('div');
        aiMsg.className = 'ai-msg ai';
        aiMsg.textContent = data.response;
        box.appendChild(aiMsg);

        scrollToBottom();
    })
    .catch(err => {
        const loading = document.getElementById(loadingId);
        if (loading) {
            loading.className = 'ai-msg ai error'; // Thêm class error nếu muốn đổi màu đỏ
            loading.textContent = '❌ Lỗi kết nối';
        }
        scrollToBottom();
    });
};
    aiInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendAI();
        }
    });
})();
const badge = document.getElementById('messenger-badge');
function updateBadgeFromDB(){
    fetch(window.BASE_URL +'demtrangthai.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('messenger-badge');
            if (data.unread > 0){
                badge.textContent = data.unread;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}
let lastTimeDivider = null;
let lastMessengerSender = null;
function receiveMessengerMessage(sender, text, msgTime){
    const box = document.getElementById('messenger-messages');

    const row = document.createElement('div');
    row.className = 'msg-row other';

    const content = document.createElement('div');
    content.className = 'msg-content';

    if (sender !== lastMessengerSender){
        const name = document.createElement('div');
        name.className = 'sender-name';
        name.textContent = sender;
        content.appendChild(name);
    }

    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble';
    bubble.textContent = text;

    content.appendChild(bubble);
    row.appendChild(content);
    box.appendChild(row);
    box.scrollTop = box.scrollHeight;

    lastMessengerSender = sender;
}
function shouldShowTime(prev, current){
    if (!prev) return true;

    const diffMinutes = (current - prev) / 1000 / 60;
    return diffMinutes >= 10; // 10 phút như FB
}
lastTimeDivider = null;
lastSender = null;
let currentMaCHT = 1;
let messengerOpened = false;
function loadTinNhan(MaCHT){
   fetch(window.BASE_URL +'loadtinnhan.php?MaCHT=' + MaCHT)
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById('messenger-messages');
            box.innerHTML = '';

            // 🔥 RESET GROUP LOGIC
            lastTimeDivider = null;
            lastSender = null;

            if (data.error){
                alert(data.error);
                return;
            }

            data.forEach(msg => {
                const msgTime = new Date(msg.ThoiGian);

                // 👉 HIỂN THỊ KHUNG GIỜ
                if (shouldShowTime(lastTimeDivider, msgTime)){
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'time-divider';
                    timeDiv.textContent = formatFBTime(msgTime);
                    box.appendChild(timeDiv);

                    lastTimeDivider = msgTime;
                    lastSender = null;
                }

                const row = document.createElement('div');
                const isMe = msg.MaTK_Gui === window.CURRENT_MATK;
                row.className = isMe ? 'msg-row me' : 'msg-row other';

                const content = document.createElement('div');
                content.className = 'msg-content';

                if (!isMe && msg.HoTen !== lastSender){
                    const name = document.createElement('div');
                    name.className = 'sender-name';
                    name.textContent = msg.HoTen;
                    content.appendChild(name);
                    lastSender = msg.HoTen;
                }

                const bubble = document.createElement('div');
                bubble.className = 'msg-bubble';
                bubble.textContent = msg.NoiDung;

                content.appendChild(bubble);
                row.appendChild(content);
                box.appendChild(row);
            });

            box.scrollTop = box.scrollHeight;
        });
}
function formatFBTime(datetime){
    const d = new Date(datetime);

    const h = String(d.getHours()).padStart(2,'0');
    const m = String(d.getMinutes()).padStart(2,'0');

    const days = ['CN','T2','T3','T4','T5','T6','T7'];
    const day = days[d.getDay()];

    return `${h}:${m} ${day}`;
}
(() => {
    const messengerFloat = document.getElementById('messenger-float');
    const messengerBox   = document.getElementById('messenger-box');
    const messengerClose = document.getElementById('messenger-close');
    const messengerInput = document.getElementById('messenger-text');

    messengerFloat.addEventListener('click', () => {
       const isOpen = messengerBox.style.display === 'flex';

    if (isOpen) {
        messengerBox.style.display = 'none';
        messengerOpened = false;
        localStorage.setItem('messenger_open', '0');
    } else {
        messengerBox.style.display = 'flex';
        messengerOpened = true;
        localStorage.setItem('messenger_open', '1');

        fetch(window.BASE_URL +'tattrangthai.php')
            .then(() => updateBadgeFromDB());

        loadTinNhan(currentMaCHT);
    }
    });
    messengerClose.addEventListener('click', () => {
        messengerBox.style.display = 'none';
        messengerOpened = false;
        localStorage.setItem('messenger_open', '0');
    });
    window.sendMessenger = function () {
    const msg = messengerInput.value.trim();
    if (!msg) return;

    fetch(window.BASE_URL+'tinnhan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            NoiDung: msg,
            MaCHT: 1
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
        alert(data.error);
        return;
    }

    messengerInput.value = '';

    // Load lại tin nhắn từ DB cho đúng khung giờ + sender
    loadTinNhan(currentMaCHT);

    updateBadgeFromDB();
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi gửi tin nhắn');
    });
};
    messengerInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessenger();
        }
    });
})();
document.addEventListener('DOMContentLoaded', () => {
    // ===== RESTORE AI =====
    if (localStorage.getItem('ai_open') === '1') {
        document.getElementById('ai-chat-box').style.display = 'flex';
    }

    // ===== RESTORE MESSENGER =====
    if (localStorage.getItem('messenger_open') === '1') {
        const messengerBox = document.getElementById('messenger-box');
        messengerBox.style.display = 'flex';
        messengerOpened = true;
        loadTinNhan(currentMaCHT);

        // 👉 nếu AI đang mở thì shift messenger
        if (localStorage.getItem('ai_open') === '1') {
            messengerBox.classList.add('shift-left');
        }
    }
     updateBadgeFromDB();
});
let intervalId = null;

function startAutoRefresh(){
    if (intervalId) return;
    intervalId = setInterval(() => {
        updateBadgeFromDB();
        if (messengerOpened) {
            loadTinNhan(currentMaCHT);
        }
    }, 3000);
}

function stopAutoRefresh(){
    clearInterval(intervalId);
    intervalId = null;
}
// Lắng nghe trạng thái tab
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
});
startAutoRefresh();
</script>
