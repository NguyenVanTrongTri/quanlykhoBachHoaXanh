<?php 
session_start();
include '../KetNoi/connect.php';
include(__DIR__ . "/../php/time.php");
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
$avatarPath = "../uploads/avatar/$avatar";

function getChucVu($maTK) {
    global $conn; // dùng PDO connection từ connect.php

    $sql = "SELECT nv.ChucVu 
            FROM taikhoan tk
            INNER JOIN nhanvien nv ON nv.MaNV = tk.MaNV
            WHERE tk.MaTK = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$maTK]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['ChucVu'] : null;
} 
$chucVu = null;
if (isset($_SESSION['MaTK'])) {
    $chucVu = getChucVu($_SESSION['MaTK']);
} 

function getHangHoa() {
    $conn = connectdb();

    $sql = "SELECT 
                hh.STT,
                hh.MaHangHoa,
                hh.TenHangHoa,
                hh.DonViTinh,
                hh.DonGia,
                hh.MaLoaiHang,
                lh.TenLoaiHang,
                hh.XuatXu,
                hh.MaVach
            FROM hanghoa  hh inner join loaihanghoa lh on lh.MaLoaiHang=hh.MaLoaiHang
            ORDER BY STT";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if (!isset($_SESSION['Hanghoa_items'])) {
    $_SESSION['Hanghoa_items'] = [];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {

    // Kết nối DB
    $conn = connectdb();

    // Lấy dữ liệu từ form
    $maHangHoa   = $_POST['mahanghoa'] ?? '';
    $tenHangHoa  = $_POST['tenhanghoa'] ?? '';
    $donViTinh   = $_POST['donvitinh'] ?? '';
    $maLoaiHang  = $_POST['maloaihang'] ?? '';
    $xuatXu      = $_POST['xuatxu'] ?? '';
    $maVach      = $_POST['mavach'] ?? '';

    // Kiểm tra khóa chính
    if (empty($maHangHoa)) {
    $_SESSION['popup_error'] = true;
    $_SESSION['popup_message'] = "Vui lòng chọn hàng hóa!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
    }

    try {
        $sql = "
            UPDATE hanghoa SET
                TenHangHoa  = :ten,
                DonViTinh   = :donvi,
                MaLoaiHang  = :maloai,
                XuatXu      = :xuatxu,
                MaVach      = :mavach
            WHERE MaHangHoa = :mahang
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':ten'     => $tenHangHoa,
            ':donvi'   => $donViTinh,
            ':maloai'  => $maLoaiHang,
            ':xuatxu'  => $xuatXu,
            ':mavach'  => $maVach,
            ':mahang'  => $maHangHoa
        ]);

        $_SESSION['success'] = 'Cập nhật hàng hóa thành công';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Lỗi UPDATE hàng hóa: " . $e->getMessage();
    }
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

    $conn = connectdb();

    // 1. Lấy mã hàng hóa
    $maHangHoa = trim($_POST['mahanghoa'] ?? '');

    // 2. Lưu session tạm
    $_SESSION['Hanghoa_temp'] = [
        'MaHangHoa' => $maHangHoa
    ];

    // 3. Kiểm tra dữ liệu rỗng
    if (empty($maHangHoa)) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Vui lòng chọn hàng hóa cần xóa';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // 4. Kiểm tra hàng hóa có tồn tại không
    $check = $conn->prepare("SELECT 1 FROM hanghoa WHERE MaHangHoa = ?");
    $check->execute([$maHangHoa]);

    if (!$check->fetch()) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Hàng hóa không tồn tại trong hệ thống';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // 5. Thực hiện xóa
    try {
        $stmt = $conn->prepare("DELETE FROM hanghoa WHERE MaHangHoa = ?");
        $stmt->execute([$maHangHoa]);

        // 6. Xóa khỏi session chính nếu có
        if (!empty($_SESSION['Hanghoa_items'])) {
            foreach ($_SESSION['Hanghoa_items'] as $key => $item) {
                if ($item['MaHangHoa'] === $maHangHoa) {
                    unset($_SESSION['Hanghoa_items'][$key]);
                    break;
                }
            }
            // Reset index
            $_SESSION['Hanghoa_items'] = array_values($_SESSION['Hanghoa_items']);
        }

        // 7. Xóa session tạm
        unset($_SESSION['Hanghoa_temp']);

        $_SESSION['success'] = 'Đã xóa hàng hóa thành công';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Không thể xóa hàng hóa (đang được sử dụng)';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['delete_cancel'])) {
}

