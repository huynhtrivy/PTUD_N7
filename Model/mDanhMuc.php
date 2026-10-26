<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMDanhMuc {
    function getDanhMuc() {
        $p=new clsKetnoi(); $c=$p->moketnoi();
        $rows=$c->query("SELECT dm.*, COUNT(hh.MaHang) SoMatHang FROM DanhMucHang dm LEFT JOIN HangHoa hh ON hh.MaDanhMuc=dm.MaDanhMuc GROUP BY dm.MaDanhMuc ORDER BY dm.MaDanhMuc")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c); return $rows;
    }
    function addDanhMuc($ma,$ten,$mota,$trangthai){
        if($ma==='' || $ten==='') return ['ok'=>false,'message'=>'Mã danh mục và tên danh mục không được để trống.'];
        if(trim($ten)==='') return ['ok'=>false,'message'=>'Tên danh mục không được chỉ chứa khoảng trắng.'];
        if(!in_array($trangthai,['Hoạt động','Ngừng hoạt động'],true)) $trangthai='Hoạt động';
        $p=new clsKetnoi(); $c=null;
        try{
            $c=$p->moketnoi();
            $stmt=$c->prepare("SELECT COUNT(*) c FROM DanhMucHang WHERE MaDanhMuc=? OR LOWER(ChungLoai)=LOWER(?)");
            $stmt->bind_param('ss',$ma,$ten); $stmt->execute(); $exists=(int)$stmt->get_result()->fetch_assoc()['c']; $stmt->close();
            if($exists>0) throw new Exception('Mã hoặc tên danh mục đã tồn tại.');
            $stmt=$c->prepare("INSERT INTO DanhMucHang(MaDanhMuc,ChungLoai,MoTa,TrangThai) VALUES(?,?,?,?)");
            $stmt->bind_param('ssss',$ma,$ten,$mota,$trangthai); $stmt->execute(); $stmt->close();
            $p->dongketnoi($c); return ['ok'=>true,'message'=>'Thêm danh mục thành công.'];
        }catch(Throwable $e){ if($c) $p->dongketnoi($c); return ['ok'=>false,'message'=>$e->getMessage()]; }
    }
}
