<?php
session_start();
include 'KetNoi/connect.php';
$conn = connectdb();

// --- HÀM KIỂM TRA THIẾT BỊ DI ĐỘNG (PHP) ---
function isMobile() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $mobileKeywords = [
        'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 
        'Windows Phone', 'Opera Mini', 'IEMobile'
    ];
    
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

$thongbao = "";
$loai_thongbao = "error"; 
$user = "";
$background_skin = "images/binhngo.png"; 

// Kiểm tra thiết bị di động thông thường
$is_mobile = isMobile();

if (isset($_GET['register']) && $_GET['register'] == 'success') {
    $thongbao = "Đăng ký thành công! Hãy đăng nhập ngay.";
    $loai_thongbao = "success";
}

if (isset($_POST["btnLogin"])) 
{
    $user = trim($_POST["txtUser"] ?? "");
    $pass = $_POST["txtPass"] ?? "";

    $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE TenDangNhap = ? LIMIT 1");
    $stmt->execute([$user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($pass, $row['MatKhau'])) 
    {
        $_SESSION['MaTK'] = $row['MaTK'];   
        $_SESSION["username"] = $row['TenDangNhap'];
        $_SESSION["MaNV"] = $row['MaNV'];
        $_SESSION['LAST_ACTIVITY'] = time(); 

        $update = $conn->prepare("UPDATE taikhoan SET isOnline = 1 WHERE MaTK = ?");
        $update->execute([$row['MaTK']]);

        if ($user === 'Admin') {
            header("location: admin/admin.php");
        } else {
            header("location: TrangChu.php");
        }
        exit();
    }
    else 
    {
        $thongbao = "Tên đăng nhập hoặc mật khẩu không chính xác!";
        $loai_thongbao = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Bách Hóa Xanh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-image: url('<?= $background_skin ?>');
            --primary-color: #00923F;
            --accent-yellow: #ffbc00;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-image) no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            z-index: 0;
        }

        .main { 
            position: relative;
            z-index: 1;
            display: flex; 
            min-height: 100vh; 
            justify-content: center; 
            align-items: center; 
            padding: 15px; 
        }

        .login-wrapper { 
            display: flex; 
            width: 100%;
            max-width: 900px; 
            border-radius: 20px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            background: rgba(255, 255, 255, 0.9);
            overflow: hidden; 
            backdrop-filter: blur(10px);
            min-height: 450px;
        }

        /* GIAO DIỆN HÌNH 1 - MOBILE THƯỜNG */
        .mobile-download-box {
            padding: 40px 20px;
            text-align: center;
            width: 100%;
        }

        .app-icon {
            width: 100px; height: 100px;
            background: var(--primary-color);
            border-radius: 22px;
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 20px rgba(0,146,63,0.3);
        }
        .app-icon i { color: white; font-size: 50px; }

        .btn-download {
            display: inline-block;
            margin-top: 25px;
            padding: 15px 30px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: 0.3s;
        }

        /* GIAO DIỆN HÌNH 2 - GIẢ LẬP DESKTOP TRÊN MOBILE */
        .desktop-mode-error-box {
            padding: 60px 20px;
            text-align: center;
            width: 100%;
            display: none; /* Chỉ hiện qua JS */
        }
        .monitor-error-icon {
            font-size: 70px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .btn-retry {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 30px;
            background: var(--accent-yellow);
            color: #333;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
        }
        /* Ẩn icon con mắt mặc định của Microsoft Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        /* Ẩn icon mặc định trên một số trình duyệt khác (nếu có) */
        input::-webkit-contacts-auto-fill-button,
        input::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            display: none !important;
            pointer-events: none;
        }

        /* GIAO DIỆN DESKTOP GỐC */
        #pc-content { display: flex; width: 100%; }
        .left-box { flex: 1; display: block; border-right: 1px solid rgba(0,0,0,0.1); }
        .left-box img { width: 100%; height: 100%; object-fit: cover; }
        .login-box { width: 100%; max-width: 450px; padding: 40px; }
        .login-title { text-align: center; color: var(--primary-color); font-size: 22px; font-weight: bold; margin-bottom: 30px; }
        
        label { font-weight: bold; display: block; margin-bottom: 8px; color: #333; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 12px 40px 12px 12px; border-radius: 8px; border: 1px solid #ddd; font-size: 16px; }
        .input-group i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777; }

        .btn-login { width: 100%; padding: 14px; background: var(--primary-color); color: white; font-size: 18px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

        .msg { padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; font-size: 14px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }

        @media (max-width: 768px) {
            .left-box { display: none; }
            .login-wrapper { max-width: 450px; }
        }
    </style>
</head>
<body>

<div class="main">
    <div class="login-wrapper">
        
        <?php if ($is_mobile): ?>
            <div class="mobile-download-box">
                <div class="app-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <h2 style="color: var(--primary-color); margin-bottom: 10px;">Bách Hóa Xanh</h2>
                <p style="color: #555; line-height: 1.6;">Để có trải nghiệm tốt nhất trên di động,<br>vui lòng tải ứng dụng</p>
                <h3 style="margin-top: 10px; color: #333;">Quản lý kho Bách Hóa Xanh</h3>
                
                <a href="#" class="btn-download">
                    <i class="fab fa-google-play"></i> TẢI ỨNG DỤNG NGAY
                </a>
                
                <p style="margin-top: 25px; font-size: 12px; color: #888;">
                    Phiên bản web mobile hiện đang được phát triển.
                </p>
            </div>

        <?php else: ?>
            <div id="desktop-mode-error" class="desktop-mode-error-box">
                <div class="monitor-error-icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">Chế độ không khả dụng</h3>
                <p style="color: #666; font-size: 14px; line-height: 1.6;">
                    Hệ thống phát hiện bạn đang dùng thiết bị di động ở chế độ máy tính.<br>
                    Vui lòng dùng <b>Laptop/PC</b> để đảm bảo an toàn dữ liệu kho.
                </p>
                <a href="javascript:location.reload()" class="btn-retry">THỬ LẠI</a>
            </div>

            <div id="pc-content">
                <div class="left-box">
                    <img src="images/hinhnenlogin.webp" alt="Warehouse">
                </div>

                <div class="login-box">
                    <div class="login-title">HỆ THỐNG QUẢN LÝ KHO</div>

                    <?php if ($thongbao != ""): ?>
                        <div class="msg <?= $loai_thongbao ?>"><?= $thongbao ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <label>Tên đăng nhập</label>
                        <div class="input-group">
                            <input type="text" name="txtUser" value="<?= htmlspecialchars($user) ?>" placeholder="Username..." required>
                        </div>

                        <label>Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" name="txtPass" id="txtPass" placeholder="Password..." required style="padding-right: 45px;">
                            <i class="fa fa-eye" id="togglePass"></i>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 14px;">
                            <label style="font-weight: normal;"><input type="checkbox"> Nhớ mật khẩu</label>
                            <a href="quenmatkhau.php" style="color: var(--primary-color); text-decoration: none;">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" name="btnLogin" class="btn-login">ĐĂNG NHẬP</button>
                        
                        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
                            Chưa có tài khoản? <a href="TaoTaiKhoan.php" style="color: #00923F; font-weight: bold; text-decoration: none;">Đăng ký ngay</a>
                        </p>
                    </form>
                    <div id="ai-status-toast" style="position: fixed; bottom: 20px; left: 20px; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: bold; z-index: 9999; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: all 0.5s ease; background: #f1c40f; color: #000;">
    <i class="fas fa-spinner fa-spin" id="ai-icon"></i>
    <span id="ai-text">Đang đánh thức Lora AI...</span>
</div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Logic ẩn hiện mật khẩu
    if(document.querySelector('#togglePass')) {
        const togglePass = document.querySelector('#togglePass');
        const password = document.querySelector('#txtPass');

        if(togglePass) {
            togglePass.addEventListener('click', function () {
                // Chuyển đổi loại input
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Đổi icon từ mắt mở sang mắt gạch chéo
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    }

    // --- NÂNG CẤP KIỂM TRA CHẾ ĐỘ MÁY TÍNH TRÊN ĐIỆN THOẠI ---
    document.addEventListener("DOMContentLoaded", function() {
        // Kiểm tra phần cứng có cảm ứng (Touch) không
        const isTouchDevice = (('ontouchstart' in window) || (navigator.maxTouchPoints > 0));
        
        // Kiểm tra xem PHP có đang nhận diện là Desktop không
        const isMobilePHP = <?php echo $is_mobile ? 'true' : 'false'; ?>;

        // Nếu PHP nói là Desktop NHƯNG thiết bị lại có cảm ứng (Touch)
        if (!isMobilePHP && isTouchDevice) {
            const pcContent = document.getElementById('pc-content');
            const desktopError = document.getElementById('desktop-mode-error');
            
            if (pcContent && desktopError) {
                pcContent.style.display = 'none';      // Ẩn form PC
                desktopError.style.display = 'block'; // Hiện cảnh báo Hình 2
            }
        }

        // Logic cũ: Ép class mobile nếu màn hình nhỏ
        if (window.innerWidth <= 768) {
            document.documentElement.classList.add('is-mobile-device');
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const aiToast = document.getElementById('ai-status-toast');
        const aiText = document.getElementById('ai-text');
        const aiIcon = document.getElementById('ai-icon');

        console.log("Lora AI: Đang gửi tín hiệu đánh thức...");

        // Gõ cửa Render
        fetch("https://lora-ai-9ti1.onrender.com/", { mode: 'cors' })
            .then(response => {
                if (response.ok) {
                    // KHI ĐÃ THỨC GIẤC
                    aiToast.style.background = "#00923F"; // Màu xanh Bách Hóa Xanh
                    aiToast.style.color = "#fff";
                    aiText.innerText = "Lora AI đã sẵn sàng!";
                    aiIcon.className = "fas fa-check-circle"; // Đổi icon sang tích xanh
                    console.log("Lora AI: Đã thức giấc!");
                    
                    // Sau 5 giây tự ẩn đi cho đỡ vướng
                    setTimeout(() => { aiToast.style.opacity = "0.5"; }, 5000);
                }
            })
            .catch(error => {
                // KHI ĐANG ĐỢI HOẶC LỖI
                console.log("Lora AI: Đang khởi động...");
            });
    });
</script>
</body>
</html>