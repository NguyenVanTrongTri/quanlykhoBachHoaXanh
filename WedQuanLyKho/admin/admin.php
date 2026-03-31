<?php
session_start();
// Kết nối Database
include '../KetNoi/connect.php'; 
$conn = connectdb();

// 1. Lấy thông tin người dùng đang đăng nhập
$defaultAvatar = 'default.png';
$avatar = $defaultAvatar;
$tenNguoiDung = 'Admin';
$chucVuHienTai = 'Quản trị';

if (isset($_SESSION['MaTK'])) {
    $sql = "SELECT tk.TenDangNhap, tk.Avatar, nv.ChucVu
            FROM taikhoan tk
            LEFT JOIN nhanvien nv ON nv.MaNV = tk.MaNV
            WHERE tk.MaTK = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['MaTK']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $tenNguoiDung = $row['TenDangNhap'];
        $avatar = !empty($row['Avatar']) ? $row['Avatar'] : $defaultAvatar;
        $chucVuHienTai = !empty($row['ChucVu']) ? $row['ChucVu'] : 'Quản trị';
    }
}

// Đường dẫn avatar (Cần khớp với cấu trúc thư mục của bạn)
$avatarPath = "../uploads/avatar/$avatar";

// 2. Thống kê dữ liệu
$tongNhap = $conn->query("SELECT SUM(SoLuongThucTeNhap) FROM chitietphieunhap")->fetchColumn() ?: 0;
$tongXuat = $conn->query("SELECT SUM(SoLuongThucTeXuat) FROM chitietphieuxuat")->fetchColumn() ?: 0;
$tongSucChua = $conn->query("SELECT SUM(TongSucChua) FROM kehanghoa")->fetchColumn() ?: 0;
$daChuaKho = $conn->query("SELECT SUM(DaChua) FROM kehanghoa")->fetchColumn() ?: 0;
$conTrong = max(0, $tongSucChua - $daChuaKho);
$tongNhanVien = $conn->query("SELECT COUNT(*) FROM nhanvien")->fetchColumn() ?: 0;
$tongHangHoa = $conn->query("SELECT COUNT(*) FROM hanghoa")->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị | Bách Hóa Xanh</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --bhx-green: #00923F;
            --bhx-yellow: #ffbc00;
            --bg-light: #f4f7f6;
            --sidebar-width: 280px;
        }

        /* RESET BOX MODEL - Đây là chìa khóa để khớp kích thước */
        * { 
            box-sizing: border-box; 
        }

        body { 
            margin: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-light); 
            overflow: hidden; 
        }

        .admin-container { 
            display: flex; 
            height: 100vh; 
            width: 100vw;
        }
        
        /* SIDEBAR - Cố định kích thước giống permission.php */
        .admin-sidebar { 
            width: var(--sidebar-width); 
            min-width: var(--sidebar-width);
            background: #fff; 
            border-right: 1px solid #ddd; 
            padding: 20px; 
            display: flex; 
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
        }
        
        .admin-brand {
            font-size: 20px; 
            font-weight: bold; 
            color: var(--bhx-green);
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid var(--bhx-yellow);
            padding-bottom: 10px;
        }

        .user-profile {
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 15px;
            background: #f9f9f9; 
            border-radius: 10px; 
            margin-bottom: 25px;
        }

        .user-avatar { 
            width: 45px; 
            height: 45px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid var(--bhx-green); 
        }

        .admin-menu-item {
            display: flex; 
            align-items: center; 
            gap: 10px; 
            padding: 12px 15px;
            text-decoration: none; 
            color: #555; 
            border-radius: 8px;
            margin-bottom: 5px; 
            font-size: 14px;
            transition: 0.3s;
        }

        .admin-menu-item:hover, .admin-menu-item.active { 
            background: var(--bhx-green); 
            color: #fff !important; 
        }

        .admin-menu-item i { width: 20px; text-align: center; }

        /* NỘI DUNG CHÍNH */
        .admin-main { 
            flex: 1; 
            overflow-y: auto; 
            padding: 30px; 
        }

        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 20px; 
            margin-bottom: 30px; 
        }

        .stat-card {
            background: #fff; 
            padding: 20px; 
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            border-left: 5px solid var(--bhx-green);
        }

        .stat-card h3 { margin: 0; font-size: 12px; color: #888; text-transform: uppercase; font-weight: bold; }
        .stat-card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: var(--bhx-green); }

        .charts-container { display: flex; gap: 20px; }
        .chart-box {
            background: #fff; 
            padding: 25px; 
            border-radius: 12px; 
            flex: 1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            text-align: center;
        }
        .canvas-wrapper { width: 220px; margin: 0 auto; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="admin-brand">BHX - QUẢN TRỊ</div>
        
        <div class="user-profile">
            <img src="<?= $avatarPath ?>" class="user-avatar" alt="Avatar">
            <div>
                <div style="font-weight:bold; font-size:14px;"><?= htmlspecialchars($tenNguoiDung) ?></div>
                <div style="font-size:11px; color: var(--bhx-green);">
                <?= !empty($me['ChucVu']) ? htmlspecialchars($me['ChucVu']) : 'Administrator' ?>
            </div>
            </div>
        </div>

        <div style="flex: 1;">
            <a href="admin.php" class="admin-menu-item active">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="users/account.php" class="admin-menu-item">
                <i class="fa-solid fa-users-gear"></i> Tài khoản
            </a>
            <a href="permission/permission.php" class="admin-menu-item">
                <i class="fa-solid fa-user-shield"></i> Phân quyền
            </a>
            
            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
            
            <a href="../TonKho/ThongKeHangTon.php" class="admin-menu-item">
                <i class="fa-solid fa-box-open"></i> Tồn kho
            </a>
            <a href="NhatKyHeThong.php" class="admin-menu-item">
                <i class="fa-solid fa-clipboard-list"></i> Nhật ký
            </a>
        </div>

        <a href="../../Login.php" class="admin-menu-item" style="color:#e74c3c;">
            <i class="fa-solid fa-power-off"></i> Đăng xuất
        </a>
    </div>

    <div class="admin-main">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h2 style="margin:0; color: #333;">Bảng điều khiển quản trị</h2>
            <div style="font-size:14px; color:#666; font-weight: 500;">
                <i class="fa-regular fa-calendar-days"></i> <?php echo date('d/m/Y - H:i'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3>Nhân viên</h3><p><?= number_format($tongNhanVien) ?></p></div>
            <div class="stat-card" style="border-left-color: var(--bhx-yellow);"><h3>Mặt hàng</h3><p><?= number_format($tongHangHoa) ?></p></div>
            <div class="stat-card"><h3>Tổng nhập</h3><p><?= number_format($tongNhap) ?></p></div>
            <div class="stat-card" style="border-left-color: #e67e22;"><h3>Sử dụng kho</h3><p><?= $tongSucChua > 0 ? round(($daChuaKho/$tongSucChua)*100, 1) : 0 ?>%</p></div>
        </div>

        <div class="charts-container">
            <div class="chart-box">
                <h4 style="color:#555; margin-bottom: 20px;">Tỷ lệ Nhập / Xuất</h4>
                <div class="canvas-wrapper"><canvas id="adminChart1"></canvas></div>
            </div>
            <div class="chart-box">
                <h4 style="color:#555; margin-bottom: 20px;">Trạng thái kệ hàng</h4>
                <div class="canvas-wrapper"><canvas id="adminChart2"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
// Vẽ biểu đồ
const setupChart = (id, data, labels, colors) => {
    new Chart(document.getElementById(id), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: { 
            cutout: '75%', 
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { boxWidth: 12, padding: 20, font: { size: 12 } } 
                } 
            } 
        }
    });
};

setupChart('adminChart1', [<?= $tongXuat ?>, <?= $tongNhap ?>], ['Xuất kho', 'Nhập kho'], ['#ffbc00', '#00923F']);
setupChart('adminChart2', [<?= $daChuaKho ?>, <?= $conTrong ?>], ['Đã chứa', 'Còn trống'], ['#ffbc00', '#00923F']);
</script>

</body>
</html>