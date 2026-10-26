<?php
include_once __DIR__ . '/../Model/mAuth.php';

class clsCAuth {
    function DangNhap($username, $password) {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return ['ok' => false, 'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu.'];
        }
        $m = new clsMAuth();
        $user = $m->getUserByCredentials($username, md5($password));
        if (!$user) {
            return ['ok' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.'];
        }
        $_SESSION['user_id'] = $user['MaTaiKhoan'];
        $_SESSION['username'] = $user['TenDangNhap'];
        $_SESSION['full_name'] = $user['HoTen'];
        $_SESSION['role_id'] = $user['MaNhomQuyen'];
        $_SESSION['role_code'] = $user['MaQuyen'];
        $_SESSION['role_name'] = $user['TenNhomQuyen'];
        return ['ok' => true];
    }

    function DangXuat() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
