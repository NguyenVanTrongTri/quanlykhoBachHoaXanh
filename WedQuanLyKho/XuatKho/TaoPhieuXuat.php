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


$phieu = $_SESSION['PhieuNhap_items'] ?? [];

$sqlHH = "
    SELECT DISTINCT 
        hh.MaHangHoa,
        hh.TenHangHoa
    FROM kehanghoa kh
    INNER JOIN hanghoa hh 
        ON kh.MaHangHoa = hh.MaHangHoa
    Where kh.DaChua > 0
    ORDER BY hh.TenHangHoa
";

$stmtHH = $conn->query($sqlHH);
$dsHangHoa = $stmtHH->fetchAll(PDO::FETCH_ASSOC); 

$sqlLoaiKho = "
    SELECT MaLoaiKho, TenLoaiKho
    FROM loaikho
    ORDER BY TenLoaiKho
";
$stmtLoaiKho = $conn->query($sqlLoaiKho);
$dsLoaiKho = $stmtLoaiKho->fetchAll(PDO::FETCH_ASSOC);

$sqlDiaChi = "
    SELECT DISTINCT DiaChi
    FROM nhaphanphoi
    WHERE DiaChi IS NOT NULL
    ORDER BY DiaChi
";
$stmtDiaChi = $conn->query($sqlDiaChi);
$dsDiaChi = $stmtDiaChi->fetchAll(PDO::FETCH_ASSOC);
 
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
$phieu = $_SESSION['PhieuXuat_items'] ?? [];

function taoMaPhieuXuat($conn){

    $prefix = "PX026";

    $sql = "SELECT MaPhieuXuat
            FROM phieuxuat
            WHERE MaPhieuXuat LIKE :prefix
            ORDER BY MaPhieuXuat DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':prefix' => $prefix . '%'
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $lastCode = $row['MaPhieuXuat'];

        // lấy 4 số cuối
        $number = intval(substr($lastCode, -4));
    } else {
        $number = 0;
    }

    do {

        $number++;

        $newCode = $prefix . str_pad($number, 4, "0", STR_PAD_LEFT);

        // kiểm tra trùng
        $checkSql = "SELECT COUNT(*) 
                     FROM phieuxuat 
                     WHERE MaPhieuXuat = :code";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([
            ':code' => $newCode
        ]);

        $exists = $checkStmt->fetchColumn();

    } while ($exists > 0);

    return $newCode;
}
function layTonKho($conn, $maHangHoa){

    $sql = "SELECT SUM(SoLuongTon) AS TonKho
            FROM tonkho_chitiet
            WHERE MaHangHoa = :ma
              AND TrangThai = 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':ma' => $maHangHoa
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row && $row['TonKho'] !== null){
        return $row['TonKho']; // trả về tổng tồn kho
    }

    return 0; // nếu không có tồn
}
// Khởi tạo mảng hàng hóa nếu chưa có
if (!isset($_SESSION['XuatKho_items'])) {
    $_SESSION['XuatKho_items'] = [];
}
if (!isset($_SESSION['PhieuXuat_items'])) {
    $_SESSION['PhieuXuat_items'] = [];
}

