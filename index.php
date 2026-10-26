<?php
session_start();
define('APP_ENTRY', true);
include_once __DIR__ . '/Controller/cAuth.php';

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function money($value) { return number_format((float)$value, 0, ',', '.') . ' ₫'; }
function setFlash($type, $message) { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function getFlash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

$auth = new clsCAuth();

if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    $auth->DangXuat();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    include __DIR__ . '/View/frmLogin.php';
    exit;
}

$routes = [
    'phieunhap' => [
        'title' => 'Lập Phiếu Nhập Kho',
        'file' => 'phieuNhap.php',
        'roles' => ['ADMIN','THUKHO'],
        'icon' => '▣'
    ],
    'donban' => [
        'title' => 'Thêm Đơn Bán Hàng',
        'file' => 'donBan.php',
        'roles' => ['ADMIN','NHANVIEN'],
        'icon' => '◫'
    ],
    'danhmuc' => [
        'title' => 'Danh Mục Hàng Hóa',
        'file' => 'danhMuc.php',
        'roles' => ['ADMIN','KETOAN'],
        'icon' => '▤'
    ],
    'dieuchuyen' => [
        'title' => 'Lập Phiếu Điều Chuyển',
        'file' => 'dieuChuyen.php',
        'roles' => ['ADMIN','THUKHO'],
        'icon' => '⇄'
    ],
    'donnhap' => [
        'title' => 'Lập Đơn Đặt Nhập Hàng',
        'file' => 'donNhap.php',
        'roles' => ['ADMIN','CHUNPP'],
        'icon' => '▥'
    ],
    'phanquyen' => [
        'title' => 'Phân Quyền Người Dùng',
        'file' => 'phanQuyen.php',
        'roles' => ['ADMIN'],
        'icon' => '⚙'
    ],
];

function defaultPageForRole($role) {
    return match ($role) {
        'THUKHO' => 'phieunhap',
        'NHANVIEN' => 'donban',
        'KETOAN' => 'danhmuc',
        'CHUNPP' => 'donnhap',
        default => 'phanquyen',
    };
}

$page = $_GET['page'] ?? defaultPageForRole($_SESSION['role_code']);

if (!isset($routes[$page])) {
    $page = defaultPageForRole($_SESSION['role_code']);
}

$route = $routes[$page];
$authorized = in_array($_SESSION['role_code'], $route['roles'], true);

ob_start();

if ($authorized) {
    include __DIR__ . '/View/' . $route['file'];
} else {
    include __DIR__ . '/View/forbidden.php';
}

$content = ob_get_clean();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($route['title']) ?> - THÀNHSHOP</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js"></script>
</head>
<body>
<div class="app">

    <aside class="sidebar">
        <div class="brand">THÀNH<span>SHOP</span></div>

        <div class="role-chip">
            <?= e($_SESSION['role_name']) ?>
        </div>

        <div class="menu-title">
            Chức năng nghiệp vụ
        </div>

        <nav class="nav">
            <?php foreach ($routes as $key => $item): ?>
                <?php if (in_array($_SESSION['role_code'], $item['roles'], true)): ?>
                    <a class="nav-link <?= $page === $key ? 'active' : '' ?>" href="?page=<?= e($key) ?>">
                        <span><?= e($item['icon']) ?></span>
                        <?= e($item['title']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <div class="page-title">
                    <?= e($route['title']) ?>
                </div>

                <div class="page-subtitle">
                    Hệ thống quản lý bán hàng
                </div>
            </div>

            <div class="user-box">

                <div class="avatar">
                    <?= e(mb_strtoupper(mb_substr($_SESSION['full_name'], 0, 1))) ?>
                </div>

                <div class="user-info">
                    <b><?= e($_SESSION['full_name']) ?></b>
                    <small><?= e($_SESSION['role_name']) ?></small>
                </div>

                <a href="?page=logout" class="top-logout">
                    Đăng xuất
                </a>

            </div>
        </header>

        <section class="content">

            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>

        </section>

    </main>

</div>
</body>
</html>