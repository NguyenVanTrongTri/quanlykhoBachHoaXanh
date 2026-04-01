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

function getPhieuXuat() {
    $conn = connectdb();
    $sql = "SELECT 
            STT,
            MaPhieuXuat,
            DonVi,
            BoPhan,
            NguoiNhanHang,
            DiaChi,
            ThoiGian,
            DiaDiem,
            LyDo,
            XuatTaiKho
        FROM phieuxuat
        ORDER BY ThoiGian DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//CHỨC NĂNG SỬA THÔNG TIN PHIẾU NHẬP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {

    // Kết nối DB
    $conn = connectdb();

    // Lấy dữ liệu từ form
    $maPhieuXuat    = $_POST['maphieuxuat'] ?? '';
    $donVi          = $_POST['donvi'] ?? '';
    $boPhan         = $_POST['bophan'] ?? '';
    $nguoiNhan      = $_POST['nguoinhanhang'] ?? '';
    $diaChi         = $_POST['diachibophan'] ?? '';
    $thoiGian       = $_POST['thoigian'] ?? '';
    $diaDiem        = $_POST['diadiem'] ?? '';
    $lyDo           = $_POST['lydoxuatkho'] ?? '';
    $xuatTaiKho     = $_POST['xuattaikho'] ?? '';

    // Kiểm tra khóa chính
    if (empty($maPhieuXuat)) {
        echo "Lỗi: không có mã phiếu xuất!";
        exit;
    }

    try {
        $sql = "
            UPDATE phieuxuat SET
                DonVi          = :donvi,
                BoPhan         = :bophan,
                NguoiNhanHang  = :nguoinhan,
                DiaChi         = :diachi,
                ThoiGian       = :thoigian,
                DiaDiem        = :diadiem,
                LyDo           = :lydo,
                XuatTaiKho     = :xuatkho
            WHERE MaPhieuXuat = :maphieu
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':donvi'    => $donVi,
            ':bophan'   => $boPhan,
            ':nguoinhan'=> $nguoiNhan,
            ':diachi'   => $diaChi,
            ':thoigian' => $thoiGian,
            ':diadiem'  => $diaDiem,
            ':lydo'     => $lyDo,
            ':xuatkho'  => $xuatTaiKho,
            ':maphieu'  => $maPhieuXuat
        ]);
        header("Location: " . $_SERVER['PHP_SELF']);

    } catch (PDOException $e) {
        echo "Lỗi UPDATE: " . $e->getMessage();
    }
}
// CHỨC NĂNG XÓA PHIẾU XUẤT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {

    // Kết nối DB
    $conn = connectdb();

    // Lấy mã phiếu
    $maPhieuXuat = $_POST['maphieuxuat'] ?? '';

    // Kiểm tra
    if (empty($maPhieuXuat)) {
        echo "<script>alert('Lỗi: Không có mã phiếu xuất để xóa!');</script>";
        exit;
    }

    try {

        // Lệnh DELETE
        $sql = "DELETE FROM PhieuXuat WHERE MaPhieuXuat = :maphieu";
        $stmt = $conn->prepare($sql);

        // Thực thi
        $stmt->execute([
            ':maphieu' => $maPhieuXuat
        ]);
        header("Location: " . $_SERVER['PHP_SELF']);
        //echo "<script>alert('Xóa phiếu xuất thành công!');</script>";
        //echo "<script>window.location.reload();</script>";

    } catch (PDOException $e) {
        echo "<script>alert('Lỗi XÓA: " . $e->getMessage() . "');</script>";
    }
}
$searchResults = [];
$noResults = false; // biến báo không có kết quả
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_item'])) {
    $conn = connectdb();
    $keyword = trim($_POST['keyword'] ?? '');
    if ($keyword !== '') {
        $sql = "SELECT * FROM phieuxuat
                WHERE MaPhieuXuat LIKE :keyword
                   OR DonVi LIKE :keyword
                   OR BoPhan LIKE :keyword
                   OR NguoiNhanHang LIKE :keyword";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':keyword' => "%$keyword%"]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($searchResults)) {
            $_SESSION['popup_error'] = true;
            $_SESSION['popup_message'] = "Không tìm thấy kết quả nào!";
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
$path = "../"; // từ TaoPhieuXuat/ lên root
include(__DIR__ . "/../php/header.php");
include('../ThongTinTaiKhoan.php'); 
?>
<script>
// chức năng click tên để mở cửa sổ thông tin tài khoảng
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
<!-- HEADER -->
<!-- LAYOUT -->
<div class="layout">

    <!-- SIDEBAR -->
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
        <form method="post" id="searchForm">
                <header class="search-area">
                <input type="text" id="keyword" name="keyword" class="search-input"placeholder="Nhập ký tự cần tìm">
                <button class="btn btn-search" name="search_item" >Tìm kiếm</button>
            </form>
        </header>
        <div class="form-container">
            <div class="section-box">
            <form method="post">
                <div class="section-title">Thông tin phiếu xuất</div>
                <div class="form-group">
                    <div class="form-field">
                        <label for="maphieuxuat">Mã phiếu xuất</label>
                        <input type="text" id="maphieuxuat" name="maphieuxuat"placeholder="" readonly>
                    </div>
                    <div class="form-field">
                        <label for="nguoinhanhang">Người nhận hàng</label>
                        <input type="text" id="nguoinhanhang" name="nguoinhanhang" placeholder="">
                    </div>

                    <div class="form-field">
                        <label for="donvi">Đơn vị</label>
                        <input type="text" id="donvi" name="donvi" style="width:100px;" placeholder="">
                    </div>
                    <div class="form-field">
                        <label for="diachibophan">Địa chỉ (bộ phận)</label>
                        <input type="text" id="diachibophan" name="diachibophan" placeholder="">
                    </div>
                    <div class="form-field">
                        <label for="bophan">Bộ phận</label>
                        <input type="text" id="bophan" name="bophan" style="width:100px;"placeholder="">
                    </div>
                    <div class="form-field">
                        <label for="thoigian">Thời gian</label>
                        <input type="text" id="thoigian" name="thoigian" style="width:100px;"placeholder="">
                    </div>
                    <div class="form-field">
                        <label for="lydoxuatkho">Lý do xuất kho</label>
                        <input type="text" id="lydoxuatkho" name="lydoxuatkho" placeholder="">
                    </div>
                    
                    <div class="form-field">
                        <label for="diadiem">Địa điểm</label>
                        <input type="text" id="diadiem"  name="diadiem" placeholder="">
                    </div>
                    <div class="form-field">
                        <label for="xuattaikho">Xuất tại kho</label>
                        <input type="text" id="xuattaikho" name="xuattaikho"  placeholder="">
                    </div>
                </div>
            </div>
                <div class="action-buttons">
                    
                    <button type="submit" name="edit_item" class="btn edit">Sửa</button>
                    <!-- Lưu index dòng đang chọn để xử lý xóa -->
                    <input type="hidden" name="delete_index" id="delete_index">
                    <button type="submit" name="delete_item" class="btn delete">Xóa</button>
                    <button type="submit" name="clear_item" class="btn clear">Hủy bỏ</button>
                    <button type="submit" name="inport_item" class="btn clear">Xuất hóa đơn</button>
                </div>
            </form>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã phiếu xuất</th>
                            <th>Đơn vị</th>
                            <th>Bộ phận</th>
                            <th>Người nhận hàng</th>
                            <th>Địa chỉ</th>
                            <th>Thời gian</th>
                            <th>Địa điểm</th>
                            <th>Lý do xuất kho</th>
                            <th>Xuất tại kho</th>
                        </tr>
                    </thead>

                   <tbody>
                        <?php
                         $data = !empty($searchResults) ? $searchResults : getPhieuXuat();
                        $stt = 1;
                        foreach ($data as $row) {
                            echo "<tr class='row-select'
                                data-maphieuxuat='".$row['MaPhieuXuat']."'
                                data-donvi='".$row['DonVi']."'
                                data-bophan='".$row['BoPhan']."'
                                data-diadiem='".$row['DiaDiem']."'
                                data-diachibophan='".$row['DiaChi']."'
                                data-nguoinhan='".$row['NguoiNhanHang']."'
                                data-thoigian='".$row['ThoiGian']."'
                                data-lydo='".$row['LyDo']."'
                                data-xuatkho='".$row['XuatTaiKho']."'
                            >";
                            echo "<td>" . $stt++ . "</td>";
                            echo "<td>" . $row['MaPhieuXuat'] . "</td>";
                            echo "<td>" . $row['DonVi'] . "</td>";
                            echo "<td>" . $row['BoPhan'] . "</td>";
                            echo "<td>" . $row['NguoiNhanHang'] . "</td>";
                            echo "<td>" . $row['DiaChi'] . "</td>";
                            echo "<td>" . $row['ThoiGian'] . "</td>";
                            echo "<td>" . $row['DiaDiem'] . "</td>";
                            echo "<td>" . $row['LyDo'] . "</td>";
                            echo "<td>" . $row['XuatTaiKho'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                        <script>
                        document.querySelectorAll(".row-select").forEach(row => {
                            row.addEventListener("click", function () {

                                document.getElementById("maphieuxuat").value = this.dataset.maphieuxuat;
                                document.getElementById("nguoinhanhang").value = this.dataset.nguoinhan;
                                document.getElementById("donvi").value = this.dataset.donvi;
                                document.getElementById("diachibophan").value = this.dataset.diachibophan;
                                document.getElementById("bophan").value = this.dataset.bophan;
                                document.getElementById("thoigian").value = this.dataset.thoigian;
                                document.getElementById("lydoxuatkho").value = this.dataset.lydo;
                                document.getElementById("diadiem").value = this.dataset.diadiem;
                                document.getElementById("xuattaikho").value = this.dataset.xuatkho;

                                // Hiệu ứng chọn dòng
                                document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
                                this.classList.add("active-row");
                            });
                        });
                        </script>
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
                        </script>
                        <div id="popup" class="popup-overlay" style="display:none;">
                            <div class="popup-box">
                                <h3>Thông báo !</h3>
                                <p id="popup-message"></p>
                                <button type="button" onclick="closePopup()" class="popup-btn">OK</button>
                            </div>
                        </div>
                </table>
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
</div>