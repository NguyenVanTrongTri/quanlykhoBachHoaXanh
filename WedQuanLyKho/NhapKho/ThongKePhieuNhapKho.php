<?php 
session_start();
include '../KetNoi/connect.php'; 
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

function getPhieuNhap() {
    $conn = connectdb();
    $sql = "SELECT 
            STT,
            MaPhieuNhap,
            DonVi,
            BoPhan,
            NguoiGiaoHang,
            DiaChi,
            ThoiGian,
            DiaDiem,
            NhapTaiKho
        FROM phieunhap
        ORDER BY ThoiGian DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$from = date('Y-m-d 00:00:00');
$to   = date('Y-m-d 23:59:59');
$kq = [
    'tongphieu' => 0,
    'tonggiatri' => 0,
];
$fromInput = '';
$toInput   = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loc_items'])) {
    $boloc = $_POST['boloc'] ?? '';

    switch ($boloc) {
        case 'homnay':
            $from = date('Y-m-d 00:00:00');
            $to   = date('Y-m-d 23:59:59');
            break;

        case 'homqua':
            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));
            $to   = date('Y-m-d 23:59:59', strtotime('-1 day'));
            break;

        case '7ngaytruoc':
            $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
            break;

        case '1thangtruoc':
            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));
            break;
    }
    $sql = "
    SELECT 
    COUNT(DISTINCT pn.MaPhieuNhap) AS tongphieu,
    SUM(ct.ThanhTien) AS tonggiatri
    FROM phieunhap pn
    INNER JOIN chitietphieunhap ct
        ON pn.MaPhieuNhap = ct.MaPhieuNhap
    WHERE pn.ThoiGian BETWEEN :from AND :to
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':from' => $from,
        ':to'   => $to
    ]);
    $kq = $stmt->fetch(PDO::FETCH_ASSOC);

   $sqltenhang = "
        SELECT 
            hh.TenHangHoa AS tenhanghoa,
            nc.TenNCC AS tennhacungcap,
            ct.SoLuongThucTeNhap AS soluongthucte
        FROM phieunhap pn
        INNER JOIN chitietphieunhap ct
            ON pn.MaPhieuNhap = ct.MaPhieuNhap
        INNER JOIN hanghoa hh 
            ON hh.MaHangHoa = ct.MaHangHoa
        INNER JOIN nhacungcap nc on nc.MaNCC=pn.MaNCC
        WHERE pn.ThoiGian BETWEEN :from AND :to
        ORDER BY ct.SoLuongThucTeNhap DESC
        LIMIT 1
        ";
    $stmt = $conn->prepare($sqltenhang);
    $stmt->execute([
        ':from' => $from,
        ':to'   => $to
    ]);
    $tenhanghoa = $stmt->fetch(PDO::FETCH_ASSOC);

    $sqltenhangitnhat = "
    SELECT 
        hh.TenHangHoa AS tenhanghoaitnhat
    FROM phieunhap pn
    INNER JOIN chitietphieunhap ct
        ON pn.MaPhieuNhap = ct.MaPhieuNhap
    INNER JOIN hanghoa hh 
        ON hh.MaHangHoa = ct.MaHangHoa
    WHERE pn.ThoiGian BETWEEN :from AND :to
    ORDER BY ct.SoLuongThucTeNhap ASC
        LIMIT 1
    ";
    $stmt = $conn->prepare( $sqltenhangitnhat);
    $stmt->execute([
        ':from' => $from,
        ':to'   => $to
    ]);
    $tenhanghoaitnhat = $stmt->fetch(PDO::FETCH_ASSOC);
    
}
function getTimeRange($type) {
    switch ($type) {

        case 'homnay':
            return [
                'from' => date('Y-m-d 00:00:00'),
                'to'   => date('Y-m-d 23:59:59')
            ];

        case 'homqua':
            return [
                'from' => date('Y-m-d 00:00:00', strtotime('-1 day')),
                'to'   => date('Y-m-d 23:59:59', strtotime('-1 day'))
            ];
         case 'tuanay':
            return [
                'from' => date('Y-m-d 00:00:00', strtotime('-6 days')),
                'to'   => date('Y-m-d 23:59:59')
            ];

         case 'tuantruoc':
            return [
                'from' => date('Y-m-d 00:00:00', strtotime('-13 days')),
                'to'   => date('Y-m-d 23:59:59', strtotime('-7 days'))
            ];

        case 'thangnay':
            return [
                'from' => date('Y-m-01 00:00:00'),
                'to'   => date('Y-m-t 23:59:59')
            ];

        case 'thangtruoc':
            return [
                'from' => date('Y-m-01 00:00:00', strtotime('-1 month')),
                'to'   => date('Y-m-t 23:59:59', strtotime('-1 month'))
            ];
    }
    return null;
}
$kq1 = ['soluong' => 0, 'giatri' => 0];
$kq2 = ['soluong' => 0, 'giatri' => 0];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['thongke_item'])) {

    $batdau  = $_POST['batdau']  ?? '';
    $ketthuc = $_POST['ketthuc'] ?? '';

    if ($batdau && $ketthuc) {

        $time1 = getTimeRange($batdau);
        $time2 = getTimeRange($ketthuc);

        // ---- KHOẢNG 1
        $sql = "SELECT COUNT(pn.MaPhieuNhap) AS soluong, SUM(ct.ThanhTien) AS giatri
                FROM  phieunhap pn inner join chitietphieunhap ct 
                on pn.MaPhieuNhap=ct.MaPhieuNhap
                WHERE pn.ThoiGian BETWEEN :from AND :to";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':from' => $time1['from'],
            ':to'   => $time1['to']
        ]);
        $kq1 = $stmt->fetch(PDO::FETCH_ASSOC);

        // ---- KHOẢNG 2
        $stmt->execute([
            ':from' => $time2['from'],
            ':to'   => $time2['to']
        ]);
        $kq2 = $stmt->fetch(PDO::FETCH_ASSOC);
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
/* Hiệu ứng chọn dòng*/
.active-row {
    background: #d1eaff !important;
    transition: 0.2s;
}
.row-select:hover {
    background: #f0f8ff;
    cursor: pointer;
}
/* Dùng cho tim kiém*/
    .search-area {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
    background-color: #e5e5e5;
}

