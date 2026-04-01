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

function getNhanVien() {
    $conn = connectdb();

    $sql = "SELECT
                nv.STT,
                nv.MaNV,
                nv.HoTen,
                nv.NgaySinh,
                nv.GioiTinh,
                nv.DiaChi,
                nv.SDT,
                nv.Email,
                nv.MaLoaiNV,
                lnv.TenLoaiNV,
                nv.ChucVu,
                nv.TrangThai,
                nv.GhiChu
            FROM nhanvien nv
            INNER JOIN loainhanvien lnv 
                ON lnv.MaLoaiNV = nv.MaLoaiNV
            ORDER BY nv.STT";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getDanhSachChucVu() {
    $conn = connectdb(); // Đảm bảo gọi hàm kết nối DB bên trong hoặc dùng global $conn
    try {
        $sql = "SELECT DISTINCT TenChucVu FROM phanquyen ORDER BY TenChucVu ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return []; // Trả về mảng rỗng nếu lỗi
    }
}
$dsChucVu = getDanhSachChucVu();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
     // Kết nối DB
    $conn = connectdb();

    // Lấy dữ liệu từ form
    $maNV      = $_POST['manhanvien'] ?? '';
    $hoTen     = $_POST['hoten'] ?? '';
    $ngaySinh  = $_POST['ngaysinh'] ?? null;
    $gioiTinh  = $_POST['gioitinh'] ?? '';
    $diaChi    = $_POST['diachi'] ?? '';
    $email     = $_POST['mail'] ?? '';
    $sdt       = $_POST['sodienthoai'] ?? '';
    $chucVu    = $_POST['chucvu'] ?? '';
    $trangThai = $_POST['trangthai'] ?? '';
    $ghiChu    = $_POST['ghichu'] ?? '';

    // Kiểm tra khóa chính
    if (empty($maNV)) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng chọn nhân viên!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        $sql = "
            UPDATE nhanvien SET
                HoTen      = :hoten,
                NgaySinh   = :ngaysinh,
                GioiTinh   = :gioitinh,
                DiaChi     = :diachi,
                Email      = :email,
                SDT        = :sdt,
                ChucVu     = :chucvu,
                TrangThai  = :trangthai,
                GhiChu     = :ghichu
            WHERE MaNV = :manv
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':hoten'     => $hoTen,
            ':ngaysinh'  => $ngaySinh,
            ':gioitinh'  => $gioiTinh,
            ':diachi'    => $diaChi,
            ':email'     => $email,
            ':sdt'       => $sdt,
            ':chucvu'    => $chucVu,
            ':trangthai' => $trangThai,
            ':ghichu'    => $ghiChu,
            ':manv'      => $maNV
        ]);

        $_SESSION['success'] = 'Cập nhật nhân viên thành công';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Lỗi UPDATE nhân viên: " . $e->getMessage();
    }
    
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

   if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

    $conn = connectdb();
    $maNV = $_POST['manhanvien'] ?? '';

    if (empty($maNV)) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng chọn nhân viên!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        $sql = "UPDATE nhanvien 
                SET TrangThai = 'Ngừng làm việc'
                WHERE MaNV = :manv";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':manv' => $maNV]);

        $_SESSION['success'] = "Đã ngừng hoạt động nhân viên";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Lỗi cập nhật trạng thái nhân viên: " . $e->getMessage();
    }
}
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['delete_cancel'])) {
}

$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_item'])) {
}

