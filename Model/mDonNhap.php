<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMDonNhap {
    private function nextCodeWithConn($conn){$rs=$conn->query("SELECT COALESCE(MAX(CAST(SUBSTRING(SoDonNhap,3) AS UNSIGNED)),0)+1 n FROM DonNhapHang WHERE SoDonNhap REGEXP '^DN[0-9]+$'");return 'DN'.str_pad((string)((int)$rs->fetch_assoc()['n']),6,'0',STR_PAD_LEFT);}
    function nextCode(){$p=new clsKetnoi();$c=$p->moketnoi();$x=$this->nextCodeWithConn($c);$p->dongketnoi($c);return $x;}
    function getNhaCungCap(){$p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT * FROM NhaCungCap WHERE TrangThai='Hoạt động' ORDER BY TenNCC")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;}
    function getKho(){$p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT * FROM Kho WHERE TrangThai='Hoạt động' ORDER BY TenKho")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;}
    function getHangHoa(){$p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT MaHang,TenHang,GiaNhapMacDinh,DonViTinh FROM HangHoa WHERE TrangThai='Hoạt động' ORDER BY TenHang")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;}
    function getTonKho(){$p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT MaKho,MaHang,SoLuongTon FROM TonKho")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;}
    function getDangChoNhan(){
        $p=new clsKetnoi();$c=$p->moketnoi();
        $sql="SELECT dn.MaKhoDuKien MaKho,ct.MaHang,
                     SUM(GREATEST(ct.SoLuongDat-COALESCE((SELECT SUM(ctpn.SoLuongNhap) FROM PhieuNhapKho pn JOIN ChiTietPhieuNhap ctpn ON ctpn.MaPhieuNhap=pn.MaPhieuNhap WHERE pn.SoDonNhap=dn.SoDonNhap AND pn.TrangThai='Đã duyệt' AND ctpn.MaHang=ct.MaHang),0),0)) DangChoNhan
              FROM DonNhapHang dn JOIN ChiTietDonNhap ct ON ct.SoDonNhap=dn.SoDonNhap
              WHERE dn.TrangThai IN('Chờ nhận hàng','Đã nhận một phần') GROUP BY dn.MaKhoDuKien,ct.MaHang";
        $r=$c->query($sql)->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;
    }
    function getDonGanDay(){
        $p=new clsKetnoi();$c=$p->moketnoi();$r=$c->query("SELECT d.SoDonNhap,d.NgayLap,n.TenNCC,k.TenKho,d.TongTien,d.TrangThai FROM DonNhapHang d JOIN NhaCungCap n ON n.MaNCC=d.MaNCC JOIN Kho k ON k.MaKho=d.MaKhoDuKien ORDER BY d.NgayLap DESC,d.SoDonNhap DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);$p->dongketnoi($c);return $r;
    }
    function saveDon($h,$rows){
        $p=new clsKetnoi();$conn=null;
        try{
            $conn=$p->moketnoi();$conn->begin_transaction();
            // Serialize requests from one owner, including retries after a timeout.
            $stmt=$conn->prepare("SELECT tk.MaTaiKhoan FROM TaiKhoan tk JOIN NhomQuyen nq ON nq.MaNhomQuyen=tk.MaNhomQuyen WHERE tk.MaTaiKhoan=? AND tk.TrangThai='Hoạt động' AND nq.MaQuyen IN ('ADMIN','CHUNPP') FOR UPDATE");
            $stmt->bind_param('i',$h['MaChuNPP']);$stmt->execute();$owner=$stmt->get_result()->fetch_assoc();$stmt->close();
            if(!$owner)throw new Exception('Tài khoản không còn quyền lập đơn đặt nhập hàng.');
            $stmt=$conn->prepare('SELECT SoDonNhap FROM DonNhapHang WHERE RequestKey=? AND MaChuNPP=?');
            $stmt->bind_param('si',$h['RequestKey'],$h['MaChuNPP']);$stmt->execute();$existing=$stmt->get_result()->fetch_assoc();$stmt->close();
            if($existing){$conn->commit();$p->dongketnoi($conn);return ['ok'=>true,'message'=>'Lập đơn đặt nhập hàng thành công. Mã đơn: '.$existing['SoDonNhap'],'code'=>$existing['SoDonNhap']];}
            if($h['ChietKhauPhanTram']<0||$h['ChietKhauPhanTram']>100||$h['ThueSuat']<0||$h['ThueSuat']>100)throw new Exception('Tỷ lệ chiết khấu và thuế phải từ 0 đến 100%.');
            $stmt=$conn->prepare("SELECT MaNCC FROM NhaCungCap WHERE MaNCC=? AND TrangThai='Hoạt động'");$stmt->bind_param('s',$h['MaNCC']);$stmt->execute();if(!$stmt->get_result()->fetch_assoc())throw new Exception('Nhà cung cấp không còn hợp lệ.');$stmt->close();
            $stmt=$conn->prepare("SELECT MaKho FROM Kho WHERE MaKho=? AND TrangThai='Hoạt động'");$stmt->bind_param('s',$h['MaKhoDuKien']);$stmt->execute();if(!$stmt->get_result()->fetch_assoc())throw new Exception('Kho dự kiến nhận không còn hợp lệ.');$stmt->close();
            $seen=[];$prepared=[];$tongHang=0;
            foreach($rows as $r){
                if($r['NhuCau']<0||$r['SoLuongDat']<=0||$r['GiaDuKien']<=0)throw new Exception('Nhu cầu không được âm; số lượng đặt và giá dự kiến phải lớn hơn 0.');
                if(isset($seen[$r['MaHang']]))throw new Exception('Mặt hàng '.$r['MaHang'].' bị thêm trùng.');$seen[$r['MaHang']]=1;
                $stmt=$conn->prepare("SELECT MaHang FROM HangHoa WHERE MaHang=? AND TrangThai='Hoạt động'");$stmt->bind_param('s',$r['MaHang']);$stmt->execute();if(!$stmt->get_result()->fetch_assoc())throw new Exception('Hàng hóa '.$r['MaHang'].' không còn hợp lệ.');$stmt->close();
                $stmt=$conn->prepare("SELECT COALESCE(SoLuongTon,0) Ton FROM TonKho WHERE MaKho=? AND MaHang=?");$stmt->bind_param('ss',$h['MaKhoDuKien'],$r['MaHang']);$stmt->execute();$x=$stmt->get_result()->fetch_assoc();$ton=$x?(float)$x['Ton']:0;$stmt->close();
                $stmt=$conn->prepare("SELECT COALESCE(SUM(GREATEST(ct.SoLuongDat-COALESCE((SELECT SUM(ctpn.SoLuongNhap) FROM PhieuNhapKho pn JOIN ChiTietPhieuNhap ctpn ON ctpn.MaPhieuNhap=pn.MaPhieuNhap WHERE pn.SoDonNhap=dn.SoDonNhap AND pn.TrangThai='Đã duyệt' AND ctpn.MaHang=ct.MaHang),0),0)),0) Cho
                                      FROM DonNhapHang dn JOIN ChiTietDonNhap ct ON ct.SoDonNhap=dn.SoDonNhap
                                      WHERE dn.MaKhoDuKien=? AND ct.MaHang=? AND dn.TrangThai IN('Chờ nhận hàng','Đã nhận một phần')");
                $stmt->bind_param('ss',$h['MaKhoDuKien'],$r['MaHang']);$stmt->execute();$cho=(float)$stmt->get_result()->fetch_assoc()['Cho'];$stmt->close();
                $r['TonKhaDung']=$ton;$r['DangChoNhan']=$cho;$r['ThanhTien']=$r['SoLuongDat']*$r['GiaDuKien'];$tongHang+=$r['ThanhTien'];$prepared[]=$r;
            }
            if(!$prepared)throw new Exception('Đơn đặt nhập phải có ít nhất một mặt hàng.');
            $ck=round($tongHang*$h['ChietKhauPhanTram']/100,2);$thue=round(($tongHang-$ck)*$h['ThueSuat']/100,2);$tong=$tongHang-$ck+$thue;
            $code=$h['SoDonNhap']?:$this->nextCodeWithConn($conn);$stmt=$conn->prepare("SELECT COUNT(*) c FROM DonNhapHang WHERE SoDonNhap=?");$stmt->bind_param('s',$code);$stmt->execute();if((int)$stmt->get_result()->fetch_assoc()['c']>0)$code=$this->nextCodeWithConn($conn);$stmt->close();
            $ngay=date('Y-m-d');$status='Chờ nhận hàng';$payment=$h['HinhThucThanhToan'];$loai=($payment==='Trả ngay'?'IN':($payment==='Ký gửi'?'ND':'NP'));$requestKey=$h['RequestKey'];
            $stmt=$conn->prepare("INSERT INTO DonNhapHang(SoDonNhap,MaNCC,MaKhoDuKien,MaChuNPP,NgayLap,LoaiDon,HinhThucThanhToan,ChietKhauPhanTram,ThueSuat,TongTienHang,TienChietKhau,TienThue,TongTien,TrangThai,RequestKey)
                                  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('sssisssddddddss',$code,$h['MaNCC'],$h['MaKhoDuKien'],$h['MaChuNPP'],$ngay,$loai,$payment,$h['ChietKhauPhanTram'],$h['ThueSuat'],$tongHang,$ck,$thue,$tong,$status,$requestKey);$stmt->execute();$stmt->close();
            $ins=$conn->prepare("INSERT INTO ChiTietDonNhap(SoDonNhap,MaHang,NhuCau,TonKhaDung,DangChoNhan,SoLuongDat,GiaDuKien,ThanhTien) VALUES(?,?,?,?,?,?,?,?)");
            foreach($prepared as $r){$ins->bind_param('ssdddddd',$code,$r['MaHang'],$r['NhuCau'],$r['TonKhaDung'],$r['DangChoNhan'],$r['SoLuongDat'],$r['GiaDuKien'],$r['ThanhTien']);$ins->execute();}$ins->close();
            $conn->commit();$p->dongketnoi($conn);return ['ok'=>true,'message'=>'Lập đơn đặt nhập hàng thành công. Mã đơn: '.$code,'code'=>$code];
        }catch(Throwable $e){if($conn){try{$conn->rollback();}catch(Throwable $ignore){}$p->dongketnoi($conn);}return ['ok'=>false,'message'=>$e->getMessage()];}
    }
}