.search-input {
    width: 150px;
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
        font-size: 10px;
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

    .action-buttons .btn.edit {
        background: #e60000; /* Màu đỏ */
        color: white;
    }

    .action-buttons .btn.delete {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }
    .action-buttons .btn.clear {
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
</style>
</head>

<body>
<?php
$path = "../";
include(__DIR__ . "/../php/header.php");
include('../ThongTinTaiKhoan.php'); 
?>
<script>
// chức năng click tên để mở cửa sổ thông tin tài khoảng
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.username').addEventListener('click', function() {
        fetch('../ThongTinTaiKhoan.php')
            .then(response => response.text())
            .then(html => {
                document.body.insertAdjacentHTML('beforeend', html);
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

        <button class="overview-btn" onclick="window.location.href='../TrangChu.php'" >Tổng quan</button>

        <!-- NHẬP KHO -->
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
  
        <!-- NHÂN VIÊN -->
        <div class="menu-item parent">👥 Nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../NhanVien/QuanLyNhanVien.php')">> Quản lý nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../TaiKhoan/QuanLyTaiKhoan.php')">> Quản lý tài khoản</div>
        
    </div>
    <div class="content">
        <header class="search-area">
            <form method="post">
            <select id="boloc" name="boloc" class="search-input">
                            <option value="" selected>-- Vui lòng chọn --</option>
                            <option value="homnay">Hôm nay</option>
                            <option value="homqua">Hôm qua</option>
                            <option value="7ngaytruoc">7 ngày trước</option>
                            <option value="1thangtruoc"> 1 tháng trước</option>
                        </select>
            <button class="btn btn-search" type="submit" name="loc_items"> Lọc </button>
            </form>
        </header>
        <div class="form-container">
            <div class="section-box">
            <form method="post">
                <div class="section-title">Thống kê phiếu nhập</div>
                <div class="form-group">
                    <div class="form-field">
                        <label for="tongso">Tổng số phiếu nhập kho</label>
                        <input type="text" id="tongso" name="tongso" value="<?= $kq['tongphieu'] ?>" placeholder="" readonly>
                        <label for="tonggiatri">Tổng giá trị nhập kho</label>
                        <input type="text" id="tonggiatri" name="tonggiatri" value="<?= number_format($kq['tonggiatri']) ?> đ" placeholder="" readonly>
                    </div>
                    <div class="form-field"> 
                        <label for="thoigianbatdau">Thời gian bắt đầu </label>
                        <input type="datetime-local" id="thoigianbatdau" name="thoigianbatdau" value="<?= $from ? date('Y-m-d\TH:i', strtotime($from)) : '' ?>">
                    </div>
                    <div class="form-field">
                        <label for="mathangnhieunhat">Mặt hàng nhập nhiều nhất</label>
                        <input type="text" id="mathangnhieunhat" name="mathangnhieunhat" value="<?= $tenhanghoa['tenhanghoa']?? 'Không có hàng hóa' ?>" placeholder="" readonly>
                        <label for="mathangitnhat">Mặt hàng nhập ít nhất</label>
                        <input type="text" id="mathangitnhat" name="mathangitnhat" value="<?= $tenhanghoaitnhat['tenhanghoaitnhat']?? 'Không có hàng hóa' ?>" placeholder="" readonly>
                    </div>
                    <div class="form-field">
                        <label for="thoigianketthuc"> Thời gian kết thúc </label>
                        <input type="datetime-local" id="thoigianketthuc" name="thoigianketthuc"  value="<?= $to ? date('Y-m-d\TH:i', strtotime($to)) : '' ?>">
                    </div>
                    <div class="form-field">
                        <label for="donvicungcap">Đơn vị cung cấp chính </label>
                        <input type="text" id="donvicungcap" name="donvicungcap" value="<?= $tenhanghoa['tennhacungcap']?? ' ' ?>" placeholder="" readonly>
                        <label for="soluong"> Số lượng đã nhập </label>
                        <input type="text" id="soluong" name="soluong" value="<?= $tenhanghoa['soluongthucte']?? '0' ?>" placeholder="" readonly>
                    </div>
                    <div class="form-field">
                        <label for="sosanh">So sánh</label>
                        <select id="batdau" name="batdau" class="search-input">
                            <option value="" selected>-- Vui lòng chọn --</option>
                            <option value="homnay">Hôm nay</option>
                            <option value="tuantruoc">Tuần này</option>
                            <option value="thangnay"> Tháng này</option>
                        </select>
                        <select id="ketthuc" name="ketthuc" class="search-input">
                            <option value="" selected>-- Vui lòng chọn --</option>
                            <option value="homqua">Hôm qua</option>
                            <option value="tuantruoc">Tuần trước</option>
                            <option value="thangtruoc"> Tháng trước</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="diadiem">Địa điểm</label>
                        <input type="text" id="diadiem"  name="diadiem" placeholder="" readonly>
                    </div>
                    <div class="form-field">
                        <label for="sophieunhap">Số phiếu nhập</label>
                        <input type="text" id="soluonghomnay" name="soluonghomnay" value="<?= $kq1['soluong'] ?? 0 ?>" style="width:100px;"placeholder="10" readonly>
                        <input type="text" id="soluonghomqua" name="soluonghomqua" value="<?= $kq2['soluong'] ?? 0 ?>" style="width:100px;"placeholder="20" readonly>
                        <input type="text" id="chenhlenh" name="chenhlenh" style="width:100px;"  value="<?= ($kq1['soluong'] ?? 0) - ($kq2['soluong'] ?? 0) ?>" placeholder=" Chênh lệch" readonly>
                    </div>
                    <div class="form-field">
                        <label for="nhaptaikho">Nhập tại kho</label>
                        <input type="text" id="nhaptaikho" name="nhaptaikho" placeholder="" readonly>
                    </div>
                    <div class="form-field">
                        <label for="tongiatri">Tổng giá trị</label>
                        <input type="text" id="giatrihomnay" name="giatrihomnay" style="width:100px;"  value="<?= number_format($kq1['giatri'] ?? 0) ?>" placeholder="10" readonly>
                        <input type="text" id="giatrihomqua" name="giatrihomqua" style="width:100px;"  value="<?= number_format($kq2['giatri'] ?? 0) ?>" placeholder="20"readonly>
                        <input type="text" id="giatrichenhlenh" name="giatrichenhlenh" style="width:100px;" value="<?= number_format(($kq1['giatri'] ?? 0) - ($kq2['giatri'] ?? 0)) ?>" placeholder=" Chênh lệch" readonly>
                    </div>
                </div>
            </div>
                <div class="action-buttons">
                    <button type="submit" name="thongke_item" class="btn edit"> Thống kê</button>
                    <button type="submit" name="clear_item" class="btn clear">Hủy bỏ</button>
                </div>
            </form>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã phiếu nhập</th>
                            <th>Đơn vị</th>
                            <th>Bộ phận</th>
                            <th>Người giao hàng</th>
                            <th>Địa chỉ</th>
                            <th>Thời gian</th>
                            <th>Địa điểm</th>
                            <th>Nhập tại kho</th>
                        </tr>
                    </thead>
                   <tbody>
                        <?php
                        $data = getPhieuNhap();
                        $stt = 1;
                        foreach ($data as $row) {
                            echo "<tr class='row-select'
                                data-maphieunhap='".$row['MaPhieuNhap']."'
                                data-donvi='".$row['DonVi']."'
                                data-bophan='".$row['BoPhan']."'
                                data-diadiem='".$row['DiaDiem']."'
                                data-diachi='".$row['DiaChi']."'
                                data-nguoigiaohang='".$row['NguoiGiaoHang']."'
                                data-thoigian='".$row['ThoiGian']."'
                                data-nhaptaikho='".$row['NhapTaiKho']."'
                            >";
                            echo "<td>" . $stt++ . "</td>";
                            echo "<td>" . $row['MaPhieuNhap'] . "</td>";
                            echo "<td>" . $row['DonVi'] . "</td>";
                            echo "<td>" . $row['BoPhan'] . "</td>";
                            echo "<td>" . $row['NguoiGiaoHang'] . "</td>";
                            echo "<td>" . $row['DiaChi'] . "</td>";
                            echo "<td>" . $row['ThoiGian'] . "</td>";
                            echo "<td>" . $row['DiaDiem'] . "</td>";
                            echo "<td>" . $row['NhapTaiKho'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                        <script>
                        document.querySelectorAll(".row-select").forEach(row => {
                            row.addEventListener("click", function () {

                                document.getElementById("maphieunhap").value = this.dataset.maphieunhap;
                                document.getElementById("nguoigiaohang").value = this.dataset.nguoigiaohang;
                                document.getElementById("donvi").value = this.dataset.donvi;
                                document.getElementById("diachi").value = this.dataset.diachi;
                                document.getElementById("bophan").value = this.dataset.bophan;
                                document.getElementById("thoigian").value = this.dataset.thoigian;
                                document.getElementById("diadiem").value = this.dataset.diadiem;
                                document.getElementById("nhaptaikho").value = this.dataset.nhaptaikho;

                                // Hiệu ứng chọn dòng
                                document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
                                this.classList.add("active-row");
                            });
                        });
                        </script>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>