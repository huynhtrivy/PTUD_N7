<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMAuth {
    function getUserByCredentials($username, $passwordMd5) {
        $p = new clsKetnoi();
        $conn = $p->moketnoi();
        $sql = "SELECT tk.MaTaiKhoan, tk.TenDangNhap, tk.HoTen, tk.MaNhomQuyen,
                       nq.MaQuyen, nq.TenNhomQuyen
                FROM TaiKhoan tk
                JOIN NhomQuyen nq ON nq.MaNhomQuyen = tk.MaNhomQuyen
                WHERE tk.TenDangNhap = ? AND tk.MatKhau = ? AND tk.TrangThai = 'Hoạt động'
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $passwordMd5);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $p->dongketnoi($conn);
        return $row ?: false;
    }
}