?>
<?php define('ALLOW_RENDER', true);
include_once('../ThongTinTaiKhoan.php');
include_once __DIR__ . '/../Tienich.php';
?>
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
    .form-field input[type="number"] {
    width: 450px; /* chỉnh chiều rộng tùy ý */
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    }   
    .form-select {
    flex-grow: 5;          /* cho phép giãn giống input */
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;

    /* Tùy chỉnh kích thước */
    width: 500%;           /* bạn có thể đổi 150px, 200px, 50%... */
    max-width: 550px;      /* muốn giới hạn chiều rộng */
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
        <div class="form-container">
        <form method="post" id="formHangHoa">
            <div class="section-box">
                <div class="section-title">Thông tin nhân viên</div>
                    <div class="form-group">
                        <div class="form-field">
                            <label for="manhanvien">Mã nhân viên</label>
                            <input type="text" id="manhanvien" name="manhanvien" autocomplete="off"readonly>
                        </div>
                        <div class="form-field">
                            <label for="ngaysinh">Ngày tháng năm sinh</label>
                            <input type="date" id="ngaysinh" name="ngaysinh">
                        </div>
                        <div class="form-field">
                            <label for="hoten">Họ và tên</label>
                            <input type="text" id="hoten" name="hoten" placeholder="">
                        </div>
                        <div class="form-field">
                            <label for="diachi">Địa chỉ</label>
                            <input type="text" id="diachi" name="diachi" placeholder="">
                        </div>
                        <div class="form-field">
                        <label for="gioitinh">Giới tính</label>
                        <select id="gioitinh" name="gioitinh" autocomplete="off"class="form-select">
                            <option value="" selected>--Giới tính--</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Không">Không</option> 
                        </select>
                        </div>
                        <div class="form-field">
                        <label for="chucvu">Chức vụ</label>
                        <select id="chucvu" name="chucvu" autocomplete="off" class="form-select">
                            <option value="" selected>-- Chọn chức vụ --</option>
                            
                            <?php if (!empty($dsChucVu)): ?>
                                <?php foreach ($dsChucVu as $cv): ?>
                                    <option value="<?= htmlspecialchars($cv['TenChucVu']) ?>">
                                        <?= htmlspecialchars($cv['TenChucVu']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">(Chưa có dữ liệu chức vụ)</option>
                            <?php endif; ?>
                        </select>
                        </div>
                         <div class="form-field">
                            <label for="mail"> Email</label>
                            <input type="text" id="mail" name="mail" placeholder="">
                        </div>
                        <div class="form-field">
                            <label for="trangthai"> Trạng thái</label>
                            <input type="text" id="trangthai" name="trangthai" placeholder="">
                        </div>
                         <div class="form-field">
                            <label for="sodienthoai">Số điện thoại</label>
                            <input type="text" id="sodienthoai" name="sodienthoai" placeholder="">
                        </div>
                        <div class="form-field">
                            <label for="ghichu"> Ghi chú</label>
                            <textarea id="ghichu" name="ghichu" rows="4" placeholder="Nhập ghi chú..."></textarea>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" name="edit_item" class="btn add">Sửa</button>
                        <button type="submit" name="delete_item" class="btn delete">Xóa</button>
                        <button type="reset" class="btn delete">Hủy bỏ</button>
                    </div>

                            
                        <div id="popup" class="popup-overlay" style="display:none;">
                            <div class="popup-box">
                                <h3>Thông báo !</h3>
                                <p id="popup-message"></p>
                                <button type="button" onclick="closePopup()" class="popup-btn">OK</button>
                            </div>
                        </div>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã nhân viên </th>
                            <th>Họ và tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Địa chỉ</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Chức vụ</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $data = getNhanVien();
                            $stt = 1;
                            foreach ($data as $row) {
                                    echo "<tr class='row-select'
                                        data-manhanvien='{$row['MaNV']}'
                                        data-hoten='{$row['HoTen']}'
                                        data-ngaysinh='{$row['NgaySinh']}'
                                        data-gioitinh='{$row['GioiTinh']}'
                                        data-diachi='{$row['DiaChi']}'
                                        data-mail='{$row['Email']}'
                                        data-sodienthoai='{$row['SDT']}'
                                        data-chucvu='{$row['ChucVu']}'
                                        data-trangthai='{$row['TrangThai']}'
                                        data-ghichu='{$row['GhiChu']}'
                                    >";
                                     echo "<td>".$stt++."</td>";
                                    echo "<td>{$row['MaNV']}</td>";
                                    echo "<td>{$row['HoTen']}</td>";
                                    echo "<td>{$row['NgaySinh']}</td>";
                                    echo "<td>{$row['GioiTinh']}</td>";
                                    echo "<td>{$row['DiaChi']}</td>";
                                    echo "<td>{$row['Email']}</td>";
                                    echo "<td>{$row['SDT']}</td>";
                                    echo "<td>{$row['ChucVu']}</td>";
                                    echo "<td>{$row['TrangThai']}</td>";
                                    echo "<td>{$row['GhiChu']}</td>";
                                    echo "</tr>";
                                   
                                }
                        ?>  
                    </tbody>
                        <script>
                        document.querySelectorAll(".row-select").forEach(function (row) {
                            row.addEventListener("click", function () {

                                // Lấy dữ liệu từ data-*
                                document.getElementById("manhanvien").value = this.dataset.manhanvien;
                                document.getElementById("hoten").value       = this.dataset.hoten;
                                document.getElementById("ngaysinh").value    = this.dataset.ngaysinh;
                                document.getElementById("diachi").value      = this.dataset.diachi;
                                document.getElementById("mail").value       = this.dataset.mail;
                                document.getElementById("sodienthoai").value = this.dataset.sodienthoai;
                                document.getElementById("chucvu").value      = this.dataset.chucvu;
                                document.getElementById("trangthai").value   = this.dataset.trangthai;
                                document.getElementById("ghichu").value      = this.dataset.ghichu;

                                // Select giới tính
                                document.getElementById("gioitinh").value = this.dataset.gioitinh;

                                // Hiệu ứng chọn dòng
                                document.querySelectorAll(".row-select")
                                    .forEach(r => r.classList.remove("active-row"));
                                this.classList.add("active-row");
                            });
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
