$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_item'])) {

    $conn = connectdb();
    $keyword = trim($_POST['keyword'] ?? '');

    if ($keyword !== '') {

        $sql = "SELECT 
                    hh.MaHangHoa,
                    hh.TenHangHoa,
                    hh.DonViTinh,
                    hh.MaLoaiHang,
                    lh.TenLoaiHang,
                    hh.XuatXu,
                    hh.MaVach
                FROM hanghoa hh
                LEFT JOIN loaihanghoa lh ON hh.MaLoaiHang = lh.MaLoaiHang
                WHERE hh.MaHangHoa LIKE :keyword
                   OR hh.TenHangHoa LIKE :keyword
                   OR hh.MaVach LIKE :keyword
                   OR lh.TenLoaiHang LIKE :keyword
                ORDER BY hh.MaHangHoa";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':keyword' => "%$keyword%"]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($searchResults)) {
            $_SESSION['popup_error'] = true;
            $_SESSION['popup_message'] = 'Không tìm thấy hàng hóa phù hợp';
        }
    }
}

?>
<?php define('ALLOW_RENDER', true);
include_once('../ThongTinTaiKhoan.php');
include_once __DIR__ . '/../Tienich.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang chủ | Bách Hóa Xanh</title>
<style>

/* Dùng cho tim kiém*/
    .search-area {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
    background-color: #e5e5e5;
}

.search-input {
    width: 250px;
    height: 30px;
    border-radius: 6px;
    background-color: white;
    border: 1px solid #ccc;
    margin-right: 10px;
}

.btn {
    padding: 8px 15px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    border-radius: 4px;
}

.btn-search {
    border-radius: 6px;
    background-color: #e74c3c; /* Màu đỏ */
    color: white;
}
    /* Hiệu ứng chọn dòng*/
