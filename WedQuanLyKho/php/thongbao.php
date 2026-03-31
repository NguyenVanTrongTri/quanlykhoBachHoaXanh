<?php
// thongbao.php
?>

<!-- Overlay -->
<div class="tb-overlay" id="tbOverlay"></div>

<!-- Popup thông báo -->
<div class="tb-modal" id="tbModal">
    <div class="tb-header">
        <span>Thông Báo</span>
        <span class="tb-close" id="tbClose">&times;</span>
    </div>
    <div class="tb-body">
        Hiện không có thông báo nào
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellIcon  = document.getElementById('bell-icon');
    const tbModal   = document.getElementById('tbModal');
    const tbOverlay = document.getElementById('tbOverlay');
    const tbClose   = document.getElementById('tbClose');

    // Nếu trang nào đó không có header thì thoát
    if (!bellIcon || !tbModal || !tbOverlay || !tbClose) return;

    function closeTB() {
        tbModal.style.display = 'none';
        tbOverlay.style.display = 'none';
    }

    function toggleTB() {
        const isOpen = tbModal.style.display === 'block';
        if (isOpen) {
            closeTB();
        } else {
            tbModal.style.display = 'block';
            tbOverlay.style.display = 'block';
        }
    }

    // Click chuông ở header
    bellIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleTB();
    });

    // Click overlay
    tbOverlay.addEventListener('click', closeTB);

    // Click nút X
    tbClose.addEventListener('click', closeTB);
});
</script>

<style>
.tb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.3);
    z-index: 998;
}

.tb-modal {
    display: none;
    position: fixed;
    top: 60px;
    right: 40px;
    width: 300px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    z-index: 999;
    overflow: hidden;
}

.tb-header {
    background: #00923F;
    color: #fff;
    padding: 10px 12px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tb-close {
    font-size: 20px;
    cursor: pointer;
}

.tb-body {
    padding: 18px;
    font-size: 14px;
    color: #555;
    text-align: center;
}
</style>
