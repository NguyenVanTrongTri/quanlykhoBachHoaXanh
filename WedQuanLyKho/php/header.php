<?php 
include_once $path . 'php/thongbao.php'; 
include_once $path . 'KetNoi/connect.php';
include_once $path . 'thaotac.php';

$tenChucVu = $_SESSION['ChucVu'] ?? ''; 
$listQuyen = [];
$allDbPermissions = [];

// --- NÂNG CẤP 2: Gọi hàm kết nối (vì connect.php của bạn dùng function) ---
if (function_exists('connectdb')) {
    $conn = connectdb(); 
}

if (isset($conn)) {
    try {
        if ($tenChucVu !== '') {
            $stmt = $conn->prepare("SELECT ChucNang FROM phanquyen WHERE TenChucVu = ?");
            $stmt->execute([$tenChucVu]);
            $listQuyen = $stmt->fetchAll(PDO::FETCH_COLUMN); 
        }

        $stmtAll = $conn->prepare("SELECT TenChucVu, ChucNang FROM phanquyen");
        $stmtAll->execute();
        $allDbPermissions = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
    }
}
?>

<script>
    // --- NÂNG CẤP 3: Đóng gói dữ liệu vào một Object hệ thống ---
    window.AppConfig = {
        user: {
            chucVu: "<?php echo addslashes($tenChucVu); ?>",
            permissions: <?php echo json_encode($listQuyen); ?>
        },
        dbData: <?php echo json_encode($allDbPermissions); ?>,
        path: "<?php echo $path; ?>"
    };

    // Giữ nguyên các biến cũ để không làm hỏng code cũ của Trọng
    window.userChucVu = window.AppConfig.user.chucVu;
    window.userPermissions = window.AppConfig.user.permissions;
    window.allDbPermissions = window.AppConfig.dbData;

    // --- NÂNG CẤP 4: Hàm tự động ẩn menu (UI Protection) ---
    function autoHideInvalidMenu() {
        const myRole = window.userChucVu.trim();
        const allRules = window.allDbPermissions;

        // Quét tất cả thẻ có class 'menu-item' (hoặc bạn có thể đổi tên class)
        // Yêu cầu: HTML menu cần có attribute data-role="NhapKho" chẳng hạn
        document.querySelectorAll('[data-role]').forEach(element => {
            const requiredRole = element.getAttribute('data-role');
            const canAccess = allRules.some(r => 
                r.TenChucVu.trim() === myRole && r.ChucNang === requiredRole
            );

            if (!canAccess) {
                element.style.opacity = '0.5'; // Làm mờ
                element.style.pointerEvents = 'none'; // Không cho click
                // element.style.display = 'none'; // Hoặc ẩn hẳn nếu muốn
            }
        });
    }

    document.addEventListener("DOMContentLoaded", autoHideInvalidMenu);
</script>

<script src="<?php echo $path; ?>js/phanquyen.js"></script>

<link rel="stylesheet" href="<?php echo $path; ?>css/giaodien.css">
<link rel="stylesheet" href="<?php echo $path; ?>css/header.css">
<link rel="stylesheet" href="<?php echo $path; ?>css/menu.css">
<link rel="stylesheet" href="<?php echo $path; ?>css/thongbao.css">

<div class="header">
    <img src="<?php echo $path; ?>images/Logo-Bach-Hoa-Xanh-H.webp" alt="Bách Hóa Xanh">
    <div class="header-icons">
        <span id="bell-icon" style="font-size:26px; cursor:pointer;">🔔</span>
        <span onclick="window.location.href='<?php echo $path; ?>php/logout.php'" style="cursor:pointer;">↩</span>
    </div>
</div>

<script>
// Logic Reset Timer giữ nguyên nhưng bọc lại cho gọn
(function() {
    let timeout = 15 * 60 * 1000; 
    let timer;
    const logoutUrl = "<?php echo $path; ?>php/logout.php";

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(() => { window.location.href = logoutUrl; }, timeout);
    }

    ["onload", "onmousemove", "onkeypress", "onclick", "onscroll", "touchstart"].forEach(evt => {
        window.addEventListener(evt, resetTimer);
    });
})();
</script>