.active-row {
    background: #d1eaff !important;
    transition: 0.2s;
}
.row-select:hover {
    background: #f0f8ff;
    cursor: pointer;
}
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e5e5e5;
    }
    .header-icons span:hover {
        opacity: 0.7;
    }
    .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
    }

    .popup-box {
        background: #fff;
        padding: 20px 25px;
        border-radius: 10px;
        width: 350px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        animation: fadeIn 0.25s ease-out;
    }

    .popup-btn {
        margin-top: 15px;
        padding: 8px 20px;
        background: #28a745;
        border: none;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
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
    .user-image {
        width: 100%;
        height: 100%;
        object-fit: cover;     /* căn chỉnh ảnh cho đẹp */
    }
    /* FORM CONTENT STYLES */
    .form-container {
        padding: 20px;
    }

    .form-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }

    .section-box {
        background: #f0f0f0; /* Màu nền xám nhạt cho từng phần */
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .form-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 30px; /* Khoảng cách giữa các cặp label/input */
        margin-bottom: 10px;
    }

    .form-field {
        display: flex;
        align-items: center;
        width: calc(50% - 15px); /* Chia 2 cột, trừ khoảng gap */
    }
    .form-field label {
        width: 150px; /* Chiều rộng cố định cho label */
        font-weight: 50;
        color: #555;
        flex-shrink: 0;
    }
    .form-field input[type="text"] {
        flex-grow: 1;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }
      .form-field input,
    .form-field select {
        flex: 1;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .form-field button {
        padding: 8px 15px;
        border: none;
        background: #00923F;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
        transition: 0.2s;
    }

    .form-field button:hover {
        background: #007b36;
    }

    /* TABLE STYLES (FOOTER TABLE) */
    .data-table-container {
        margin-top: 20px;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .data-table th, .data-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    .data-table th {
        background-color: #00923F;
        color: white;
        font-weight: bold;
    }

    .data-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .data-table tr:hover {
        background-color: #f1f1f1;
    }

    /* BUTTONS */
    .action-buttons {
        display: flex;
        justify-content: flex-end; /* Căn nút sang phải */
        gap: 15px;
        padding: 15px 0;
    }

    .action-buttons .btn {
        padding: 10px 25px;
        font-size: 10px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    .action-buttons .btn.add {
        background: #e60000; /* Màu đỏ */
        color: white;
    }

    .action-buttons .btn.delete {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }

    .action-buttons .btn.add:hover {
        background: #cc0000;
    }

    .action-buttons .btn.delete:hover {
        background: #007b36;
    }
    /* FOOTER SUMMARY & ACTIONS */
    .summary-and-actions {
        padding: 20px 0;
    }

    .summary-info {
        display: flex;
        justify-content: flex-start;
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-field {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        display: flex;
        align-items: center;
    }

    .summary-field input {
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        font-weight: bold;
        margin-left: 10px;
        width: 200px; /* Chiều rộng cho trường tổng tiền */
        text-align: right;
        background: #fff;
    }

    .final-actions {
        display: flex;
        justify-content: flex-end; /* Đẩy các nút sang phải */
        gap: 15px;
    }

    .final-actions .btn {
        padding: 12px 30px;
        font-size: 10px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    .final-actions .btn.save {
        background: #e60000; /* Màu đỏ */
        color: white;
    }

    .final-actions .btn.cancel {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }

    .final-actions .btn.save:hover {
        background: #cc0000;
    }

    .final-actions .btn.cancel:hover {
        background: #007b36;
    }
</style>
</head>
<body>
<?php
$path = "../"; 
include(__DIR__ . "/../php/header.php");
?>
<!-- chọn mục hiển thị thông tin tài khoản -->
<script>  
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.username').addEventListener('click', function() {
        // Load modal từ file ModalUser.php
        fetch('../ThongTinTaiKhoan.php')
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


        <button class="overview-btn" onclick="window.location.href='../TrangChu.php'">Tổng quan</button>

       <script>
        // Truyền biến chức vụ sang JS
        window.userChucVu = '<?php echo $chucVu; ?>';
        </script>
        <script src="../php/phanquyen.js?v=<?= time() ?>"></script> <!-- đường dẫn tới file JS dùng chung -->
        <!-- NHẬP KHO -->
       <div class="menu-item parent">📥 Nhập kho</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/TaoPhieuNhapKho.php')">> Tạo phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/CapNhatPhieuNhapKho.php')">> Cập nhật phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/ThongKePhieuNhapKho.php')">> Thống kê phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/KeHoachNhapHang.php')">> Kế hoạch nhập hàng</div>
        <!-- XUẤT KHO -->
        <div class="menu-item parent">📤 Xuất kho</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/TaoPhieuXuat.php')">> Tạo phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/CapNhatPhieuXuatKho.php')">> Cập nhật phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/ThongKePhieuXuatKho.php')">> Thống kê phiếu xuất</div>

        <!-- TỒN KHO -->
        <div class="menu-item parent">📦 Tồn kho</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/ThongKeHangTon.php')">> Thống kê hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/DieuChinhHangTon.php')">> Điều chỉnh hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/CanhBaoHangTon.php')">> Cảnh báo hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/QuanLyKeHang.php')">> Quản lý kệ hàng</div>

        <!-- HÀNG HÓA -->
        <div class="menu-item parent">📦 Hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('../HangHoa/ThemHangHoa.php')">> Thêm hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('../HangHoa/CapNhatHangHoa.php')">> Cập nhật hàng hóa</div>
        
        <!-- NHÂN VIÊN -->
        <div class="menu-item parent">👥 Nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../NhanVien/QuanLyNhanVien.php')">> Quản lý nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../TaiKhoan/QuanLyTaiKhoan.php')">> Quản lý tài khoản</div>
        
    </div>
    <div class="content">
        <form method="post" id="searchForm">
                <header class="search-area">
                <input type="text" id="keyword" name="keyword" class="search-input"placeholder="Nhập ký tự cần tìm">
                <button class="btn btn-search" name="search_item" >Tìm kiếm</button>
                </header>
         </form>
        <div class="form-container">
        <form method="post" id="formHangHoa">
            <div class="section-box">
                <div class="section-title">Thông tin hàng hóa</div>
                    <div class="form-group">
                        <div class="form-field">
                            <label for="mahanghoa">Mã hàng hóa</label>
                            <input type="text" id="mahanghoa" name="mahanghoa" >
                        </div>

                        <div class="form-field">
                        <label for="maloaihang">Mã loại hàng hóa</label>
                        <select id="maloaihang" name="maloaihang" autocomplete="off" class="form-select">
                            <option value="" selected>-- Chọn mã loại hàng hóa --</option>
                            <option value="LHH01">LHH01 - Thịt</option>
                            <option value="LHH02">LHH02 - Cá</option>
                            <option value="LHH03">LHH03 - Trứng</option>
                            <option value="LHH04">LHH04 - Hải sản</option>
                            <option value="LHH05">LHH05 - Rau</option>
                            <option value="LHH06">LHH06 - Củ</option>
                            <option value="LHH07">LHH07 - Nấm</option>
                            <option value="LHH08">LHH08 - Trái cây</option>
                            <option value="LHH09">LHH09 - Bia</option>
                            <option value="LHH10">LHH10 - Nước giải khát</option>
                            <option value="LHH11">LHH11 - Sữa</option>
                            <option value="LHH12">LHH12 - Gạo</option>
                            <option value="LHH13">LHH13 - Bột</option>
                            <option value="LHH14">LHH14 - Đồ khô</option>
                            <option value="LHH15">LHH15 - Dầu ăn</option>
                            <option value="LHH16">LHH16 - Nước chấm</option>
                            <option value="LHH17">LHH17 - Gia vị</option>
                            <option value="LHH18">LHH18 - Mì</option>
                            <option value="LHH19">LHH19 - Miến</option>
                            <option value="LHH20">LHH20 - Cháo</option>
                            <option value="LHH21">LHH21 - Phở</option>
                            <option value="LHH22">LHH22 - Kem</option>
                            <option value="LHH23">LHH23 - Sữa chua</option>
                            <option value="LHH24">LHH24 - Thực phẩm đông mát</option>
                            <option value="LHH25">LHH25 - Bánh kẹo</option>
                            <option value="LHH26">LHH26 - Chăm sóc cá nhân</option>
                            <option value="LHH27">LHH27 - Sản phẩm cho mẹ và bé</option>
                            <option value="LHH28">LHH28 - Đồ dùng gia đình</option>
                        </select>
                        </div>
                         <div class="form-field">
                            <label for="tenhanghoa">Tên hàng hóa</label>
                            <input type="text" id="tenhanghoa" name="tenhanghoa" placeholder="">
                        </div>
                        <div class="form-field">
                            <label for="tenloaihanghoa">Tên loại hàng hóa</label>
                            <input type="text" id="tenloaihanghoa" name="tenloaihanghoa"   readonly>
                            <script>
                                    document.getElementById("maloaihang").addEventListener("change", function () {
                                        let ma = this.value.trim();

                                        if (ma === "") {
                                            document.getElementById("tenloaihanghoa").value = "";
                                            return;
                                        }
                                        fetch("../php/getLoaiHang.php?maloaihang=" + encodeURIComponent(ma))
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.status === "success") {
                                                    document.getElementById("tenloaihanghoa").value = data.data.TenLoaiHang;
                                                } else {
                                                    showPopup("Không tìm thấy mã loại hàng!");
                                                    document.getElementById("tenloaihanghoa").value = "";
                                                }
                                            })
                                            .catch(() => showPopup("Lỗi kết nối server"));
                                    });
                            </script>
                        </div>
                        <div class="form-field">
                        <label for="donvitinh">Đơn vị tính</label>
                        <select id="donvitinh" name="donvitinh" class="form-select">
                            <option value="" selected>-- Chọn đơn vị --</option>
                            <option value="Kg">Kg</option>
                            <option value="Thùng">Thùng</option>
                            <option value="Hộp">Hộp</option>
                        </select>
                        </div>
                        <div class="form-field">
                            <label for="xuatxu">Xuất xứ</label>
                            <input type="text" id="xuatxu"  name="xuatxu"placeholder="">
                        </div>
                        <script>
                            //hàm thông báo
                            function closePopup() {
                                const popup = document.getElementById("popup");
                                if (popup) {
                                    popup.style.display = "none";
                                }
                            }
                            // Hàm hiện thông báo
                            function showPopup(msg) {
                                document.getElementById("popup-message").innerText = msg;
                                document.getElementById("popup").style.display = "flex";
                            }
                            // Lắng nghe sự kiện tư form
                            document.getElementById("formHangHoa").onsubmit = function(e) { 
                               const ngaySX = document.getElementById("ngaysanxuat").value;
                                const hanSD  = document.getElementById("hansudung").value;
                                // Chỉ kiểm tra khi cả 2 đều có giá trị
                                return true;
                            };
                        </script>
                        
                        <div id="popup" class="popup-overlay" style="display:none;">
                            <div class="popup-box">
                                <h3>Thông báo !</h3>
                                <p id="popup-message"></p>
                                <button type="button" onclick="closePopup()" class="popup-btn">OK</button>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="mavach">Mã vạch</label>
                            <input type="text" id="mavach"  name="mavach"placeholder="">
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" name="edit_item" class="btn add">Sửa</button>
                        <button type="submit" name="delete_item" class="btn delete">Xóa</button>
                        <button type="reset" class="btn delete">Hủy bỏ</button>
                    </div>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã hàng hóa</th>
                            <th>Tên hàng hóa</th>
                            <th>Đơn vị tính</th>
                            <th>Loại hàng hóa</th>
                            <th>Tên loại hàng hóa</th>
                            <th>Xuất xứ</th>
                            <th>Mã vạch</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        <?php
                            $data = !empty($searchResults) ? $searchResults : getHangHoa();
                            $stt = 1;
                            foreach ($data as $index => $row) {
                                    echo "<tr class='row-select'
                                    data-mahanghoa='".$row['MaHangHoa']."'
                                    data-tenhanghoa='".$row['TenHangHoa']."'
                                    data-donvitinh='".$row['DonViTinh']."'
                                    data-maloaihang='".$row['MaLoaiHang']."'
                                    data-tenloaihang='".$row['TenLoaiHang']."'
                                    data-xuatxu='".$row['XuatXu']."'
                                    data-mavach='".$row['MaVach']."'
                                >";
                                echo "<td>".$stt++."</td>";
                                echo "<td>".$row['MaHangHoa']."</td>";
                                echo "<td>".$row['TenHangHoa']."</td>";
                                echo "<td>".$row['DonViTinh']."</td>";
                                echo "<td>".$row['MaLoaiHang']."</td>";
                                echo "<td>".$row['TenLoaiHang']."</td>";
                                echo "<td>".$row['XuatXu']."</td>";
                                echo "<td>".$row['MaVach']."</td>";
                                echo "</tr>";
                            }
                            ?>  
                        </tbody>
                        <script>
                            document.querySelectorAll(".row-select").forEach(function(row) {
                                row.addEventListener("click", function() {
                                    // lấy dữ liệu từ data-*
                                    document.getElementById("mahanghoa").value = this.dataset.mahanghoa;
                                    document.getElementById("tenhanghoa").value = this.dataset.tenhanghoa;
                                    document.getElementById("donvitinh").value = this.dataset.donvitinh;

                                    const tenLoaiHang = this.dataset.tenloaihang; // vd: "Thịt"
                                    const selectLoai  = document.getElementById("maloaihang");

                                    // 🔍 Tìm option có chứa tên loại hàng
                                    for (let option of selectLoai.options) {
                                        if (option.text.includes(tenLoaiHang)) {
                                            selectLoai.value = option.value;
                                            break;
                                        }
                                    }

                                    // 🔥 trigger change SAU khi đã set đúng option
                                    selectLoai.dispatchEvent(new Event("change"));
                                    document.getElementById("xuatxu").value = this.dataset.xuatxu;
                                    document.getElementById("mavach").value = this.dataset.mavach;

                                 // Hiệu ứng chọn dòng
                                document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
                                this.classList.add("active-row");
                                });
                            });
                        </script>
                        <script>
                        document.getElementById("maloaihang").addEventListener("change", function () {

                            if (!this.value) {
                                document.getElementById("tenloaihang").value = "";
                                return;
                            }
                            const selectedText = this.options[this.selectedIndex].text;
                            const tenLoaiHang = selectedText.split(" - ")[1] ?? "";
                            document.getElementById("tenloaihang").value = tenLoaiHang;
                        });
                        </script>
                </table>
            </div>
        </form> 
        </div>
    </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if (!empty($_SESSION['popup_error'])): ?>
            showPopup("<?= addslashes($_SESSION['popup_message']) ?>");
            <?php
                unset($_SESSION['popup_error']);
                unset($_SESSION['popup_message']);
            ?>
        <?php endif; ?>
    });
</script>
</body>
















