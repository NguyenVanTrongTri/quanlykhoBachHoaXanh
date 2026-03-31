<?php
session_start();
include 'KetNoi/connect.php';
$conn = connectdb();

$defaultAvatar = 'default.png'; // chỉ tên file mặc định trong uploads/avatar
$avatar = $defaultAvatar;       // mặc định
$tenNguoiDung = 'Khách';
$chucVu = null;
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
        $tenNguoiDung = $row['TenDangNhap'] ?: 'Khách';
        // nếu Avatar trống → dùng default.png
        $avatar = !empty($row['Avatar']) ? $row['Avatar'] : $defaultAvatar;
        $chucVu = $row['ChucVu'];
    }
}

// đường dẫn đầy đủ cho HTML
$avatarPath = "uploads/avatar/$avatar";


// Tổng số lượng nhập kho
$sqlNhap = "SELECT SUM(SoLuongThucTeNhap) AS tongNhap FROM chitietphieunhap";
$stmtNhap = $conn->query($sqlNhap);
$rowNhap = $stmtNhap->fetch(PDO::FETCH_ASSOC);
$tongNhap = $rowNhap['tongNhap'] ?? 0;

// Tổng số lượng xuất kho
$sqlXuat = "SELECT SUM(SoLuongThucTeXuat) AS tongXuat FROM chitietphieuxuat";
$stmtXuat = $conn->query($sqlXuat);
$rowXuat = $stmtXuat->fetch(PDO::FETCH_ASSOC);
$tongXuat = $rowXuat['tongXuat'] ?? 0;

$sqlKho = "SELECT SUM(TongSucChua) AS tongSucChua FROM kehanghoa";
$stmtKho = $conn->query($sqlKho);
$rowKho = $stmtKho->fetch(PDO::FETCH_ASSOC);
$tongSucChua = $rowKho['tongSucChua'] ?? 0;

// Tổng số đã chứa trong kho
$sqlDaChua = "SELECT SUM(DaChua) AS daChuaTong FROM kehanghoa";
$stmtDaChua = $conn->query($sqlDaChua);
$rowDaChua = $stmtDaChua->fetch(PDO::FETCH_ASSOC);
$daChuaKho = $rowDaChua['daChuaTong'] ?? 0;
// Tính số đã chứa và còn trống
$conTrong = max(0, $tongSucChua - $daChuaKho);
?>
<?php define('ALLOW_RENDER', true);
include_once('ThongTinTaiKhoan.php');
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<?php ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang chủ | Bách Hóa Xanh</title>

<style>

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e5e5e5;
    }
    .header-icons span:hover {
        opacity: 0.7;
    }
    .password-wrapper {
    position: relative;
    }

    .password-wrapper i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        cursor: pointer;
        color: #555;
    }

    /* LAYOUT */
    .layout {
        display: flex;
        width: 100%;
        height: calc(100vh - 48px);
        overflow: hidden;
    }

    /* SIDEBAR */
    .sidebar {
        width: 260px;
        background: #ffffff;
        padding: 20px;
        border-right: 1px solid #ccc;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .user-icon {
        font-size: 34px;
        color: #00923F;
    }

    .username {
        font-size: 15px;
        font-weight: bold;
        color: #333;
    }

    .overview-btn {
        width: 100%;
        background: #00923F;
        color: white;
        padding: 9px 15px;
        border-radius: 20px;
        border: none;
        font-size: 15px;
        cursor: pointer;
        margin-bottom: 15px;
        transition: 0.2s;
    }

    .overview-btn:hover {
        background: #007b36;
        transform: scale(1.02);
    }

    /* MENU */
    .menu-item {
    padding-left: 25px;
    margin-bottom: 5px;
    font-size: 14px;
    cursor: pointer;
    color: #444;
    user-select: none;
    padding-top: 3px;
    padding-bottom: 3px;
    border-radius: 5px;
    transition: 0.2s;
    }

    .menu-item:hover {
        color: white;
        background: #008947;
    }

    .menu-item.parent {
        padding-left: 8px !important;
        font-weight: bold;
        color: #00923F;
        font-size: 15px;
    }

    .menu-item.parent:hover {
        background: #008947;
        color: white;
    }

    /* CONTENT */
    .content {
        flex: 1;
        background: #e5e5e5;
        padding: 20px;
        overflow-y: auto;
    }
     /* AVARTA */
    .user-image-wrapper {
    width: 42px;
    height: 42px;
    border-radius: 50%;    /* avatar tròn - có thể đổi */
    overflow: hidden;      /* cắt phần thừa */
    flex-shrink: 0;        /* không bị co lại khi thu sidebar */
    }
    .user-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    }
