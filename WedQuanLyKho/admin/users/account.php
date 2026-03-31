<?php 
session_start();

/** * 1. CẤU HÌNH ĐƯỜNG DẪN 
 */
include '../../KetNoi/connect.php'; 
$conn = connectdb();


// Lấy thông tin admin đang đăng nhập
$sql_me = "SELECT tk.TenDangNhap, tk.Avatar, nv.ChucVu FROM taikhoan tk 
           LEFT JOIN nhanvien nv ON nv.MaNV = tk.MaNV WHERE tk.MaTK = ? LIMIT 1";
$stmt_me = $conn->prepare($sql_me);
$me = $stmt_me->fetch(PDO::FETCH_ASSOC);

$tenNguoiDung = $me['TenDangNhap'] ?? 'Admin';
$avatarPath = "../../uploads/avatar/" . (!empty($me['Avatar']) ? $me['Avatar'] : 'default.png');

/**
 * 2. XỬ LÝ DỮ LIỆU
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maTK = $_POST['matkhoan_id'] ?? '';

    // Cập nhật tài khoản (Đổi mật khẩu hoặc quyền)
    if (isset($_POST['edit_user']) && !empty($maTK)) {
        try {
            $newPass = $_POST['password'];
            if (!empty($newPass)) {
                // Nếu có nhập mật khẩu mới thì cập nhật cả mật khẩu
                $sql = "UPDATE taikhoan SET TenDangNhap = :user, MatKhau = :pass WHERE MaTK = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':user' => $_POST['username'],
                    ':pass' => $newPass, // Nên dùng password_hash nếu hệ thống có bảo mật cao
                    ':id'   => $maTK
                ]);
            } else {
                // Chỉ cập nhật tên đăng nhập
                $sql = "UPDATE taikhoan SET TenDangNhap = :user WHERE MaTK = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user' => $_POST['username'], ':id' => $maTK]);
            }
            $_SESSION['success'] = "Cập nhật tài khoản ID: $maTK thành công!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        header("Location: users.php");
        exit;
    }

    // Xóa tài khoản
    if (isset($_POST['delete_user']) && !empty($maTK)) {
        $sql = "DELETE FROM taikhoan WHERE MaTK = ?";
        $conn->prepare($sql)->execute([$maTK]);
        $_SESSION['success'] = "Đã xóa tài khoản ID: $maTK.";
        header("Location: users.php");
        exit;
    }
}

// Lấy danh sách tài khoản kèm thông tin nhân viên
$query = "SELECT tk.*, nv.HoTen, nv.ChucVu, nv.Email 
          FROM taikhoan tk 
          LEFT JOIN nhanvien nv ON tk.MaNV = nv.MaNV 
          ORDER BY tk.MaTK DESC";
$listUsers = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Tài Khoản Người Dùng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
    /* Tận dụng lại Style từ admin.php tổng của bạn */
    :root { 
        --bhx-green: #00923F; 
        --bhx-yellow: #ffbc00; 
        --bg-light: #f4f7f6; 
        --sidebar-width: 280px; /* Định nghĩa chiều rộng cố định */
    }

    /* QUAN TRỌNG: Ép mọi phần tử tính toán kích thước bao gồm cả padding */
    * { 
        box-sizing: border-box; 
    }

    body { 
        margin: 0; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background: var(--bg-light); 
        overflow: hidden; /* Ngăn cuộn toàn trang để sidebar cố định */
    }

    .admin-container { 
        display: flex; 
        height: 100vh; 
        width: 100vw;
    }
    
    /* SIDEBAR: Khóa cứng kích thước để không bị lệch giữa các trang */
    .admin-sidebar { 
        width: var(--sidebar-width); 
        min-width: var(--sidebar-width); /* Chống co giãn */
        max-width: var(--sidebar-width);
        background: #fff; 
        border-right: 1px solid #ddd; 
        padding: 20px; 
        display: flex; 
        flex-direction: column; 
        height: 100vh;
        flex-shrink: 0; /* Không cho phép nội dung bên phải đẩy lùi sidebar */
    }

    .admin-brand { 
        font-size: 20px; 
        font-weight: bold; 
        color: var(--bhx-green); 
        text-align: center; 
        margin-bottom: 30px; 
        border-bottom: 2px solid var(--bhx-yellow); 
        padding-bottom: 10px; 
        flex-shrink: 0;
    }

    .user-profile { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        padding: 15px; 
        background: #f9f9f9; 
        border-radius: 10px; 
        margin-bottom: 25px; 
        flex-shrink: 0;
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

    /* Đảm bảo icon thẳng hàng y hệt admin.php */
    .admin-menu-item i { 
        width: 20px; 
        text-align: center; 
    }

    .admin-menu-item:hover, .admin-menu-item.active { 
        background: var(--bhx-green); 
        color: #fff !important; 
    }

    /* Main Content: Cho phép cuộn độc lập */
    .admin-main { 
        flex: 1; 
        overflow-y: auto; 
        padding: 30px; 
    }

    .form-section { 
        background: #fff; 
        padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        margin-bottom: 30px; 
        border-top: 5px solid var(--bhx-yellow); 
    }

    .grid-inputs { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 20px; 
        margin-bottom: 20px; 
    }

    .input-group label { 
        display: block; 
        font-size: 12px; 
        color: #777; 
        margin-bottom: 8px; 
        font-weight: bold; 
    }

    .input-group input { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid #ddd; 
        border-radius: 8px; 
    }
    
    .btn-group { display: flex; gap: 10px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: #fff; transition: 0.3s; }
    .btn-edit { background: #2980b9; }
    .btn-delete { background: #c0392b; }
    
    /* Table */
    .table-container { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9fa; padding: 15px; text-align: left; font-size: 13px; color: #666; }
    td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
    .row-select:hover { background: #fffde7; cursor: pointer; }
    .active-row { background: #fff9c4 !important; }
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
        <a href="../admin.php" class="admin-menu-item">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <a href="account.php" class="admin-menu-item active">
            <i class="fa-solid fa-users-gear"></i> Tài khoản
        </a>
        <a href="../permission/permission.php" class="admin-menu-item">
            <i class="fa-solid fa-user-shield"></i> Phân quyền
        </a>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
        
        <a href="../../TonKho/ThongKeHangTon.php" class="admin-menu-item">
            <i class="fa-solid fa-box-open"></i> Tồn kho
        </a>
        <a href="../NhatKyHeThong.php" class="admin-menu-item">
            <i class="fa-solid fa-clipboard-list"></i> Nhật ký
        </a>
    </div>

    <a href="../../../Login.php" class="admin-menu-item" style="color:#e74c3c;">
        <i class="fa-solid fa-power-off"></i> Đăng xuất
    </a>
</div>

    <div class="admin-main">
        <h2>Quản lý tài khoản đăng nhập</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <form method="post" id="userForm">
                <input type="hidden" id="matkhoan_id" name="matkhoan_id">
                <div class="grid-inputs">
                    <div class="input-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group">
                        <label>Mật khẩu mới (Để trống nếu không đổi)</label>
                        <input type="password" id="password" name="password" placeholder="********">
                    </div>
                    <div class="input-group">
                        <label>Chủ sở hữu (Nhân viên)</label>
                        <input type="text" id="owner" readonly style="background:#f0f0f0;">
                    </div>
                    <div class="input-group">
                        <label>Chức vụ</label>
                        <input type="text" id="role" readonly style="background:#f0f0f0;">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="submit" name="edit_user" class="btn btn-edit"><i class="fa-solid fa-save"></i> Cập nhật tài khoản</button>
                    <button type="submit" name="delete_user" class="btn btn-delete" onclick="return confirm('Xóa tài khoản này?')"><i class="fa-solid fa-trash"></i> Xóa tài khoản</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Đăng Nhập</th>
                        <th>Nhân Viên</th>
                        <th>Chức Vụ</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listUsers as $u): ?>
                    <tr class="row-select" 
                        data-id="<?= $u['MaTK'] ?>"
                        data-user="<?= htmlspecialchars($u['TenDangNhap']) ?>"
                        data-owner="<?= htmlspecialchars($u['HoTen']) ?>"
                        data-role="<?= $u['ChucVu'] ?>">
                        <td><?= $u['MaTK'] ?></td>
                        <td><b><?= htmlspecialchars($u['TenDangNhap']) ?></b></td>
                        <td><?= htmlspecialchars($u['HoTen'] ?? 'Chưa liên kết') ?></td>
                        <td><?= $u['ChucVu'] ?></td>
                        <td><?= $u['Email'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.querySelectorAll(".row-select").forEach(row => {
    row.addEventListener("click", function() {
        document.getElementById("matkhoan_id").value = this.dataset.id;
        document.getElementById("username").value = this.dataset.user;
        document.getElementById("owner").value = this.dataset.owner;
        document.getElementById("role").value = this.dataset.role;

        document.querySelectorAll(".row-select").forEach(r => r.classList.remove("active-row"));
        this.classList.add("active-row");
    });
});
</script>
</body>
</html>