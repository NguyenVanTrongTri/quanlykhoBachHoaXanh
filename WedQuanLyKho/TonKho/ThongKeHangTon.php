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


function getTonKho() {
    $conn = connectdb();

    $sql = "SELECT 
            tk.STT,
            hh.MaHangHoa,
            hh.TenHangHoa,
            hh.DonViTinh,
            kh.DaChua,
            hh.NgaySanXuat,
            hh.HanSuDung,
            hh.XuatXu,
            hh.MaVach,
            kh.MaLoaiKho,
            kh.MaKeHang,
            kh.ViTri,
            lk.TenLoaiKho,
            lk.NhietDo
        FROM tonkho tk
        INNER JOIN hanghoa hh ON tk.MaHangHoa = hh.MaHangHoa
        INNER JOIN kehanghoa kh ON kh.MaHangHoa = hh.MaHangHoa
        INNER JOIN loaikho lk ON kh.MaLoaiKho = lk.MaLoaiKho
        WHERE kh.DaChua > 0
        ORDER BY tk.MaTonKho";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  function getSucChuaByLoaiKho($MaLoaiKho) {
    $conn = connectdb();
    $sql = "
        SELECT 
            lk.MaLoaiKho,
            lk.TenLoaiKho,
            kh.TongSucChua as SucChuaKe,
            SUM(kh.TongSucChua) AS SucChuaTongKho,
            SUM(kh.DaChua) AS TongDaChua,
            SUM(kh.TongSucChua - kh.DaChua) AS ConTrong
        FROM loaikho lk
        LEFT JOIN kehanghoa kh
            ON lk.MaLoaiKho = kh.MaLoaiKho
        WHERE lk.MaLoaiKho = :maloaikho
        GROUP BY lk.MaLoaiKho, lk.TenLoaiKho, kh.TongSucChua
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':maloaikho' => $MaLoaiKho]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {

     $conn = connectdb();

    $maHangHoa  = $_POST['mahanghoa'] ?? '';
    $tenHangHoa = $_POST['tenhanghoa'] ?? '';
    $donViTinh  = $_POST['donvitinh'] ?? '';
    $daChua     = (int)($_POST['dachua'] ?? 0);
    $ngaySX     = $_POST['ngaysanxuat'] ?? null;
    $hanSD      = $_POST['hansudung'] ?? null;
    $xuatXu     = $_POST['xuatxu'] ?? '';
    $maVach     = $_POST['mavach'] ?? '';

    if (empty($maHangHoa)) {
        echo "Lỗi: Chưa chọn hàng hóa!";
        exit;
    }

    try {
        $conn->beginTransaction();

        /*UPDATE HÀNG HÓA */
        $sqlHH = "
            UPDATE hanghoa SET
                TenHangHoa  = :tenhanghoa,
                DonViTinh   = :donvitinh,
                NgaySanXuat = :ngaysanxuat,
                HanSuDung   = :hansudung,
                XuatXu      = :xuatxu,
                MaVach      = :mavach
            WHERE MaHangHoa = :mahanghoa
        ";

        $stmtHH = $conn->prepare($sqlHH);
        $stmtHH->execute([
            ':tenhanghoa'  => $tenHangHoa,
            ':donvitinh'   => $donViTinh,
            ':ngaysanxuat' => $ngaySX,
            ':hansudung'   => $hanSD,
            ':xuatxu'      => $xuatXu,
            ':mavach'      => $maVach,
            ':mahanghoa'   => $maHangHoa
        ]);

       $stmtCheck = $conn->prepare("SELECT TongSucChua FROM kehanghoa WHERE MaHangHoa = :mahanghoa");
        $stmtCheck->execute([':mahanghoa' => $maHangHoa]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception("Hàng hóa không tồn tại trong kệ");
        }

        if ($daChua > $row['TongSucChua']) {
            $_SESSION['popup_error']   = true;
            $_SESSION['popup_message'] = "Số lượng đã chứa vượt quá tổng sức chứa của kệ!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Nếu hợp lệ → update
        $sqlKe = "UPDATE kehanghoa SET DaChua = :dachua WHERE MaHangHoa = :mahanghoa";
        $stmtKe = $conn->prepare($sqlKe);
        $stmtKe->execute([
            ':dachua' => $daChua,
            ':mahanghoa' => $maHangHoa
        ]);

        $conn->commit();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Lỗi UPDATE tồn kho: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

    $conn = connectdb();

    // Lấy mã hàng hóa từ form (click dòng bảng)
    $maHangHoa = $_POST['mahanghoa'] ?? '';

    if (empty($maHangHoa)) {
        echo "<script>alert('Lỗi: Chưa chọn hàng hóa để xóa tồn kho!');</script>";
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1️⃣ Đưa số lượng đã chứa trên kệ về 0
        $sqlUpdateKe = "
            UPDATE kehanghoa
            SET DaChua = 0
            WHERE MaHangHoa = :MaHangHoa
        ";
        $stmtKe = $conn->prepare($sqlUpdateKe);
        $stmtKe->execute([
            ':MaHangHoa' => $maHangHoa
        ]);

        // 2️⃣ XÓA MỀM tồn kho (KHÔNG DELETE)
        $sqlUpdateTonKho = "
            UPDATE tonkho
            SET TrangThai = 0
            WHERE MaHangHoa = :MaHangHoa
        ";
        $stmtTK = $conn->prepare($sqlUpdateTonKho);
        $stmtTK->execute([
            ':MaHangHoa' => $maHangHoa
        ]);

        $conn->commit();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi xóa tồn kho: ".$e->getMessage()."');</script>";
    }
}
$data = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_item'])) {

    $conn = connectdb();
    $data = getTonKho();

    $keyword = trim($_POST['keyword'] ?? '');

    if ($keyword === '') {
        $_SESSION['popup_error']   = true;
        $_SESSION['popup_message'] = "Vui lòng nhập từ khóa tìm kiếm!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $search = "%" . $keyword . "%";

    $sql = "
        SELECT 
            tk.STT,
            tk.MaTonKho,
            hh.MaHangHoa,
            hh.TenHangHoa,
            hh.DonViTinh,
            kh.MaKeHang,
            kh.DaChua,
            kh.TongSucChua,
            hh.NgaySanXuat,
            hh.HanSuDung,
            hh.XuatXu,
            hh.MaVach
        FROM tonkho tk
        INNER JOIN hanghoa hh ON tk.MaHangHoa = hh.MaHangHoa
        INNER JOIN kehanghoa kh ON kh.MaHangHoa = hh.MaHangHoa
        WHERE tk.TrangThai = 1
          AND (
                hh.MaHangHoa LIKE :kw
             OR hh.TenHangHoa LIKE :kw
             OR hh.DonViTinh LIKE :kw
             OR hh.XuatXu LIKE :kw
             OR hh.MaVach LIKE :kw
             OR kh.MaKeHang LIKE :kw
             OR tk.MaTonKho LIKE :kw
             OR kh.DaChua LIKE :kw
          )
        ORDER BY tk.MaTonKho
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':kw' => $search
    ]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*KHÔNG CÓ KẾT QUẢ */
    if (!$data) {
        $_SESSION['popup_error']   = true;
        $_SESSION['popup_message'] = "Không tìm thấy dữ liệu phù hợp!";
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
    flex-grow: 1;
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
        <form method="post" id="formHangHoa">
            <div class="form-container">
                <div class="section-box">
                    <div class="section-title">Thông tin tồn kho</div>
                        <div class="form-group">
                            <div class="form-field">
                                <label for="succhuakho">Sức chứa tổng kho</label>
                                <input type="number" id="succhuakho" name="succhuakho" placeholder=""readonly>
                            </div>
                            <div class="form-field">
                                <label for="tongsucchuake">Tổng sức chứa kệ</label>
                                <input type="number" id="tongsucchuake" name="tongsucchuake" placeholder="" readonly>
                            </div>
                            <div class="form-field">
                                <label for="maloaikho">Mã kho</label>
                                <input type="text" id="maloaikho" name="maloaikho" placeholder="" readonly>
                            </div>

                            <div class="form-field">
                                <label for="masokehang">Mã số kệ hàng</label>
                                <input type="text" id="masokehang" name="masokehang" placeholder="" readonly>
                                 <label for="vitri">Vị trí bảo quản</label>
                                <input type="text" id="vitri" name="vitri" placeholder="" readonly>
                            </div>

                            <div class="form-field">
                                <label for="tenloaikho">Tên loại kho</label>
                                <input type="text" id="tenloaikho" name="tenloaikho" placeholder="" readonly>
                            </div>
                            <div class="form-field">
                                <label for="dachuake">Đã chứa</label>
                                <input type="number" id="dachuake" name="dachuake" placeholder="" readonly>
                            </div>
                            <div class="form-field">
                                <label for="nhietdo">Nhiệt độ</label>
                                <input type="text" id="nhietdo" name="nhietdo" placeholder="" readonly>
                            </div>
                            <div class="form-field">
                                <label for="controngke">Còn trống</label>
                                <input type="number" id="controngke" name="controngke" placeholder="0" readonly>
                            </div>                    
                        </div>
                </div>
                <div class="section-box">
                    <div class="section-title">Thông tin hàng hóa</div>
                        <div class="form-group">
                            <div class="form-field">
                                <label for="mahanghoa">Mã hàng hóa</label>
                                <input type="text" id="mahanghoa" name="mahanghoa" placeholder="HH001" readonly>
                            </div>
                            <div class="form-field">
                                <label for="ngaysanxuat">Ngày sản xuất</label>
                                <input type="text" id="ngaysanxuat" name="ngaysanxuat" placeholder="" readonly>
                            </div>
                            <div class="form-field">
                                <label for="tenhanghoa">Tên hàng hóa</label>
                                <input type="text" id="tenhanghoa" name="tenhanghoa" placeholder="Sầu riêng" readonly>
                            </div>
                            <div class="form-field">
                            <label for="hansudung">Hạn sử dụng</label>
                            <input type="text" id="hansudung" name="hansudung" readonly>
                            </div>
                            <div class="form-field">
                                <label for="dachua">Số lượng tồn kho</label>
                                <input type="text" id="dachua" name="dachua" placeholder="0" readonly>
                            </div>
                            <div class="form-field">
                            <label for="donvitinh">Đơn vị tính</label>
                            <select id="donvitinh" name="donvitinh" class="form-select" readonly>
                                <option value="" selected>-- Chọn đơn vị --</option>
                                <option value="Kg">Kg</option>
                                <option value="kg">Kg</option>
                                <option value="Thùng">Thùng</option>
                                <option value="thùng">Thùng</option>
                                <option value="Hộp">Hộp</option>
                                <option value="hộp">Hộp</option>
                            </select>
                            </div>
                            <div class="form-field">
                                <label for="xuatxu">Xuất xứ</label>
                                <input type="text" id="xuatxu" name="xuatxu" placeholder="Xuất xứ" readonly>
                            </div>
                            <div class="form-field">
                                <label for="mavach">Mã vạch</label>
                                <input type="text" id="mavach" name="mavach" placeholder="Mã vạch" readonly>
                            </div>  
                        </div>
                </div>
            </div>

        </form>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã hàng hóa</th>
                            <th>Tên hàng hóa</th>
                             <th>Số lượng tồn kho</th>
                            <th>Đơn vị tính</th>
                            <th>Ngày sản xuất</th>
                            <th>Hạn sử dụng</th>
                            <th>Xuất xứ</th>
                            <th>Mã vạch</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tbody>
                        <?php
                            $data = getTonKho();
                            $stt = 1;
                            foreach ($data as $row) {
                                $sucChuaData = getSucChuaByLoaiKho($row['MaLoaiKho']);
                                $sucChua = $sucChuaData['SucChuaTongKho'] ?? 0;
                                $tongDaChua = $sucChuaData['TongDaChua'] ?? 0;
                                $conTrong = $sucChuaData['ConTrong'] ?? 0;
                                $succhuake= $sucChuaData['SucChuaKe'] ?? 0;
                                echo "<tr class='row-select'
                                    data-mahanghoa='".$row['MaHangHoa']."'
                                    data-tenhanghoa='".$row['TenHangHoa']."'
                                    data-dachua='".$row['DaChua']."'
                                    data-donvitinh='".$row['DonViTinh']."'
                                    data-ngaysanxuat='".$row['NgaySanXuat']."'
                                    data-hansudung='".$row['HanSuDung']."'
                                    data-xuatxu='".$row['XuatXu']."'
                                    data-mavach='".$row['MaVach']."'

                                    data-succhuakho='".($sucChua ?? '')."'
                                    data-maloaikho='".($row['MaLoaiKho'] ?? '')."'
                                    data-tenloaikho='".($row['TenLoaiKho'] ?? '')."'
                                    data-nhietdo='".($row['NhietDo'] ?? '')."'
                                    
                                    data-tongsucchuake='".($succhuake ?? '')."'
                                    data-masokehang='".($row['MaKeHang'] ?? '')."'
                                    data-dachua='".$row['DaChua']."'
                                    data-vitri='".($row['ViTri'] ?? '')."'
                                    data-controng='".($conTrong ?? '')."' 
                                >";
                                echo "<td>".$stt++."</td>";
                                echo "<td>".$row['MaHangHoa']."</td>";
                                echo "<td>".$row['TenHangHoa']."</td>";
                                echo "<td>".$row['DaChua']."</td>";
                                echo "<td>".$row['DonViTinh']."</td>";
                                echo "<td>".$row['NgaySanXuat']."</td>";
                                echo "<td>".$row['HanSuDung']."</td>";
                                echo "<td>".$row['XuatXu']."</td>";
                                echo "<td>".$row['MaVach']."</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        <script>
                        document.querySelectorAll(".row-select").forEach(row => {
                            row.addEventListener("click", function () {
                                document.getElementById("mahanghoa").value   = this.dataset.mahanghoa;
                                document.getElementById("tenhanghoa").value  = this.dataset.tenhanghoa;
                                document.getElementById("dachua").value      = this.dataset.dachua;
                                document.getElementById("donvitinh").value   = this.dataset.donvitinh;
                                document.getElementById("ngaysanxuat").value = this.dataset.ngaysanxuat;
                                document.getElementById("hansudung").value   = this.dataset.hansudung;
                                document.getElementById("xuatxu").value      = this.dataset.xuatxu;
                                document.getElementById("mavach").value      = this.dataset.mavach;

                                document.getElementById("succhuakho").value   = this.dataset.succhuakho || 0;
                                document.getElementById("maloaikho").value = this.dataset.maloaikho || 'Khong tim thay';
                                document.getElementById("tenloaikho").value = this.dataset.tenloaikho;
                                document.getElementById("nhietdo").value = this.dataset.nhietdo;


                                document.getElementById("tongsucchuake").value = this.dataset.tongsucchuake || 0;
                                document.getElementById("dachuake").value      = this.dataset.dachua || 0;
                                document.getElementById("vitri").value = this.dataset.vitri || '';
                                document.getElementById("masokehang").value    = this.dataset.masokehang || '';
                                const conTrong = Number(this.dataset.tongsucchuake || 0) - Number(this.dataset.dachua || 0);
                                document.getElementById("controngke").value    = conTrong;

                                document.querySelectorAll(".row-select")
                                    .forEach(r => r.classList.remove("active-row"));
                                this.classList.add("active-row");
                            });
                        });
                        </script>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
</script>
</div>