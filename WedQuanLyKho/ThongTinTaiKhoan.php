<?php
// 1. LUÔN LUÔN ĐỂ LOGIC XỬ LÝ LÊN ĐẦU FILE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'KetNoi/connect.php';
$conn = connectdb();

$user = null;
if (isset($_SESSION['MaTK'])) {
    $stmt = $conn->prepare("
        SELECT tk.TenDangNhap, tk.Email, nv.HoTen, nv.ChucVu, nv.NgaySinh, nv.GioiTinh, nv.TrangThai,
               nv.DiaChi, nv.GhiChu, nv.SDT, tk.Avatar
        FROM taikhoan tk
        INNER JOIN nhanvien nv ON tk.MaNV = nv.MaNV
        WHERE tk.MaTK = ?
    ");
    $stmt->execute([$_SESSION['MaTK']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Các hàm bổ trợ
if (!function_exists('toVNDate')) {
    function toVNDate($date) {
        return (!empty($date)) ? date("d/m/Y", strtotime($date)) : "";
    }
}

if (!function_exists('convertDateToMySQL')) {
    function convertDateToMySQL($dateInput) {
        if (empty($dateInput)) return null;
        if (preg_match('/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/', $dateInput, $m)) {
            return $m[3] . "-" . $m[2] . "-" . $m[1];
        }
        return false;
    }
}

// 2. XỬ LÝ POST (Trước khi có bất kỳ dòng HTML nào)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_info']) && isset($_SESSION['MaTK'])) {
    $hoTen    = $_POST['fullname'] ?? '';
    $ngaySinh = convertDateToMySQL($_POST['dob']);
    $gioiTinh = $_POST['gender'] ?? '';
    $diaChi   = $_POST['address'] ?? '';
    $sdt      = $_POST['phone'] ?? '';
    $email    = $_POST['email'] ?? '';
    $ghiChu   = $_POST['notes'] ?? '';

    try {
        $stmtNV = $conn->prepare("SELECT MaNV FROM taikhoan WHERE MaTK = ?");
        $stmtNV->execute([$_SESSION['MaTK']]);
        $maNV = $stmtNV->fetchColumn();

        if ($maNV) {
            // Cập nhật Nhân Viên
            $sqlNV = "UPDATE nhanvien SET HoTen=?, NgaySinh=?, GioiTinh=?, DiaChi=?, SDT=?, Email=?, GhiChu=? WHERE MaNV=?";
            $conn->prepare($sqlNV)->execute([$hoTen, $ngaySinh, $gioiTinh, $diaChi, $sdt, $email, $ghiChu, $maNV]);
            
            // Cập nhật Tài Khoản
            $conn->prepare("UPDATE taikhoan SET Email=? WHERE MaTK=?")->execute([$email, $_SESSION['MaTK']]);

            // Xử lý Avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $newName = "avatar_" . $_SESSION['MaTK'] . "_" . time() . "." . $ext;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/avatar/";
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newName)) {
                    if (!empty($user['Avatar']) && $user['Avatar'] !== 'default.png') {
                        @unlink($uploadDir . $user['Avatar']);
                    }
                    $conn->prepare("UPDATE taikhoan SET Avatar=? WHERE MaTK=?")->execute([$newName, $_SESSION['MaTK']]);
                }
            }
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } catch (Exception $e) {
        $errorMsg = "Lỗi: " . $e->getMessage();
    }
}

// 3. CHỈ XUẤT HTML KHI CÓ BIẾN ALLOW_RENDER
if (!defined('ALLOW_RENDER')) return; 
?>

<div class="modal-overlay" id="modalOverlay" onclick="closeUserModal()"></div>
<div class="modal" id="userModal">
    <form method="post" enctype="multipart/form-data">
        <div class="modal-header">
            <h2>THÔNG TIN TÀI KHOẢN</h2>
            <span class="modal-close" onclick="closeUserModal()">&times;</span>
        </div>

        <div class="modal-body">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    <?php $avatar = !empty($user['Avatar']) ? $user['Avatar'] : 'default.png'; ?>
                    <img id="avatarImg" src="/uploads/avatar/<?= htmlspecialchars($avatar) ?>" alt="Avatar">
                    <div class="avatar-edit-icon" onclick="document.getElementById('avatarInput').click()">
                        📷
                    </div>
                </div>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="previewImg(this)">
                <h3 class="display-name"><?= htmlspecialchars($user['TenDangNhap'] ?? 'Khách') ?></h3>
                <span class="badge-role"><?= htmlspecialchars($user['ChucVu'] ?? 'Thành viên') ?></span>
            </div>

            <div class="info-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" value="<?= htmlspecialchars($user['TenDangNhap'] ?? '') ?>" readonly class="readonly-input">
                    </div>
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['HoTen'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['SDT'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['Email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Ngày sinh (DD/MM/YYYY)</label>
                        <input type="text" name="dob" value="<?= toVNDate($user['NgaySinh'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gender">
                            <option value="Nam" <?= ($user['GioiTinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
                            <option value="Nữ" <?= ($user['GioiTinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['DiaChi'] ?? '') ?>">
                </div>

                <div class="form-group full-width">
                    <label>Ghi chú cá nhân</label>
                    <textarea name="notes" rows="2"><?= htmlspecialchars($user['GhiChu'] ?? '') ?></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="save_info" class="btn-save">LƯU THAY ĐỔI</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* CSS NÂNG CẤP - MODERN UI */
#userModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 850px;
    max-width: 95vw;
    max-height: 90vh;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    z-index: 2000;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    overflow-y: auto;
    border: 1px solid rgba(255,255,255,0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 { margin: 0; color: #00923F; font-size: 1.4rem; }

.modal-close {
    font-size: 28px;
    cursor: pointer;
    color: #999;
}

.modal-body {
    display: flex;
    flex-wrap: wrap;
    padding: 25px;
    gap: 30px;
}

/* Avatar Styling */
.avatar-section {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-right: 1px solid #eee;
}

.avatar-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin-bottom: 15px;
}

#avatarImg {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.avatar-edit-icon {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: #00923F;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid #fff;
}

.badge-role {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

/* Form Styling */
.info-section { flex: 3; min-width: 300px; }

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group { margin-bottom: 15px; }
.form-group.full-width { grid-column: span 2; }

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: bold;
    color: #666;
    margin-bottom: 5px;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fdfdfd;
}

.readonly-input { background: #f0f0f0 !important; cursor: not-allowed; }

.btn-save {
    background: #00923F;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: 0.3s;
}

.btn-save:hover { background: #007b36; transform: translateY(-2px); }

/* MOBILE RESPONSIVE */
@media (max-width: 768px) {
    .modal-body { flex-direction: column; }
    .avatar-section { border-right: none; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full-width { grid-column: span 1; }
}
</style>

<script>
function closeUserModal() {
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('userModal').style.display = 'none';
}

function previewImg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarImg').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>