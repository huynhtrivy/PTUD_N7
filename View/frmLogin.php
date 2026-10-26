<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->DangNhap($_POST['username'] ?? '', $_POST['password'] ?? '');
    if ($result['ok']) {
        header('Location: index.php');
        exit;
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - THÀNHSHOP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
<div class="login-page">
    <div class="login-left">
        <div class="login-brand">
            <div class="login-logo">TS</div>
            <div class="login-brand-text">THÀNH<span>SHOP</span></div>
        </div>
        <div class="login-intro">
            <div class="login-tag">HỆ THỐNG QUẢN LÝ BÁN HÀNG</div>
            <h1>Quản lý cửa hàng<br><span>đơn giản & hiệu quả.</span></h1>
            <p>Quản lý đơn hàng, nhập kho, điều chuyển hàng hóa và tồn kho trên cùng một hệ thống.</p>
            <div class="login-features">
                <div class="login-feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <b>Quản lý tập trung</b>
                        <small>Theo dõi dữ liệu trên cùng một hệ thống</small>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <b>Quản lý kho hàng</b>
                        <small>Kiểm soát nhập kho và điều chuyển hàng hóa</small>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <b>Phân quyền người dùng</b>
                        <small>Đảm bảo đúng chức năng cho từng vai trò</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-copyright">© <?= date('Y') ?> THÀNHSHOP · Hệ thống quản lý bán hàng</div>
    </div>

    <div class="login-right">
        <div class="login-box">
            <div class="login-mobile-logo">
                <div class="login-logo">TS</div>
                <div class="login-brand-text dark">THÀNH<span>SHOP</span></div>
            </div>

            <div class="login-title">
                <span>CHÀO MỪNG TRỞ LẠI</span>
                <h2>Đăng nhập hệ thống</h2>
                <p>Nhập thông tin tài khoản để tiếp tục.</p>
            </div>

            <?php if ($error): ?>
            <div class="login-error">
                <div class="error-icon">!</div>
                <span><?= e($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="post" class="login-form" autocomplete="on">
                <div class="login-field">
                    <label for="username">Tên đăng nhập</label>
                    <div class="login-control">
                        <span class="control-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M4 20c.7-4 3.5-6 8-6s7.3 2 8 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?= e($_POST['username'] ?? '') ?>"
                            placeholder="Nhập tên đăng nhập"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="login-field">
                    <label for="password">Mật khẩu</label>
                    <div class="login-control">
                        <span class="control-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M12 14v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Nhập mật khẩu"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Hiện mật khẩu">
                            <svg id="eyeOpen" viewBox="0 0 24 24" fill="none">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/>
                                <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    <span>Đăng nhập</span>
                    <span class="login-arrow">→</span>
                </button>
            </form>

            <div class="login-bottom">
                <span class="status-dot"></span>
                Sử dụng tài khoản đã được cấp để đăng nhập
            </div>
        </div>
    </div>
</div>

<script>
const togglePassword=document.getElementById('togglePassword');
const passwordInput=document.getElementById('password');
togglePassword.addEventListener('click',function(){
    const hidden=passwordInput.type==='password';
    passwordInput.type=hidden?'text':'password';
    this.classList.toggle('active',hidden);
    this.setAttribute('aria-label',hidden?'Ẩn mật khẩu':'Hiện mật khẩu');
});
</script>
</body>
</html>