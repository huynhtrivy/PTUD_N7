<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMPhanQuyen {
    function getTaiKhoan(){
        $p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT tk.MaTaiKhoan,tk.TenDangNhap,tk.HoTen,tk.MaNhomQuyen,tk.TrangThai,nq.MaQuyen,nq.TenNhomQuyen FROM TaiKhoan tk JOIN NhomQuyen nq ON nq.MaNhomQuyen=tk.MaNhomQuyen ORDER BY tk.MaTaiKhoan")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;
    }
    function getNhomQuyen(){
        $p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT * FROM NhomQuyen ORDER BY MaNhomQuyen")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;
    }
    function updateRole($maTK,$maNQ){
        $p=new clsKetnoi();$c=null;
        try{
            $c=$p->moketnoi();$c->begin_transaction();
            $stmt=$c->prepare("SELECT MaTaiKhoan FROM TaiKhoan WHERE MaTaiKhoan=? FOR UPDATE");$stmt->bind_param('i',$maTK);$stmt->execute();if(!$stmt->get_result()->fetch_assoc())throw new Exception('Tài khoản không tồn tại.');$stmt->close();
            $stmt=$c->prepare("SELECT MaNhomQuyen FROM NhomQuyen WHERE MaNhomQuyen=?");$stmt->bind_param('i',$maNQ);$stmt->execute();if(!$stmt->get_result()->fetch_assoc())throw new Exception('Nhóm quyền không hợp lệ.');$stmt->close();
            $stmt=$c->prepare("UPDATE TaiKhoan SET MaNhomQuyen=? WHERE MaTaiKhoan=?");$stmt->bind_param('ii',$maNQ,$maTK);$stmt->execute();$stmt->close();
            $c->commit();$p->dongketnoi($c);return ['ok'=>true,'message'=>'Phân quyền thành công.'];
        }catch(Throwable $e){if($c){try{$c->rollback();}catch(Throwable $ignore){}$p->dongketnoi($c);}return ['ok'=>false,'message'=>'Cập nhật quyền thất bại. '.$e->getMessage()];}
    }
}