:root{
    --bhx-green:#0a8f3c;
    --bhx-dark-green:#086b2e;
    --bhx-yellow:#ffbc00;
    --chart-green:#1e8f3e;
    --bg:#f1f8e9;
}
/* ================= CONTENT BODY ================= */
.body-content{
    padding:30px;
    overflow:auto;
}

.body-video-placeholder{
    background: #e7ffb5;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 25px;
    overflow: hidden; /* tránh ảnh tràn ra ngoài */
    position: relative;
}

.body-video-placeholder img {
     width: 100%;
    height: 100%;
    object-fit: cover; /* ảnh lấp đầy div, có thể cắt bớt */
    transition: transform 0.3s ease;
    cursor: zoom-in;
}


/* ================= DATA CARD ================= */
.body-data-card{
    background:#fff;
    border:1px solid #aaa;
    border-radius:12px;
    padding:20px;
    margin-bottom:25px;
    display:flex;
}

/* ================= CHART ================= */
.body-chart-section{
    display:flex;
    align-items:center;
    gap:25px;
    width:50%;
}

.body-chart-wrapper{
    width:200px;
    height:200px;
    position:relative;
}

.body-chart-title{
    position:absolute;
    font-size:14px;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%);
    font-weight:bold;
}

.body-chart-legend{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.body-legend-item{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
}

.body-color-box{
    width:20px;
    height:20px;
    border-radius:4px;
}

/* ================= FORM ================= */
.body-form-container{
    width:50%;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:20px;
    padding-left:30px;
}

.body-form-group{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.body-form-group label{
    font-size:14px;
}

.body-form-group input{
    width:65%;
    height:34px;
    border:1px solid #999;
    padding:5px;
}

</style>
</head>
<body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.username').addEventListener('click', function() {
        // Load modal từ file ModalUser.php
        fetch('ThongTinTaiKhoan.php')
            .then(response => response.text())
            .then(html => {
                // Chèn modal vào cuối body
                document.body.insertAdjacentHTML('beforeend', html);

                // Hiển thị modal
                document.getElementById('modalOverlay').style.display = 'block';
                document.getElementById('userModal').style.display = 'block';
            });
    });
    
    });
</script>

