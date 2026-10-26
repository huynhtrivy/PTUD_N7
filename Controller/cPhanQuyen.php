<?php
include_once __DIR__ . '/../Model/mPhanQuyen.php';

class clsCPhanQuyen {
    function layTaiKhoan() { return (new clsMPhanQuyen())->getTaiKhoan(); }
    function layNhomQuyen() { return (new clsMPhanQuyen())->getNhomQuyen(); }
    function capNhatQuyen($data) {
        $maTK = (int)($data['MaTaiKhoan'] ?? 0);
        $maNQ = (int)($data['MaNhomQuyen'] ?? 0);
        if ($maTK <= 0 || $maNQ <= 0) return ['ok' => false, 'message' => 'Vui lòng chọn tài khoản và nhóm quyền.'];
        return (new clsMPhanQuyen())->updateRole($maTK, $maNQ);
    }
}
