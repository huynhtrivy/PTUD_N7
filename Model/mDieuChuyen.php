<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMDieuChuyen {
    private function nextCodeWithConn($conn){$rs=$conn->query("SELECT COALESCE(MAX(CAST(SUBSTRING(MaPhieuDC,3) AS UNSIGNED)),0)+1 n FROM PhieuDieuChuyen WHERE MaPhieuDC REGEXP '^DC[0-9]+$'");return 'DC'.str_pad((string)((int)$rs->fetch_assoc()['n']),6,'0',STR_PAD_LEFT);}
    function nextCode(){$p=new clsKetnoi();$c=$p->moketnoi();$x=$this->nextCodeWithConn($c);$p->dongketnoi($c);return $x;}
    function getKho(){$p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT * FROM Kho WHERE TrangThai='Hoạt động' ORDER BY TenKho")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;}
    function getHangVaTon(){
        $p=new clsKetnoi();$c=$p->moketnoi();
        $r=$c->query("SELECT hh.MaHang,hh.TenHang,hh.GiaNhapMacDinh,tk.MaKho,tk.SoLuongTon FROM HangHoa hh LEFT JOIN TonKho tk ON tk.MaHang=hh.MaHang WHERE hh.TrangThai='Hoạt động' ORDER BY hh.MaHang,tk.MaKho")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c);return $r;
    }
    function getPhieuGanDay(){
        $p=new clsKetnoi();$c=$p->moketnoi();
        $r=$c->query("SELECT p.MaPhieuDC,p.NgayDieuChuyen,kx.TenKho KhoXuat,kn.TenKho KhoNhap,p.TongGiaTri,p.TrangThai
                      FROM PhieuDieuChuyen p JOIN Kho kx ON kx.MaKho=p.MaKhoXuat JOIN Kho kn ON kn.MaKho=p.MaKhoNhap
                      ORDER BY p.NgayDieuChuyen DESC,p.MaPhieuDC DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c);return $r;
    }
    function savePhieu($h,$rows){
        $p=new clsKetnoi();$conn=null;
        try{
            $conn=$p->moketnoi();$conn->begin_transaction();
            if($h['MaKhoXuat']===$h['MaKhoNhap']) throw new Exception('Kho xuất và kho nhập không được trùng nhau.');
            $stmt=$conn->prepare("SELECT MaKho FROM Kho WHERE MaKho IN (?,?) AND TrangThai='Hoạt động'");$stmt->bind_param('ss',$h['MaKhoXuat'],$h['MaKhoNhap']);$stmt->execute();$cnt=$stmt->get_result()->num_rows;$stmt->close();
            if($cnt!==2) throw new Exception('Kho xuất hoặc kho nhập không còn hợp lệ.');
            $seen=[];$prepared=[];$total=0;
            foreach($rows as $r){
                if($r['SoLuong']<=0 || floor($r['SoLuong'])!=$r['SoLuong']) throw new Exception('Số lượng chuyển phải là số nguyên dương.');
                if($r['DonGia']<0) throw new Exception('Đơn giá không hợp lệ.');
                if(isset($seen[$r['MaHang']])) throw new Exception('Mặt hàng '.$r['MaHang'].' bị thêm trùng.');$seen[$r['MaHang']]=1;
                $stmt=$conn->prepare("SELECT tk.SoLuongTon,hh.TenHang FROM TonKho tk JOIN HangHoa hh ON hh.MaHang=tk.MaHang WHERE tk.MaKho=? AND tk.MaHang=? AND hh.TrangThai='Hoạt động' FOR UPDATE");
                $stmt->bind_param('ss',$h['MaKhoXuat'],$r['MaHang']);$stmt->execute();$stock=$stmt->get_result()->fetch_assoc();$stmt->close();
                if(!$stock || (float)$stock['SoLuongTon']<$r['SoLuong']) throw new Exception('Số lượng xuất vượt quá tồn kho thực tế hiện tại: '.$r['MaHang']);
                $r['ThanhTien']=$r['SoLuong']*$r['DonGia'];$total+=$r['ThanhTien'];$prepared[]=$r;
            }
            if(!$prepared) throw new Exception('Phiếu chuyển phải có ít nhất một mặt hàng.');
            $code=$h['MaPhieuDC']?:$this->nextCodeWithConn($conn);$stmt=$conn->prepare("SELECT COUNT(*) c FROM PhieuDieuChuyen WHERE MaPhieuDC=?");$stmt->bind_param('s',$code);$stmt->execute();if((int)$stmt->get_result()->fetch_assoc()['c']>0)$code=$this->nextCodeWithConn($conn);$stmt->close();
            $ngay=date('Y-m-d');$status='Đã duyệt';
            $stmt=$conn->prepare("INSERT INTO PhieuDieuChuyen(MaPhieuDC,MaKhoXuat,MaKhoNhap,MaThuKho,NgayDieuChuyen,GhiChu,TongGiaTri,TrangThai) VALUES(?,?,?,?,?,?,?,?)");
            $stmt->bind_param('sssissds',$code,$h['MaKhoXuat'],$h['MaKhoNhap'],$h['MaThuKho'],$ngay,$h['GhiChu'],$total,$status);$stmt->execute();$stmt->close();
            $ins=$conn->prepare("INSERT INTO ChiTietDieuChuyen(MaPhieuDC,MaHang,SoLuong,DonGia,ThanhTien) VALUES(?,?,?,?,?)");
            $upd=$conn->prepare("UPDATE TonKho SET SoLuongTon=SoLuongTon-?,CapNhatLuc=CURRENT_TIMESTAMP WHERE MaKho=? AND MaHang=?");
            foreach($prepared as $r){$ins->bind_param('ssddd',$code,$r['MaHang'],$r['SoLuong'],$r['DonGia'],$r['ThanhTien']);$ins->execute();$upd->bind_param('dss',$r['SoLuong'],$h['MaKhoXuat'],$r['MaHang']);$upd->execute();}
            $ins->close();$upd->close();$conn->commit();$p->dongketnoi($conn);return ['ok'=>true,'message'=>'Lập phiếu chuyển kho thành công: '.$code,'code'=>$code];
        }catch(Throwable $e){if($conn){try{$conn->rollback();}catch(Throwable $ignore){}$p->dongketnoi($conn);}return ['ok'=>false,'message'=>$e->getMessage()];}
    }
}