<?php
$path = ""; 
include(__DIR__ . "/php/header.php");
include 'Tienich.php';
?>
<div class="layout">
    <div class="sidebar">
        <div class="user-box">
            <div class="user-image-wrapper">
                <img src="<?= $avatarPath ?>" class="user-image" alt="User Avatar">
            </div>
            <div class="username">
                <?php echo htmlspecialchars($tenNguoiDung); ?>
            </div>
        </div>

        <button class="overview-btn" >Tổng quan</button>

        <script>
        // Truyền biến chức vụ sang JS
        window.userChucVu = '<?php echo $chucVu; ?>';
        </script>
       <script src="php/phanquyen.js?v=<?= time() ?>"></script> <!-- đường dẫn tới file JS dùng chung -->
        <!-- NHẬP KHO -->
       <div class="menu-item parent">📥 Nhập kho</div>
        <div class="menu-item" onclick="checkPermission('NhapKho/TaoPhieuNhapKho.php')">> Tạo phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('NhapKho/CapNhatPhieuNhapKho.php')">> Cập nhật phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('NhapKho/ThongKePhieuNhapKho.php')">> Thống kê phiếu nhập</div>

        <!-- XUẤT KHO -->
        <div class="menu-item parent">📤 Xuất kho</div>
        <div class="menu-item" onclick="checkPermission('XuatKho/TaoPhieuXuat.php')">> Tạo phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('XuatKho/CapNhatPhieuXuatKho.php')">> Cập nhật phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('XuatKho/ThongKePhieuXuatKho.php')">> Thống kê phiếu xuất</div>

        <!-- TỒN KHO -->
        <div class="menu-item parent">📦 Tồn kho</div>
        <div class="menu-item" onclick="checkPermission('TonKho/ThongKeHangTon.php')">> Thống kê hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('TonKho/DieuChinhHangTon.php')">> Điều chỉnh hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('TonKho/CanhBaoHangTon.php')">> Cảnh báo hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('TonKho/QuanLyKeHang.php')">> Quản lý kệ hàng</div>
    
        <!-- HÀNG HÓA -->
        <div class="menu-item parent">📦 Hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('HangHoa/ThemHangHoa.php')">> Thêm hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('HangHoa/CapNhatHangHoa.php')">> Cập nhật hàng hóa</div>
        
        <!-- NHÂN VIÊN -->
        <div class="menu-item parent">👥 Nhân viên</div>
        <div class="menu-item" onclick="checkPermission('NhanVien/QuanLyNhanVien.php')">> Quản lý nhân viên</div>
        <div class="menu-item" onclick="checkPermission('TaiKhoan/QuanLyTaiKhoan.php')">> Quản lý tài khoản</div>
    
    </div>
    <!-- CONTENT -->
    <div class="content">
    <div class="body-content">

        <!-- VIDEO PLACEHOLDER -->
        <div class="body-video-placeholder">
            <img src="images/quanly.jpg">
        </div>

        <!-- ================= CARD 1 ================= -->
        <div class="body-data-card">

            <!-- CHART -->
            <div class="body-chart-section">
                <div class="body-chart-wrapper">
                    <canvas id="chart1"></canvas>
                </div>

                <div class="body-chart-legend">
                    <div class="body-legend-item">
                        <div class="body-color-box" style="background:var(--bhx-yellow)"></div>
                        Xuất kho
                    </div>
                    <div class="body-legend-item">
                        <div class="body-color-box" style="background:var(--chart-green)"></div>
                        Nhập kho
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="body-form-container">
                <div class="body-form-group">
                        <label>Số lượng nhập kho</label>
                        <input type="text" value="<?php echo $tongNhap; ?>">
                    </div>
                    <div class="body-form-group">
                        <label>Số lượng xuất kho</label>
                        <input type="text" value="<?php echo $tongXuat; ?>">
                    </div>
            </div>

        </div>

        <!-- ================= CARD 2 ================= -->
        <div class="body-data-card">

            <!-- CHART -->
            <div class="body-chart-section">
                <div class="body-chart-wrapper">
                    <canvas id="chart2"></canvas>
                </div>

                <div class="body-chart-legend">
                    <div class="body-legend-item">
                        <div class="body-color-box" style="background:var(--bhx-yellow)"></div>
                        Đã chứa
                    </div>
                    <div class="body-legend-item">
                        <div class="body-color-box" style="background:var(--chart-green)"></div>
                        Còn trống
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="body-form-container">
                 <div class="body-form-group">
                        <label>Tổng sức chứa</label>
                        <input type="text" value="<?php echo $tongSucChua; ?>">
                    </div>
                    <div class="body-form-group">
                        <label>Đã chứa</label>
                        <input type="text" value="<?php echo $daChuaKho; ?>">
                    </div>
                    <div class="body-form-group">
                        <label>Còn trống</label>
                        <input type="text" value="<?php echo $conTrong; ?>">
                    </div>
            </div>

        </div>

    </div>
</div>

</div>
<!-- CHART -->
<script>
// Ép kiểu số để Chart.js không bị NaN
const tongNhap = Number(<?php echo $tongNhap ?? 0; ?>);
const tongXuat = Number(<?php echo $tongXuat ?? 0; ?>);
const daChuakho = Number(<?php echo $daChuaKho ?? 0; ?>);
const conTrong = Number(<?php echo $conTrong ?? 0; ?>);

const donut = (id, a, b) => new Chart(document.getElementById(id), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [a, b],
            backgroundColor: ['#ffbc00', '#1e8f3e'],
            borderWidth: 0
        }]
    },
    options: {
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});

// Gán dữ liệu vào biểu đồ
// Chart Nhập/Xuất
donut('chart1', tongXuat, tongNhap);

// Chart Đã chứa/Còn trống
donut('chart2', daChuakho, conTrong);
</script>

</body>
</html> 