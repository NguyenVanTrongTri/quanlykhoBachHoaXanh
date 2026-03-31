<?php 
session_start();
ob_start();

// Bật hiển thị lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../KetNoi/connect.php'; 
$conn = connectdb();

// 1. Lấy thông tin admin đang đăng nhập (Giữ nguyên logic của bạn)
$tenNguoiDung = 'Admin';
$avatarPath = "../../uploads/avatar/default.png";
if (isset($_SESSION['MaTK'])) {
    $sql_me = "SELECT tk.TenDangNhap, tk.Avatar, nv.ChucVu FROM taikhoan tk 
               LEFT JOIN nhanvien nv ON nv.MaNV = tk.MaNV WHERE tk.MaTK = ? LIMIT 1";
    $stmt_me = $conn->prepare($sql_me);
    $stmt_me->execute([$_SESSION['MaTK']]);
    $me = $stmt_me->fetch(PDO::FETCH_ASSOC);
    if ($me) {
        $tenNguoiDung = $me['TenDangNhap'];
        $avatarPath = "../../uploads/avatar/" . (!empty($me['Avatar']) ? $me['Avatar'] : 'default.png');
    }
}

/**
 * 2. XỬ LÝ DATABASE: LƯU & XÓA TRÊN BẢNG PHANQUYEN
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenCu = $_POST['quyen_id'] ?? ''; 
    $tenMoi = trim($_POST['txtTenQuyen'] ?? '');
    $selectedFuncs = $_POST['role_func'] ?? [];
    $inTransaction = false; // Biến cờ để kiểm tra transaction

    try {
        // --- CHỨC NĂNG THÊM MỚI ---
        if (isset($_POST['add_permission']) && !empty($tenMoi)) {
            $conn->beginTransaction();
            $inTransaction = true;

            // Xóa tạm nếu đã tồn tại tên này để tránh trùng
            $stmtDel = $conn->prepare("DELETE FROM phanquyen WHERE TenChucVu = ?");
            $stmtDel->execute([$tenMoi]);

            $stmtIns = $conn->prepare("INSERT INTO phanquyen (TenChucVu, ChucNang) VALUES (?, ?)");
            if (!empty($selectedFuncs)) {
                foreach ($selectedFuncs as $func) { $stmtIns->execute([$tenMoi, $func]); }
            } else {
                $stmtIns->execute([$tenMoi, '']);
            }
            $conn->commit();
            $_SESSION['success'] = "Đã thêm chức vụ $tenMoi thành công!";
            header("Location: permission.php"); exit;
        }

        // --- CHỨC NĂNG LƯU (CẬP NHẬT) ---
        if (isset($_POST['save_permission']) && !empty($tenMoi)) {
            $conn->beginTransaction();
            $inTransaction = true;

            // Cập nhật tên ở bảng nhân viên nếu có đổi tên
            if (!empty($tenCu) && $tenCu !== $tenMoi) {
                $stmtUpNv = $conn->prepare("UPDATE nhanvien SET ChucVu = ? WHERE ChucVu = ?");
                $stmtUpNv->execute([$tenMoi, $tenCu]);
            }

            // Xóa quyền cũ của tên cũ (hoặc tên mới)
            $target = !empty($tenCu) ? $tenCu : $tenMoi;
            $stmtDel = $conn->prepare("DELETE FROM phanquyen WHERE TenChucVu = ?");
            $stmtDel->execute([$target]);

            $stmtIns = $conn->prepare("INSERT INTO phanquyen (TenChucVu, ChucNang) VALUES (?, ?)");
            if (!empty($selectedFuncs)) {
                foreach ($selectedFuncs as $func) { $stmtIns->execute([$tenMoi, $func]); }
            } else {
                $stmtIns->execute([$tenMoi, '']);
            }

            $conn->commit();
            $_SESSION['success'] = "Đã lưu thay đổi cho $tenMoi thành công!";
            header("Location: permission.php"); exit;
        }

    } catch (Exception $e) {
        // CHỈ ROLLBACK KHI TRANSACTION ĐANG MỞ
        if ($inTransaction && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        header("Location: permission.php"); exit;
    }
}
// B. CHỨC NĂNG XÓA CHỨC VỤ
if (isset($_POST['delete_permission'])) {
    // Lấy tên chức vụ cần xóa từ input ẩn 'quyen_id'
    $tenCanXoa = $_POST['quyen_id'] ?? ''; 

    if (!empty($tenCanXoa)) {
        try {
            $conn->beginTransaction();

            // Bước 1: Xóa tất cả các quyền liên quan đến chức vụ này trong bảng phanquyen
            $stmt = $conn->prepare("DELETE FROM phanquyen WHERE TenChucVu = ?");
            $stmt->execute([$tenCanXoa]);

            // Bước 2 (Tùy chọn): Cập nhật những nhân viên đang giữ chức vụ này về trống hoặc 'Chưa phân quyền'
            // Điều này giúp tránh lỗi logic khi nhân viên đăng nhập
            $stmtUpdateNV = $conn->prepare("UPDATE nhanvien SET ChucVu = '' WHERE ChucVu = ?");
            $stmtUpdateNV->execute([$tenCanXoa]);

            $conn->commit();
            $_SESSION['success'] = "Đã xóa chức vụ $tenCanXoa thành công!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['error'] = "Lỗi khi xóa: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Vui lòng chọn một chức vụ từ danh sách để xóa!";
    }
    
    // Load lại trang để cập nhật danh sách
    header("Location: permission.php");
    exit;
}
// 3. TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ
// Lấy danh sách chức vụ duy nhất từ bảng phanquyen
$dsQuyenDB = $conn->query("SELECT DISTINCT TenChucVu FROM phanquyen ORDER BY TenChucVu ASC")->fetchAll(PDO::FETCH_ASSOC);

// Lấy toàn bộ chi tiết để JS xử lý tick checkbox nhanh
$chiTietQuyen = $conn->query("SELECT TenChucVu, ChucNang FROM phanquyen")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Chức Vụ | BHX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bhx-green: #00923F; --bhx-yellow: #ffbc00; --bg-light: #f4f7f6; --sidebar-width: 280px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--bg-light); overflow: hidden; }
        .admin-container { display: flex; height: 100vh; width: 100vw; }
        .admin-sidebar { width: var(--sidebar-width); background: #fff; border-right: 1px solid #ddd; padding: 20px; display: flex; flex-direction: column; height: 100vh; flex-shrink: 0; }
        .admin-brand { font-size: 20px; font-weight: bold; color: var(--bhx-green); text-align: center; margin-bottom: 30px; border-bottom: 2px solid var(--bhx-yellow); padding-bottom: 10px; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px; background: #f9f9f9; border-radius: 10px; margin-bottom: 25px; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--bhx-green); }
        .admin-menu-item { display: flex; align-items: center; gap: 10px; padding: 12px 15px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 5px; font-size: 14px; transition: 0.3s; }
        .admin-menu-item i { width: 20px; text-align: center; }
        .admin-menu-item:hover, .admin-menu-item.active { background: var(--bhx-green); color: #fff !important; }
        .admin-main { flex: 1; overflow-y: auto; padding: 30px; }
        .form-section { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 5px solid var(--bhx-yellow); }
        .input-group label { display: block; font-size: 12px; color: #777; margin-bottom: 8px; font-weight: bold; }
        .input-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 5px; }
        .btn-save { background: var(--bhx-green); }
        .btn-delete { background: #c0392b; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .row-select:hover { background: #fffde7; cursor: pointer; }
        .active-row { background: #fff9c4 !important; }
        .new-feature-section { margin-top: 25px; padding: 15px; background: #f9fdfa; border: 1px dashed var(--bhx-green); border-radius: 8px; }
        .permission-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; }
        .check-item { display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer; padding: 5px; border-radius: 4px; transition: 0.2s; }
        .check-item:hover { background: #e8f5e9; }
        .check-item input { width: 17px; height: 17px; cursor: pointer; }
        .label-title { font-weight: bold; color: var(--bhx-green); font-size: 14px; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="admin-brand">BHX - QUẢN TRỊ</div>
        <div class="user-profile">
            <img src="<?= $avatarPath ?>" class="user-avatar">
            <div>
                <div style="font-weight:bold; font-size:14px;"><?= htmlspecialchars($tenNguoiDung) ?></div>
                <div style="font-size:11px; color: var(--bhx-green);">
                    <div>Administrator</div>
                </div>
            </div>
        </div>
        <div style="flex: 1;">
            <a href="../admin.php" class="admin-menu-item"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="../users/account.php" class="admin-menu-item"><i class="fa-solid fa-users-gear"></i> Tài khoản</a>
            <a href="permission.php" class="admin-menu-item active"><i class="fa-solid fa-user-shield"></i> Phân quyền</a>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
            <a href="../../TonKho/ThongKeHangTon.php" class="admin-menu-item"><i class="fa-solid fa-box-open"></i> Tồn kho</a>
            <a href="../NhatKyHeThong.php" class="admin-menu-item"><i class="fa-solid fa-clipboard-list"></i> Nhật ký</a>
        </div>
        <a href="../../Login.php" class="admin-menu-item" style="color:#e74c3c;"><i class="fa-solid fa-power-off"></i> Đăng xuất</a>
    </div>

    <div class="admin-main">
        <h2>Thiết lập Quyền Chức vụ (Database)</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-section">
            <form method="post" id="permissionForm">
                <input type="hidden" id="quyen_id" name="quyen_id">
                <div class="input-group">
                    <label>Tên chức vụ quản lý</label>
                    <input type="text" id="txtTenQuyen" name="txtTenQuyen" required placeholder="Nhập tên chức vụ mới hoặc chọn từ danh sách...">
                </div>

                <div class="new-feature-section">
                    <span class="label-title"><i class="fa-solid fa-key"></i> Gán quyền truy cập:</span>
                    <div class="permission-grid">
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="NhapKho"> Nhập Kho</label>
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="XuatKho"> Xuất Kho</label>
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="TonKho"> Tồn Kho</label>
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="HangHoa"> Hàng Hóa</label>
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="NhanVien"> Nhân Viên</label>
                        <label class="check-item"><input type="checkbox" name="role_func[]" value="TaiKhoan"> Tài Khoản</label>
                    </div>
                </div>

                
                <div class="btn-group">
                <button type="submit" name="add_permission" class="btn" style="background: #27ae60;">
                    <i class="fa-solid fa-plus"></i> Thêm chức vụ
                </button>

                <button type="submit" name="save_permission" class="btn btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                </button>
                
                <button type="submit" name="delete_permission" class="btn btn-delete" onclick="return confirm('Xóa chức vụ này?')">
                    <i class="fa-solid fa-trash-can"></i> Xóa
                </button>

                <button type="button" class="btn" style="background: #7f8c8d;" onclick="window.location.href='permission.php'">
                    <i class="fa-solid fa-xmark"></i> Hủy bỏ
                </button>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr style="background: #f8f9fa;">
                    <th>Chức vụ (Trong bảng Phân Quyền)</th>
                    <th>Số lượng quyền được gán</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dsQuyenDB as $q): 
                    $count = 0;
                    foreach($chiTietQuyen as $item) {
                        // Chỉ đếm nếu tên chức vụ khớp VÀ ChucNang không phải là chuỗi rỗng
                        if($item['TenChucVu'] == $q['TenChucVu'] && !empty($item['ChucNang'])) {
                            $count++;
                        }
                    }
                ?>
                <tr class="row-select" data-name="<?= htmlspecialchars($q['TenChucVu']) ?>">
                    <td><b><?= htmlspecialchars($q['TenChucVu']) ?></b></td>
                    <td><span style="background: #e8f5e9; color: var(--bhx-green); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;"><?= $count ?> quyền</span></td>
                    <td style="color: var(--bhx-green); font-size: 12px;"><i class="fa-solid fa-circle-check"></i> Đang áp dụng</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Dữ liệu chi tiết từ PHP để JS xử lý tick checkbox
const chiTietQuyen = <?php echo json_encode($chiTietQuyen); ?>;
const checkboxes = document.querySelectorAll('input[name="role_func[]"]');
const quyenIdInput = document.getElementById("quyen_id");
const tenQuyenInput = document.getElementById("txtTenQuyen");

// 1. CHỨC NĂNG THÊM MỚI: Reset form về trạng thái trống
function resetPermissionForm() {
    // Xóa ID cũ để PHP hiểu đây là thêm mới (hoặc không bị ghi đè nhầm)
    quyenIdInput.value = "";
    
    // Xóa tên chức vụ trong ô nhập
    tenQuyenInput.value = "";
    tenQuyenInput.placeholder = "Nhập tên chức vụ mới tại đây...";
    
    // Bỏ tích tất cả checkbox quyền
    checkboxes.forEach(chk => chk.checked = false);
    
    // Xóa hiệu ứng dòng đang chọn ở bảng bên dưới
    document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
    
    // Tự động nhảy con trỏ chuột vào ô nhập tên cho tiện
    tenQuyenInput.focus();
}

// 2. CHỨC NĂNG SỬA: Khi click vào một dòng trong bảng
document.querySelectorAll(".row-select").forEach(row => {
    row.addEventListener("click", function() {
        const name = this.dataset.name;
        
        // Điền dữ liệu vào form
        quyenIdInput.value = name;
        tenQuyenInput.value = name;
        
        // Hiệu ứng dòng active (đổi màu dòng được chọn)
        document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
        this.classList.add("active-row");

        // Reset toàn bộ checkbox trước khi tick cái mới
        checkboxes.forEach(chk => chk.checked = false);

        // Lọc và Tick các quyền tương ứng của chức vụ này
        const selectedPermissions = chiTietQuyen.filter(p => p.TenChucVu === name);
        selectedPermissions.forEach(p => {
            const chk = document.querySelector(`input[value="${p.ChucNang}"]`);
            if (chk) chk.checked = true;
        });
    });
});
</script>
</body>
</html>