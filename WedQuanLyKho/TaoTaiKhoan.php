<?php
include 'KetNoi/connect.php';
$conn = connectdb();

$thongbao = "";
// Skin hình nền đồng bộ (Sau này quản lý qua permission.php)
$background_skin = "images/binhngo.png"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $repass   = $_POST["repass"] ?? "";
    $gender   = $_POST["gender"] ?? "";
    $email    = trim($_POST["email"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");

    // Hàm tạo ID tự động (Giữ nguyên logic của Trọng)
    function generateNewId($conn, $table, $col, $prefix) {
        $stmt = $conn->query("SELECT $col FROM $table ORDER BY $col DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();
        $num = $lastId ? (int)substr($lastId, strlen($prefix)) + 1 : 1;
        
        $newId = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
        $exists = $conn->prepare("SELECT COUNT(*) FROM $table WHERE $col = ?");
        while (true) {
            $exists->execute([$newId]);
            if ($exists->fetchColumn() == 0) break;
            $num++;
            $newId = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return $newId;
    }

    if ($password !== $repass) {
        $thongbao = "Mật khẩu nhập lại không khớp!";
    } else {
        try {
            $sql_check = $conn->prepare("SELECT COUNT(*) FROM taikhoan WHERE TenDangNhap = ?");
            $sql_check->execute([$username]);
            
            if ($sql_check->fetchColumn() > 0) {
                $thongbao = "Tên đăng nhập đã tồn tại!";
            } else {
                $conn->beginTransaction();

                $MaNV = generateNewId($conn, 'nhanvien', 'MaNV', 'NV');
                $MaTK = generateNewId($conn, 'taikhoan', 'MaTK', 'TK');
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);

                $sql_insert_nv = $conn->prepare("INSERT INTO nhanvien (MaNV, HoTen, GioiTinh, SDT, Email, MaLoaiNV, ChucVu, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $sql_insert_nv->execute([$MaNV, $username, $gender, $phone, $email, 'LNV01', 'Nhân viên', 'Đang làm']);

                $sql_insert_tk = $conn->prepare("INSERT INTO taikhoan (MaTK, MaNV, TenDangNhap, MatKhau, GioiTinh, Email, SDT) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $sql_insert_tk->execute([$MaTK, $MaNV, $username, $hashPassword, $gender, $email, $phone]);

                $conn->commit();
                header("Location: Login.php?register=success");
                exit();
            }
        } catch (Exception $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            $thongbao = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản - Bách Hóa Xanh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-image: url('<?= $background_skin ?>');
            --primary-color: #00923F;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-image) no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .register-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 35px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .title {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        label { font-weight: bold; display: block; margin-bottom: 6px; color: #333; font-size: 14px; }

        input, select {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            background: white;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 8px rgba(0, 146, 63, 0.2);
        }

        .password-wrapper { position: relative; }
        .password-wrapper i {
            position: absolute;
            right: 12px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .inline-box { display: flex; gap: 15px; }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #007a34;
            transform: translateY(-2px);
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #333;
            text-decoration: none;
        }

        .login-link b { color: var(--primary-color); }

        .error-msg {
            color: #d32f2f;
            background: #fdecea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="register-box">
    <div class="title">TẠO TÀI KHOẢN</div>

    <form method="post">
        <label>Tên đăng nhập</label>
        <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required>

        <div class="inline-box">
            <div style="flex:2;">
                <label>Mật khẩu</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Mật khẩu..." required>
                    <i class="fa fa-eye" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div style="flex:1;">
                <label>Giới tính</label>
                <select name="gender">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>
        </div>

        <label>Nhập lại mật khẩu</label>
        <div class="password-wrapper">
            <input type="password" name="repass" id="repass" placeholder="Xác nhận mật khẩu..." required>
            <i class="fa fa-eye" onclick="togglePassword('repass', this)"></i>
        </div>

        <label>Email</label>
        <input type="email" name="email" placeholder="Ví dụ: example@gmail.com" required>

        <label>Số điện thoại</label>
        <input type="text" name="phone" placeholder="Nhập số điện thoại..." required>

        <button class="btn-submit" type="submit">ĐĂNG KÝ HỆ THỐNG</button>

        <?php if (!empty($thongbao)) : ?>
            <div class="error-msg"><?= $thongbao ?></div>
        <?php endif; ?>

        <a href="Login.php" class="login-link">Đã có tài khoản? <b>Đăng nhập ngay</b></a>
    </form>
</div>

<script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
</body>
</html>