//Chức năng thêm
if($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['add_item'])){
    // Lấy dữ liệu từ form
    $ma = $_POST['mahanghoa'];
    $sl_ct = intval($_POST['soluongtheochungtu'] ?? 0);
    $sl_tt = intval($_POST['soluongthuctexuat'] ?? 0);
    $dv = $_POST['donvitinh'];
    $dg = floatval($_POST['dongia'] ?? 0);
    $tt = $sl_tt * $dg; 


    $stmt = $conn->prepare("SELECT MaKeHang, DaChua FROM kehanghoa WHERE MaHangHoa = :mahh");
    $stmt->execute([':mahh' => $ma ]);
    $ke = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ke) {
        $_SESSION['error'] = "Hàng hóa chưa được gán kệ";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $ma = $_POST['mahanghoa'];

    
    $stmtHH = $conn->prepare("
        SELECT TenHangHoa, DonViTinh
        FROM hanghoa
        WHERE MaHangHoa = :ma
    ");
    $stmtHH->execute([':ma' => $ma]);
    $hh = $stmtHH->fetch(PDO::FETCH_ASSOC);

    if (!$hh) {
        $_SESSION['popup_error'] = true;
        $_SESSION['popup_message'] = "Hàng hóa không tồn tại!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $ma = $_POST['mahanghoa'];
    // KIỂM TRA HÀNG HÓA ĐÃ CÓ TRONG SESSION CHƯA
    if (!empty($_SESSION['XuatKho_items'])) {

        foreach ($_SESSION['XuatKho_items'] as $item) {

            if ($item['MaHangHoa'] === $ma) {

                $_SESSION['popup_error'] = true;
                $_SESSION['popup_message'] = "Hàng hóa $ma đã được tồn tại trong phiếu xuất,Bạn vui lòng xóa và nhập lại hàng hóa mới!";

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        }

    }
    $ten = $hh['TenHangHoa'];
    $daXuatTam = 0;
    $maKeHang = $ke['MaKeHang'];
   
    $DaChua = (int)$ke['DaChua'];

    if (!empty($_SESSION['XuatKho_items'])) {
        foreach ($_SESSION['XuatKho_items'] as $item) {
            if ($item['MaKeHang'] === $maKeHang) {
            $daXuatTam += $item['SoLuongThucTeXuat'];
            }
        }
    }
    
    $DaChuaThuc = $DaChua - $daXuatTam;
    // 3. KIỂM TRA SỨC CHỨA KỆ
    if ($sl_tt > $DaChuaThuc) {
    $_SESSION['popup_error'] = true;
    $_SESSION['popup_message'] =
        "Kệ hàng $maKeHang không đủ hàng hóa, không thể xuất hàng hóa,Vui lòng liên hệ thủ kho để biết thêm chi tiết !";
    }
    // Thêm vào session
    $_SESSION['XuatKho_items'][] = [
        'MaHangHoa' => $ma,
        'MaKeHang'  => $maKeHang,
        'TenHangHoa' => $ten,
        'DonViTinh' => $dv,
        'SoLuongTheoChungTu' => $sl_ct,
        'SoLuongThucTeXuat' => $sl_tt,
        'DonGia' => $dg,
        'ThanhTien' => $tt
    ];
    // LẤY THÔNG TIN CHUNG PHIẾU NHẬP TỪ FORM
    $soHoaDon      = $_POST['sohoadon'] ?? '';
    $NguoiNhanHang = $_POST['nguoinhanhang'] ?? '';
    $donVi         = $_POST['donvi'] ?? '';
    $boPhan        = $_POST['bophan'] ?? '';
    $diaChi        = $_POST['diachi'] ?? '';
    $thoiGian      = $_POST['thoigian'] ?? '';
    $XuatTaiKho    = $_POST['xuattaikho'] ?? '';
    $DiaDiem       = $_POST['diadiem'] ?? '';
    $LyDoXuat       = $_POST['lydoxuat'] ?? '';

    // LƯU VÀO SESSION (BỘ NHỚ TẠM)
    $_SESSION['PhieuXuat_items'] = [
        'SoHoaDon'      => $soHoaDon,
        'NguoiNhanHang' => $NguoiNhanHang,
        'DonVi'         => $donVi,
        'BoPhan'        => $boPhan,
        'DiaChi'        => $diaChi,
        'ThoiGian'      => $thoiGian,
        'XuatTaiKho'    => $XuatTaiKho,
        'DiaDiem'       => $DiaDiem,
        'LyDoXuat'      => $LyDoXuat
    ];
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
} 
// XỬ LÝ NÚT XÓA
if ( $_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['delete_item']) && isset($_POST['delete_index'])) {
    $index = $_POST['delete_index'];
    unset($_SESSION['XuatKho_items'][$index]);
    $_SESSION['XuatKho_items'] = array_values($_SESSION['XuatKho_items']);
}
//Chức năng LƯU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    try {
        $conn->beginTransaction();

        if (empty($_SESSION['XuatKho_items'])) {
            throw new Exception("Chưa có hàng xuất kho");
        }

        /* ========= 1. LẤY NHÂN VIÊN ========= */
        $stmtNV = $conn->prepare("
            SELECT MaNV FROM taikhoan WHERE MaTK = ?
        ");
        $stmtNV->execute([$_SESSION['MaTK']]);
        $maNV = $stmtNV->fetchColumn();

        if (!$maNV) {
            throw new Exception("Không xác định được nhân viên");
        }

        /* ========= 2. KIỂM TRA TỒN (TRƯỚC) ========= */
        foreach ($_SESSION['XuatKho_items'] as $item) {
            $stmtCheck = $conn->prepare("
                SELECT SUM(SoLuongTon)
                FROM tonkho_chitiet
                WHERE MaHangHoa = ? AND TrangThai = 1
                FOR UPDATE
            ");
            $stmtCheck->execute([$item['MaHangHoa']]);

            $tongTon = (int)$stmtCheck->fetchColumn();
            if ($tongTon < (int)$item['SoLuongThucTeXuat']) {
                throw new Exception("Không đủ tồn cho hàng {$item['TenHangHoa']}");
            }
        }
        $phieu = $_SESSION['PhieuXuat_items'];
        $stmtCheckPX = $conn->prepare("
            SELECT COUNT(*) 
            FROM phieuxuat 
            WHERE MaPhieuXuat = ?
        ");
        $stmtCheckPX->execute([$phieu['SoHoaDon']]);

        if ($stmtCheckPX->fetchColumn() > 0) {

            // Hoàn tác transaction nếu đã begin
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            // Gửi popup lỗi
            $_SESSION['popup_error']   = true;
            $_SESSION['popup_message'] = "Phiếu xuất {$phieu['SoHoaDon']} đã tồn tại!";

            // Quay lại trang
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        /* ========= 3. INSERT PHIẾU XUẤT ========= */
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $thoiGian = date('Y-m-d H:i:s');

        

        $stmtPX = $conn->prepare("
            INSERT INTO phieuxuat
            (MaPhieuXuat, ThoiGian, MaNhanVien, MaNPP, DonVi, BoPhan,
             DiaDiem, LyDo, XuatTaiKho, MaDonViVanChuyen,
             DiaChi, NguoiNhanHang)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmtPX->execute([
            $phieu['SoHoaDon'],
            $thoiGian,
            $maNV,
            'NPP001',
            $phieu['DonVi'],
            $phieu['BoPhan'],
            $phieu['DiaDiem'],
            $phieu['LyDoXuat'],
            $phieu['XuatTaiKho'],
            'DV001',
            $phieu['DiaChi'],
            $phieu['NguoiNhanHang']
        ]);

        /* ========= 4. INSERT CHI TIẾT ========= */
        $stmtCT = $conn->prepare("
            INSERT INTO chitietphieuxuat
            (MaPhieuXuat, MaHangHoa, SoLuongTheoChungTu,
             SoLuongThucTeXuat, DonViTinh, DonGia)
            VALUES (?,?,?,?,?,?)
        ");

        foreach ($_SESSION['XuatKho_items'] as $item) {
            $stmtCT->execute([
                $phieu['SoHoaDon'],
                $item['MaHangHoa'],
                $item['SoLuongTheoChungTu'],
                $item['SoLuongThucTeXuat'],
                $item['DonViTinh'],
                $item['DonGia']
            ]);
        }

        /* ========= 5. TRỪ TỒN THEO LÔ NHỎ ========= */
        foreach ($_SESSION['XuatKho_items'] as $item) {

            $canXuat = (int)$item['SoLuongThucTeXuat'];

            $stmtLo = $conn->prepare("
                SELECT MaCTTK, SoLuongTon
                FROM tonkho_chitiet
                WHERE MaHangHoa = ?
                  AND TrangThai = 1
                ORDER BY SoLuongTon ASC
                FOR UPDATE
            ");
            $stmtLo->execute([$item['MaHangHoa']]);

            while ($lo = $stmtLo->fetch(PDO::FETCH_ASSOC)) {
                if ($canXuat <= 0) break;

                if ($lo['SoLuongTon'] <= $canXuat) {
                // ✅ XUẤT HẾT → XÓA LUÔN LÔ
                $conn->prepare("
                    DELETE FROM tonkho_chitiet
                    WHERE MaCTTK = ?
                ")->execute([$lo['MaCTTK']]);

                $canXuat -= $lo['SoLuongTon'];
            } else {
                // ➖ XUẤT 1 PHẦN → TRỪ SỐ LƯỢNG
                $conn->prepare("
                    UPDATE tonkho_chitiet
                    SET SoLuongTon = SoLuongTon - ?
                    WHERE MaCTTK = ?
                ")->execute([$canXuat, $lo['MaCTTK']]);

                $canXuat = 0;
            }
            }

            /* ========= 6. UPDATE KỆ ========= */
            $conn->prepare("
                UPDATE kehanghoa
                SET DaChua = DaChua - ?
                WHERE MaHangHoa = ?
            ")->execute([
                $item['SoLuongThucTeXuat'],
                $item['MaHangHoa']
            ]);

            /* ========= 7. UPDATE TỒN TỔNG ========= */
            $stmtCon = $conn->prepare("
                SELECT SUM(SoLuongTon)
                FROM tonkho_chitiet
                WHERE MaHangHoa = ? AND TrangThai = 1
            ");
            $stmtCon->execute([$item['MaHangHoa']]);

            $conLai = (int)$stmtCon->fetchColumn();

            $conn->prepare("
                UPDATE tonkho
                SET TrangThai = ?
                WHERE MaHangHoa = ?
            ")->execute([
                $conLai > 0 ? 'Còn hàng' : 'Hết hàng',
                $item['MaHangHoa']
            ]);
        }

        /* ========= 8. HOÀN TẤT ========= */
        $conn->commit();

        unset($_SESSION['XuatKho_items'], $_SESSION['PhieuXuat_items']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();

    } catch (Exception $e) {
        $conn->rollBack();

        $_SESSION['popup_error']   = true;
        $_SESSION['popup_message'] = $e->getMessage();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    unset($_SESSION['XuatKho_items']);
    unset($_SESSION['PhieuXuat_items']);

    unset($_SESSION['popup_error'], $_SESSION['popup_message'], $_SESSION['error']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
$soHoaDon = taoMaPhieuXuat($conn);
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
        
            <div class="form-title" style="color: #007b36">PHIẾU XUẤT KHO</div>
        <form method="post" id="formHangHoa">
            <div class="section-box">
                <div class="section-title">Thông tin chung</div>
                <div class="form-group">
                    <div class="form-field">
                        <label for="sohoadon">Số hóa đơn</label>
                        <input type="text" id="sohoadon"  name="sohoadon"placeholder="PX001"  value="<?= $phieu['SoHoaDon'] ?? $soHoaDon ?>" readonly>
                    </div>
                    <div class="form-field">
                        <label for="nguoinhanhang">Người nhận hàng</label>
                        <input type="text" id="nguoinhanhang" name="nguoinhanhang" value="<?= $phieu['NguoiNhanHang'] ?? '' ?>" placeholder="Nguyễn Văn A" >
                    </div>

                    <div class="form-field">
                        <label for="donvi">Đơn vị</label>
                        <input type="text" id="donvi" name="donvi" value="<?= $phieu['DonVi'] ?? 'Phòng tài chính' ?>" style="width:100px;" placeholder="Phòng kế toán" readonly>
                    </div>
                    <div class="form-field">
                        <label for="diachi">Địa chỉ</label>
                        <select id="diachi" name="diachi" class="form-select">
                            <option value="">-- Chọn địa chỉ giao hàng --</option>

                            <?php foreach ($dsDiaChi as $dc): ?>
                                <option 
                                    value="<?= htmlspecialchars($dc['DiaChi']) ?>"
                                    <?= (isset($phieu['DiaChi']) && $phieu['DiaChi'] == $dc['DiaChi']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($dc['DiaChi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="bophan">Bộ phận</label>
                        <input type="text" id="bophan"  name="bophan" value="<?= $phieu['BoPhan'] ?? 'Xuất kho' ?>" style="width:100px;"placeholder="Nhập kho" readonly>
                    </div>
                   <?php
                    $thoiGianMacDinh = date('Y-m-d\TH:i'); 
                    ?>
                    <div class="form-field">
                        <label for="thoigian">Thời gian</label>
                        <input type="datetime-local"
                            id="thoigian"
                            name="thoigian"
                            value="<?= $phieu['ThoiGian'] ?? $thoiGianMacDinh ?>">
                    </div>
                    <div class="form-field">
                        <label for="xuattaikho">Xuất tại kho</label>
                        <select id="xuattaikho" name="xuattaikho" class="form-select">
                            <option value="">-- Chọn loại kho --</option>

                            <?php foreach ($dsLoaiKho as $kho): ?>
                                <option 
                                    value="<?= $kho['MaLoaiKho'] ?>"
                                    <?= (isset($phieu['XuatTaiKho']) && $phieu['XuatTaiKho'] == $kho['MaLoaiKho']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kho['TenLoaiKho']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-field">
                        <label for="diadiem">Địa điểm</label>
                        <input type="text" id="diadiem"  name="diadiem" value="<?= $phieu['DiaDiem'] ?? '' ?>" placeholder="Địa điểm kho">
                    </div>
                     <div class="form-field">
                        <label for="lydoxuat">Lý do xuất</label>
                        <input type="text" id="lydoxuat" name="lydoxuat" value="<?= $phieu['LyDoXuat'] ?? 'Thiếu hàng' ?>" placeholder="Thiếu hàng">
                    </div>
                </div>
            </div>

            <div class="section-box">
                <div class="section-title">Thông tin hàng hóa</div>
                    <div class="form-group">
                        <div class="form-field">
                            <label for="mahanghoa_view">Mã hàng hóa</label>
                            <input type="text" id="mahanghoa_view" readonly placeholder="HH001">
                            <label for="tonkho_view">Tồn kho hiện tại</label>
                            <input type="text" id="tonkho_view" readonly >
                        </div>
                        <div class="form-field">
                            <label for="soluongtheochungtu">Số lượng theo chứng từ</label>
                            <input type="number" id="soluongtheochungtu" name="soluongtheochungtu"placeholder="5">
                        </div>
                        <div class="form-field">
                            <label for="hanghoa">Hàng hóa</label>
                            <select id="hanghoa" name="mahanghoa" class="form-select">
                                <option value="">-- Chọn hàng hóa --</option>

                                <?php foreach ($dsHangHoa as $hh): 
                                    $tonKho = layTonKho($conn,$hh['MaHangHoa']);
                                ?>

                                <option 
                                    value="<?= $hh['MaHangHoa'] ?>"
                                    data-ton="<?= $tonKho ?>"
                                    data-ten="<?= htmlspecialchars($hh['TenHangHoa']) ?>"
                                >
                                    <?= htmlspecialchars($hh['TenHangHoa']) ?>
                                </option>

                                <?php endforeach; ?>
                            </select>
                        </div>
                        <script>
                            document.getElementById("hanghoa").addEventListener("change", function () {

                                let option = this.selectedOptions[0];

                                if (!option || this.value === "") {
                                    document.getElementById("mahanghoa_view").value = "";
                                    document.getElementById("tonkho_view").value = "";
                                    return;
                                }

                                document.getElementById("mahanghoa_view").value = this.value;

                                // lấy tồn kho từ data attribute
                                let tonKho = option.getAttribute("data-ton");

                                document.getElementById("tonkho_view").value = tonKho;

                            });
                            </script>
                        <div class="form-field">
                            <label for="soluongthuctexuat">Số lượng thực tế</label>
                            <input type="number" id="soluongthuctexuat"  min="0"name="soluongthuctexuat"placeholder="5">
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
                                //ràng buộc số lượng thực tế phải nhỏ hơn hoặc bằng số lượng theo chứng từ
                                    if (e.submitter && e.submitter.name === 'add_item') {
                                        let sl_ct = Number(document.getElementById("soluongtheochungtu").value);
                                        let sl_tt = Number(document.getElementById("soluongthuctexuat").value);
                                        
                                        if (sl_tt > sl_ct) {
                                            e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                            showPopup("Số lượng thực tế không được lớn hơn số lượng chứng từ ");
                                            return false;
                                        }
                                        
                                    }
                                    //Số hóa đơn không được để trống
                                    if (document.getElementById("sohoadon").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Số hóa đơn không được để trống");
                                        return false;
                                    }
                                    //Người giao hàng không được để trống
                                    if (document.getElementById("nguoinhanhang").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Người nhận hàng không được để trống");
                                        return false;
                                    }
                                    //Đơn vị không được để trống
                                    if (document.getElementById("donvi").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Đơn vị không được để trống");
                                        return false;
                                    }
                                    //Địa chỉ không được để trống
                                    if (document.getElementById("diachi").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Địa chỉ không được để trống");
                                        return false;
                                    }
                                    //Bộ phận không được để trống
                                    if (document.getElementById("bophan").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Bộ phận không được để trống");
                                        return false;
                                    }
                                    //Thời gian không được để trống
                                    if (document.getElementById("thoigian").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Thời gian không được để trống");
                                        return false;
                                    }
                                    //nhập tại kho không được để trống
                                    if (document.getElementById("xuattaikho").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Xuất tại kho không được để trống");
                                        return false;
                                    }
                                    //Địa điểm không được để trống
                                    if (document.getElementById("diadiem").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Địa điểm không được để trống");
                                        return false;
                                    }
                                    if (document.getElementById("lydoxuat").value.trim() === "") {
                                         e.preventDefault(); // CHẶN LỆNH GỬI FORM TẠI ĐÂY
                                        showPopup("Lý do xuất không được để trống");
                                        return false;
                                    }
                                        
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
                        <label for="donvitinh">Đơn vị tính</label>
                        <select id="donvitinh" name="donvitinh" class="form-select">
                            <option value="" selected>-- Chọn đơn vị --</option>
                            <option value="Kg">Kg</option>
                            <option value="Thùng">Thùng</option>
                            <option value="Hộp">Hộp</option>
                        </select>
                        </div>
                        <div class="form-field">
                            <label for="dongia_display">Đơn giá</label>
                            <input 
                                type="text"
                                id="dongia_display"
                                placeholder="140.000 đ"
                                oninput="handleDonGiaInput(this)"
                                onblur="formatDonGiaBlur(this)"
                            >
                            <input 
                                type="hidden"
                                id="dongia_value"
                                name="dongia"
                            >
                        </div>
                    </div>
                        <script>
                            function handleDonGiaInput(input) {
                                let raw = input.value.replace(/\D/g, '');
                                input.value = raw;
                                document.getElementById('dongia_value').value = raw;
                            }
                            function formatDonGiaBlur(input) {
                                let raw = input.value.replace(/\D/g, '');
                                if (raw === '') {
                                    input.value = '';
                                    return;
                                }
                                input.value = Number(raw).toLocaleString('vi-VN') + ' đ';
                            }
                        </script>
                    <div class="action-buttons">
                        <button type="submit" name="add_item" class="btn add">Thêm</button>
                        <input type="hidden" name="delete_index" id="delete_index">
                        <button type="submit" name="delete_item" class="btn delete">Xóa</button>
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
                            <th>Số lượng theo chứng từ</th>
                            <th>Số lượng thực tế</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        <?php foreach ($_SESSION['XuatKho_items'] as $i => $item): ?>
                            <tr class="clickable-row"
                                data-index="<?= $i ?>"
                                data-ma="<?= $item['MaHangHoa'] ?>"
                                data-ten="<?= $item['TenHangHoa'] ?>"
                                data-dvt="<?= $item['DonViTinh'] ?>"
                                data-slct="<?= $item['SoLuongTheoChungTu'] ?>"
                                data-sltte="<?= $item['SoLuongThucTeXuat'] ?>"
                                data-dongia="<?= $item['DonGia'] ?>">
                                
                                <td><?= $i+1 ?></td>
                                <td><?= $item['MaHangHoa'] ?></td>
                                <td><?= $item['TenHangHoa'] ?></td>
                                <td><?= $item['DonViTinh'] ?></td>
                                <td><?= $item['SoLuongTheoChungTu'] ?></td>
                                <td><?= $item['SoLuongThucTeXuat'] ?></td>
                                <td><?= number_format((float) str_replace(['.', ','], '', $item['DonGia']), 0, ',', '.') ?>đ</td>
                                <td><?= number_format((float) str_replace(['.', ','], '', $item['ThanhTien']), 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                document.querySelectorAll(".clickable-row").forEach(function(row) {
                                    row.addEventListener("click", function() {

                                        // Mã hàng hóa
                                        document.getElementById("mahanghoa_view").value = this.dataset.ma;

                                        // Select hàng hóa
                                        document.getElementById("hanghoa").value = this.dataset.ma;

                                        // Đơn vị tính
                                        document.getElementById("donvitinh").value = this.dataset.dvt;

                                        // Số lượng
                                        document.getElementById("soluongtheochungtu").value = this.dataset.slct;
                                        document.getElementById("soluongthuctexuat").value = this.dataset.sltte;

                                        // Đơn giá
                                         document.getElementById("dongia_display").value =
                                            Number(this.dataset.dongia).toLocaleString('vi-VN') + ' đ';

                                        document.getElementById("dongia_value").value = this.dataset.dongia;

                                        // index để xóa
                                        document.getElementById("delete_index").value = this.dataset.index;
                                    });
                                });
                            });
                        </script>
                </table>
            </div>
            <div class="summary-and-actions">
                <div class="summary-info">
                    <div class="summary-field">
                        <?php
                        $tongSo = count($_SESSION['XuatKho_items']);
                        $tongTien = 0;
                        foreach($_SESSION['XuatKho_items'] as $item){
                            // Loại bỏ dấu '.' hoặc ',' và ép kiểu float
                            $thanhTienSo = (float) str_replace([',', '.'], '', $item['ThanhTien']);
                            $tongTien += $thanhTienSo;
                        }
                        ?>
                        <label>Số lượng</label>
                        <input type="text" value="<?= $tongSo ?>" readonly>
                        <label> Tổng giá tiền</label>
                        <input type="text" value="<?= number_format($tongTien, 0, ',', '.')?> đ " readonly>
                </div>
                <div class="final-actions">
                    <button type="submit" name="save" class="btn save">LƯU</button>
                   <button type="submit" name="cancel" class="btn cancel">Hủy bỏ</button>
                   
                </div>
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




