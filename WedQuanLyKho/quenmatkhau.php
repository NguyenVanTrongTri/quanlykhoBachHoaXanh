<?php
include 'KetNoi/connect.php';
$conn = connectdb();

$thongbao = "";
$success = false;

// Cấu hình skin hình nền (Sau này sẽ lấy từ permission.php)
$background_skin = "images/binhngo.png"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");

    try {
        $stmt = $conn->prepare("SELECT MaTK FROM taikhoan WHERE TenDangNhap = ? AND Email = ? LIMIT 1");
        $stmt->execute([$username, $email]);
        $user = $stmt->fetch();

        if ($user) {
            $newPasswordRaw = "123456"; 
            $hashNewPassword = password_hash($newPasswordRaw, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE taikhoan SET MatKhau = ? WHERE TenDangNhap = ?");
            $update->execute([$hashNewPassword, $username]);

            $success = true;
            $thongbao = "Khôi phục thành công! Mật khẩu mới là: <strong>$newPasswordRaw</strong>";
        } else {
            $thongbao = "Thông tin Tên đăng nhập hoặc Email không chính xác!";
        }
    } catch (Exception $e) {
        $thongbao = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Bách Hóa Xanh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            /* Đồng bộ biến hình nền với trang Login */
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

        /* Lớp phủ làm tối nền */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); 
            z-index: 0;
        }

        /* Container chính với hiệu ứng kính mờ (Glassmorphism) */
        .forgot-container { 
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px; 
            background: rgba(255, 255, 255, 0.9); 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            backdrop-filter: blur(10px);
        }

        .title { 
            text-align: center; 
            font-size: 24px; 
            font-weight: bold; 
            color: var(--primary-color); 
            margin-bottom: 10px; 
            text-transform: uppercase;
        }
        
        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #444;
            margin-bottom: 30px;
            font-weight: 500;
        }

        label { font-weight: bold; display: block; margin-bottom: 8px; color: #333; }
        
        .input-group { margin-bottom: 20px; }
        .input-group input { 
            width: 100%; 
            padding: 12px 15px; 
            border-radius: 8px; 
            border: 1px solid #ddd; 
            font-size: 16px; 
            background: white;
            transition: 0.3s;
        }
        .input-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 8px rgba(0, 146, 63, 0.2);
        }

        .btn-submit { 
            width: 100%; 
            padding: 14px; 
            background: var(--primary-color); 
            color: white; 
            font-size: 16px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s; 
        }
        .btn-submit:hover { 
            background: #007a34; 
            transform: translateY(-2px);
        }

        /* Thông báo */
        .msg-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
        }
        .msg-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .msg-error { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }

        .link-back { 
            display: block; 
            text-align: center; 
            margin-top: 25px; 
            text-decoration: none; 
            color: var(--primary-color); 
            font-size: 15px; 
            font-weight: bold;
        }
        .link-back:hover { text-decoration: underline; }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .forgot-container { padding: 30px 20px; background: rgba(255, 255, 255, 0.95); }
            .title { font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="forgot-container">
    <div class="title">Quên mật khẩu</div>
    <div class="subtitle">Khôi phục tài khoản hệ thống kho</div>

    <form method="post">
        <div class="input-group">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required>
        </div>

        <div class="input-group">
            <label>Email xác thực</label>
            <input type="email" name="email" placeholder="Nhập email đã đăng ký..." required>
        </div>

        <button class="btn-submit" type="submit">LẤY LẠI MẬT KHẨU</button>

        <?php if (!empty($thongbao)) : ?>
            <div class="msg-box <?= $success ? 'msg-success' : 'msg-error' ?>">
                <?= $thongbao ?>
            </div>
        <?php endif; ?>

        <a href="Login.php" class="link-back">
            <i class="fa fa-arrow-left"></i> Quay lại Đăng nhập
        </a>
    </form>
</div>

</body>
</html>