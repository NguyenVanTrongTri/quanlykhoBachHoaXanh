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

$sqlHH = "
    SELECT 
        MaHangHoa,
        TenHangHoa
    FROM hanghoa
    ORDER BY TenHangHoa
";
$stmtHH = $conn->query($sqlHH);
$dsHangHoa = $stmtHH->fetchAll(PDO::FETCH_ASSOC);


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

function getKeHang() {
    $conn = connectdb();

    $sql = "SELECT 
                kh.STT,
                kh.MaKeHang,
                hh.MaHangHoa,
                hh.TenHangHoa,
                lk.MaLoaiKho,
                lk.TenLoaiKho,
                lk.NhietDo,
                kh.ViTri,
                kh.TongSucChua,
                kh.DaChua,
                kh.ConTrong,
                kh.MucToiThieuCanhBao,
                kh.MucGioiHanCanhBao,
                kh.TrangThai
            FROM kehanghoa kh
            INNER JOIN loaikho lk 
                ON kh.MaLoaiKho = lk.MaLoaiKho
            INNER JOIN hanghoa hh 
                ON hh.MaHangHoa = kh.MaHangHoa
            ORDER BY kh.STT";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if (!isset($_SESSION['temp_kehang'])) {
    $_SESSION['temp_kehang'] = [];
}
$temp = $_SESSION['temp_kehang'] ?? [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {

    $conn = connectdb();

    // ===== LẤY DỮ LIỆU TỪ FORM =====
    $maKeHang    = trim($_POST['makehang'] ?? '');
    $maLoaiKho   = trim($_POST['maloaikho'] ?? '');
    $viTri       = trim($_POST['vitri'] ?? '');
    $tongSucChua = (int)($_POST['tongsucchua'] ?? 0);
    $daChua      = (int)($_POST['dachua'] ?? 0);
    $trangThai   = trim($_POST['trangthai'] ?? '');
    $maHangHoa   = trim($_POST['mahanghoa'] ?? '');

    // 🔥 2 CỘT MỚI
    $mucToiThieu = (int)($_POST['muctoithieu'] ?? 0);
    $mucGioiHan  = (int)($_POST['mucgioihan'] ?? 0);

    $_SESSION['temp_kehang'] = [
        'makehang'    => $maKeHang,
        'maloaikho'   => $maLoaiKho,
        'vitri'       => $viTri,
        'tongsucchua' => $tongSucChua,
        'dachua'      => $daChua,
        'trangthai'   => $trangThai,
        'mahanghoa'   => $maHangHoa,
        'muctoithieu' => $mucToiThieu,
        'mucgioihan'  => $mucGioiHan
    ];

    // ===== KIỂM TRA =====
    if ($maKeHang === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng nhập mã kệ hàng!";
        return;
    }

    if ($viTri === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vị trí kệ hàng không được để trống!";
        return;
    }

    if ($maLoaiKho === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng chọn loại kho!";
        return;
    }

    if ($tongSucChua <= 0) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Tổng sức chứa phải lớn hơn 0!";
        return;
    }

    if ($daChua > $tongSucChua) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Đã chứa không được vượt quá tổng sức chứa!";
        return;
    }

    // kiểm tra mức cảnh báo
    if ($mucToiThieu < 0 || $mucGioiHan < 0 || ($mucGioiHan > 0 && $mucGioiHan < $mucToiThieu)) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Mức cảnh báo không hợp lệ!";
        return;
    }

    // ===== KIỂM TRA TRÙNG MÃ KỆ =====
    $check = $conn->prepare("SELECT 1 FROM kehanghoa WHERE MaKeHang = ?");
    $check->execute([$maKeHang]);

    if ($check->fetch()) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Mã kệ hàng đã tồn tại!";
        return;
    }

    try {
        // ===== INSERT (ĐÃ CÓ 2 CỘT CẢNH BÁO) =====
        $sql = "
            INSERT INTO kehanghoa (
                MaKeHang,
                MaLoaiKho,
                ViTri,
                TongSucChua,
                DaChua,
                TrangThai,
                MaHangHoa,
                MucToiThieuCanhBao,
                MucGioiHanCanhBao
            ) VALUES (
                :makehang,
                :maloaikho,
                :vitri,
                :tongsucchua,
                :dachua,
                :trangthai,
                :mahanghoa,
                :muctoithieu,
                :mucgioihan
            )
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':makehang'    => $maKeHang,
            ':maloaikho'   => $maLoaiKho,
            ':vitri'       => $viTri,
            ':tongsucchua' => $tongSucChua,
            ':dachua'      => $daChua,
            ':trangthai'   => $trangThai ?: 'Trống',
            ':mahanghoa'   => $maHangHoa ?: null,
            ':muctoithieu' => $mucToiThieu,
            ':mucgioihan'  => $mucGioiHan
        ]);

        unset($_SESSION['temp_kehang']);
        $_SESSION['success'] = "Thêm kệ hàng thành công!";

    } catch (PDOException $e) {
    $_SESSION['popup_error'] = true;
    $_SESSION['popup_message'] = "Có lỗi xảy ra khi thêm kệ hàng. Vui lòng thử lại!";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {

    $conn = connectdb();

    // ===== LẤY DỮ LIỆU TỪ FORM =====
    $maKeHang    = trim($_POST['makehang'] ?? '');
    $maLoaiKho   = trim($_POST['maloaikho'] ?? '');
    $viTri       = trim($_POST['vitri'] ?? '');
    $tongSucChua = (int)($_POST['tongsucchua'] ?? 0);
    $daChua      = (int)($_POST['dachua'] ?? 0);
    $trangThai   = trim($_POST['trangthai'] ?? '');
    $maHangHoa   = trim($_POST['mahanghoa'] ?? '');

    // 🔥 2 CỘT MỚI
    $mucToiThieu = (int)($_POST['muctoithieu'] ?? 0);
    $mucGioiHan  = (int)($_POST['mucgioihan'] ?? 0);

    // ===== KIỂM TRA =====
    if ($maKeHang === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng chọn kệ hàng!";
        return;
    }

    if ($maLoaiKho === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Vui lòng chọn loại kho!";
        return;
    }

    if ($daChua > $tongSucChua) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Đã chứa không được vượt quá tổng sức chứa!";
        return;
    }

    if ($mucToiThieu < 0 || $mucGioiHan < 0) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Mức cảnh báo không hợp lệ!";
        return;
    }

    if ($mucToiThieu > $mucGioiHan) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Mức tối thiểu không được lớn hơn mức giới hạn!";
        return;
    }

    // ===== KIỂM TRA KỆ HÀNG CÓ TỒN TẠI =====
    $check = $conn->prepare("SELECT 1 FROM kehanghoa WHERE MaKeHang = ?");
    $check->execute([$maKeHang]);

    if (!$check->fetch()) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Kệ hàng không tồn tại!";
        return;
    }

    try {
        // ===== UPDATE =====
        $sql = "
            UPDATE kehanghoa SET
                MaLoaiKho              = :maloaikho,
                ViTri                  = :vitri,
                TongSucChua            = :tongsucchua,
                DaChua                 = :dachua,
                TrangThai              = :trangthai,
                MaHangHoa              = :mahanghoa,
                MucToiThieuCanhBao     = :muctoithieu,
                MucGioiHanCanhBao      = :mucgioihan
            WHERE MaKeHang = :makehang
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':maloaikho'   => $maLoaiKho,
            ':vitri'       => $viTri,
            ':tongsucchua' => $tongSucChua,
            ':dachua'      => $daChua,
            ':trangthai'   => $trangThai,
            ':mahanghoa'   => $maHangHoa ?: null,
            ':muctoithieu' => $mucToiThieu,
            ':mucgioihan'  => $mucGioiHan,
            ':makehang'    => $maKeHang
        ]);

        $_SESSION['success'] = "Cập nhật kệ hàng thành công!";

    } catch (PDOException $e) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Có lỗi xảy ra khi cập nhật kệ hàng!";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

    $conn = connectdb();

    // 1. Lấy mã kệ hàng
    $maKeHang = trim($_POST['makehang'] ?? '');

    // 2. Kiểm tra rỗng
    if ($maKeHang === '') {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Vui lòng chọn kệ hàng cần xóa';
        return;
    }

    // 3. Kiểm tra kệ có tồn tại + số lượng đang chứa
    $check = $conn->prepare("
        SELECT DaChua
        FROM kehanghoa
        WHERE MaKeHang = ?
    ");
    $check->execute([$maKeHang]);
    $row = $check->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Kệ hàng không tồn tại trong hệ thống';
        return;
    }

    // 4. Không cho xóa nếu kệ còn hàng
    if ((int)$row['DaChua'] > 0) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Không thể xóa kệ đang chứa hàng';
        return;
    }

    // 5. Thực hiện xóa
    try {
        $stmt = $conn->prepare("DELETE FROM kehanghoa WHERE MaKeHang = ?");
        $stmt->execute([$maKeHang]);

        $_SESSION['success'] = 'Đã xóa kệ hàng thành công';

    } catch (PDOException $e) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = 'Không thể xóa kệ hàng (đang được sử dụng)';
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['delete_cancel'])) {
}
$searchResults = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_item'])) {

    $conn = connectdb();
    $keyword = trim($_POST['keyword'] ?? '');

    if ($keyword !== '') {

        $sql = "
            SELECT 
                kh.STT,
                kh.MaKeHang,
                hh.MaHangHoa,
                hh.TenHangHoa,
                lk.MaLoaiKho,
                lk.TenLoaiKho,
                lk.NhietDo,
                kh.ViTri,
                kh.TongSucChua,
                kh.DaChua,
                kh.ConTrong,
                kh.TrangThai,
                kh.MucToiThieuCanhBao,
                kh.MucGioiHanCanhBao
            FROM kehanghoa kh
            LEFT JOIN hanghoa hh ON kh.MaHangHoa = hh.MaHangHoa
            INNER JOIN loaikho lk ON kh.MaLoaiKho = lk.MaLoaiKho
            WHERE kh.MaKeHang   LIKE :kw
               OR hh.MaHangHoa  LIKE :kw
               OR hh.TenHangHoa LIKE :kw
               OR lk.TenLoaiKho LIKE :kw
               OR kh.ViTri      LIKE :kw
            ORDER BY kh.STT
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':kw' => "%$keyword%"
        ]);

        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($searchResults)) {
            $_SESSION['popup_error'] = true;
            $_SESSION['popup_message'] = 'Không tìm thấy kệ hàng phù hợp';
        }
    }
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
        <div class="menu-item" onclick="checkPermission('../TaiKhoan/QuanLyTaiKhoan.php')">> Quản lý tài khoản</div>
    
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
                <div class="section-title">Thông tin kệ hàng</div>
                    <div class="form-group">
                        <div class="form-field">
                            <label for="makehang">Mã kệ hàng</label>
                            <input type="text" id="makehang" name="makehang" autocomplete="off"  value="<?= htmlspecialchars($temp['makehang'] ?? '') ?>">
                             <label for="vitri">Vị trí kệ hàng</label>
                            <input type="text" id="vitri" name="vitri" placeholder="">
                        </div>

                        <div class="form-field">
                        <label for="maloaikho">Mã loại kho</label>
                        <select id="maloaikho" name="maloaikho" autocomplete="off" class="form-select">
                            <option value="" selected>-- Chọn mã loại kho --</option>
                            <option value="LK01">LK01 - Kho Thường</option>
                            <option value="LK02">LK02 - Kho Mát D1</option>
                            <option value="LK03">LK03 - Kho Đông D2</option>
                            <option value="LK04">LK04 - Kho Âm Sâu D3</option>
                        </select>
                        </div>
                        <div class="form-field">
                                <label for="mahanghoa">Mã hàng hóa</label>
                                <input type="text" id="mahanghoa" name="mahanghoa" autocomplete="off" readonly>
                                <label for="hanghoa">Tên hàng hóa</label>
                                <select id="hanghoa" class="form-select">
                                    <option value="">-- Chọn hàng hóa --</option>

                                    <?php foreach ($dsHangHoa as $hh): ?>
                                        <option value="<?= $hh['MaHangHoa'] ?>">
                                            <?= htmlspecialchars($hh['TenHangHoa']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </div>
                        <script>
                        document.getElementById("hanghoa").addEventListener("change", function () {
                            let maHangHoa = this.value;

                            if (maHangHoa === "") {
                                document.getElementById("mahanghoa").value = "";
                                return;
                            }

                            document.getElementById("mahanghoa").value = maHangHoa;
                        });
                        </script>

                        <div class="form-field">
                            <label for="tenloaikho">Tên loại kho</label>
                            <input type="text" id="tenloaikho" name="tenloaikho"   readonly>
                            <script>
                                document.getElementById("maloaikho").addEventListener("change", function () {
                                    let ma = this.value.trim();

                                    if (ma === "") {
                                        document.getElementById("tenloaikho").value = "";
                                        return;
                                    }

                                    fetch("../php/getLoaiKho.php?maloaikho=" + encodeURIComponent(ma))
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.status === "success") {
                                                document.getElementById("tenloaikho").value = data.data.TenLoaiKho;
                                            } else {
                                                showPopup("Không tìm thấy mã loại kho!");
                                                document.getElementById("tenloaikho").value = "";
                                            }
                                        })
                                        .catch(() => showPopup("Lỗi kết nối server"));
                                });
                            </script>
                        </div>
                        <div class="form-field">
                            <label for="tongsucchua">Tổng sức chứa</label>
                            <input type="number" id="tongsucchua"  name="tongsucchua"placeholder="" value="<?= htmlspecialchars($temp['tongsucchua'] ?? '') ?>">
                        </div>
                            <div class="form-field">
                            <label for="nhietdo">Nhiệt độ</label>
                            <input type="text" id="nhietdo"  name="nhietdo"placeholder="" readonly>
                            <script>
                                document.getElementById("maloaikho").addEventListener("change", function () {
                                    let ma = this.value.trim();

                                    if (ma === "") {
                                        document.getElementById("tenloaikho").value = "";
                                        document.getElementById("nhietdo").value = "";
                                        return;
                                    }

                                    fetch("../php/getLoaiKho.php?maloaikho=" + encodeURIComponent(ma))
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.status === "success") {
                                                document.getElementById("tenloaikho").value = data.data.TenLoaiKho;
                                                document.getElementById("nhietdo").value   = data.data.NhietDo;
                                            } else {
                                                showPopup("Không tìm thấy mã loại kho!");
                                                document.getElementById("tenloaikho").value = "";
                                                document.getElementById("nhietdo").value = "";
                                            }
                                        })
                                        .catch(() => showPopup("Lỗi kết nối server"));
                                });
                                </script>

                        </div>
                        <div class="form-field">
                            <label for="dachua">Đã chứa</label>
                            <input type="number" id="dachua"  name="dachua"placeholder="" value="<?= htmlspecialchars($temp['dachua'] ?? '') ?>" >
                        </div>
                        <div class="form-field">
                            <label for="trangthai">Trạng thái</label>
                            <input type="text" id="trangthai"  name="trangthai"placeholder=""  value="<?= htmlspecialchars($temp['trangthai'] ?? '') ?>">
                        </div>
                        <div class="form-field">
                            <label for="controng">Còn trống</label>
                            <input type="number" id="controng"  name="controng"placeholder="" readonly value="<?= htmlspecialchars($temp['controng'] ?? '') ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="muctoithieu"> Mức tối thiểu</label>
                            <input type="number" id="muctoithieu"  name="muctoithieu"placeholder="" value="<?= htmlspecialchars($temp['muctoithieu'] ?? '') ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="mucgioihan">Mức giới hạn</label>
                            <input type="number" id="mucgioihan"  name="mucgioihan"placeholder="" value="<?= htmlspecialchars($temp['mucgioihan'] ?? '') ?>"> 
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
                                if (ngaySX && hanSD) {
                                    if (new Date(ngaySX) > new Date(hanSD)) {
                                        e.preventDefault(); // chặn submit
                                        showPopup("Ngày sản xuất không được sau hạn sử dụng");
                                        return false;
                                    }
                                }
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
                    </div>
                    <div class="action-buttons">
                        <button type="submit" name="add_item" class="btn add">Thêm</button>
                        <button type="submit" name="edit_item" class="btn delete">Sửa</button>
                        <button type="submit" name="delete_item" class="btn delete">Xóa</button>
                        <button type="reset" class="btn delete">Hủy bỏ</button>
                    </div>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã kệ hàng</th>
                            <th>Mã hàng hóa</th>
                            <th>Tên hàng hóa</th>
                            <th>Vị trí kệ hàng</th>
                            <th>Tổng sức chứa</th>
                            <th>Đã chứa</th>
                            <th>Còn trống</th>
                            <th>Mã loại kho</th>
                            <th>Tên loại kho</th>
                            <th>Nhiệt độ</th>
                            <th>Trạng thái</th>
                            <th>Mức tối thiểu cảnh báo</th>
                            <th>Mức giới hạn cảnh báo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php                            
                            $data = !empty($searchResults) ? $searchResults : getKeHang();
                            $stt = 1;
                            foreach ($data as $index => $row) {
                               echo "<tr class='row-select'
                                    data-makehang='".$row['MaKeHang']."'
                                    data-mahanghoa='".$row['MaHangHoa']."'
                                    data-tenhanghoa='".$row['TenHangHoa']."'
                                    data-vitri='".$row['ViTri']."'
                                    data-tongsucchua='".$row['TongSucChua']."'
                                    data-dachua='".$row['DaChua']."'
                                    data-controng='".$row['ConTrong']."'
                                    data-maloaikho='".$row['MaLoaiKho']."'
                                    data-tenloaikho='".$row['TenLoaiKho']."'
                                    data-nhietdo='".$row['NhietDo']."'
                                    data-trangthai='".$row['TrangThai']."'
                                    data-muctoithieu='".$row['MucToiThieuCanhBao']."'
                                    data-mucgioihan='".$row['MucGioiHanCanhBao']."'
                                >";
                                echo "<td>".$stt++."</td>";
                                echo "<td>".$row['MaKeHang']."</td>";
                                echo "<td>".$row['MaHangHoa']."</td>";
                                echo "<td>".$row['TenHangHoa']."</td>";
                                echo "<td>".$row['ViTri']."</td>";
                                echo "<td>".$row['TongSucChua']."</td>";
                                echo "<td>".$row['DaChua']."</td>";
                                echo "<td>".$row['ConTrong']."</td>";
                                echo "<td>".$row['MaLoaiKho']."</td>";
                                echo "<td>".$row['TenLoaiKho']."</td>";
                                echo "<td>".$row['NhietDo']."</td>";
                                echo "<td>".$row['TrangThai']."</td>";
                                echo "<td>".$row['MucToiThieuCanhBao']."</td>";
                                echo "<td>".$row['MucGioiHanCanhBao']."</td>";
                                echo "</tr>";
                            }
                        ?>
                        <script>
                            document.querySelectorAll(".row-select").forEach(function (row) {
                                row.addEventListener("click", function () {
                                    document.getElementById("makehang").value     = this.dataset.makehang || "";
                                    document.getElementById("hanghoa").value     = this.dataset.tenhanghoa || "";
                                    document.getElementById("mahanghoa").value     = this.dataset.mahanghoa || "";
                                    document.getElementById("vitri").value        = this.dataset.vitri || "";
                                    document.getElementById("tongsucchua").value  = this.dataset.tongsucchua || "";
                                    document.getElementById("dachua").value       = this.dataset.dachua || "";
                                    document.getElementById("controng").value     = this.dataset.controng || "";
                                    document.getElementById("trangthai").value    = this.dataset.trangthai || "";
                                    document.getElementById("nhietdo").value      = this.dataset.nhietdo || "";
                                    document.getElementById("muctoithieu").value =this.dataset.muctoithieu || "";
                                    document.getElementById("mucgioihan").value =this.dataset.mucgioihan || "";
                                   
                                    

                                    const maLoaiKho = this.dataset.maloaikho || "";
                                    const selectLoaiKho = document.getElementById("maloaikho");

                                    selectLoaiKho.value = maLoaiKho;
                                    if (maLoaiKho !== "") {
                                        selectLoaiKho.dispatchEvent(new Event("change"));
                                    }
                                    document.querySelectorAll(".row-select").forEach(r =>
                                        r.classList.remove("active-row")
                                    );
                                    this.classList.add("active-row");
                                });
                            });
                        </script>
                    </tbody>
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
