<?php
include_once __DIR__ . '/../Model/mDanhMuc.php';

class clsCDanhMuc {
    function layDanhSach() { return (new clsMDanhMuc())->getDanhMuc(); }
    function themDanhMuc($data) {
        return (new clsMDanhMuc())->addDanhMuc(
            trim($data['MaDanhMuc'] ?? ''),
            trim($data['ChungLoai'] ?? ''),
            trim($data['MoTa'] ?? ''),
            trim($data['TrangThai'] ?? 'Hoạt động')
        );
    }
}
