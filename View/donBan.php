<?php
include_once __DIR__ . '/../Controller/cDonBan.php';
$c = new clsCDonBan();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['uc_action']??'')==='save_don_ban'){
    $res=$c->luuDonBan($_POST,(int)$_SESSION['user_id']);
    if($res['ok']){setFlash('success',$res['message']);header('Location: index.php?page=donban');exit;}
    $error=$res['message'];
}
$customers=$c->layKhachHang();$products=$c->layHangHoa();$recent=$c->layDonGanDay();$preview=$c->taoMaDonPreview();
$selected=$_POST['MaKH']??($customers[0]['MaKH']??'');
?>
<?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<div class="hint"><b>UC 2:</b> Nhân viên bán hàng chọn khách hàng, thêm ít nhất một mặt hàng, hệ thống tính thuế/thành tiền, lưu đơn ở trạng thái “Chờ thanh toán” và cập nhật công nợ khách hàng.</div>
<form method="post" id="frmDonBan">
<input type="hidden" name="uc_action" value="save_don_ban">
<div class="card">
 <div class="section-head"><div><h2>1. Thông tin đơn bán hàng</h2><p>Số đơn và ngày lập được hệ thống tự sinh.</p></div></div>
 <div class="form-grid">
   <div class="field"><label>Số đơn hàng</label><input class="code-input" name="SoDonHang" value="<?=e($_POST['SoDonHang']??$preview)?>" readonly></div>
   <div class="field"><label>Ngày lập</label><input value="<?=e(date('Y-m-d'))?>" readonly></div>
   <div class="field"><label>Nhân viên lập</label><input value="<?=e($_SESSION['full_name'])?>" readonly></div>
   <div class="field"><label>Khách hàng</label><select name="MaKH" id="dbCustomer" required><?php foreach($customers as $kh):?><option value="<?=e($kh['MaKH'])?>" <?=$selected===$kh['MaKH']?'selected':''?>><?=e($kh['MaKH'].' - '.$kh['TenKH'])?></option><?php endforeach;?></select></div>
   <div class="field"><label>Điện thoại</label><input id="dbPhone" readonly></div>
   <div class="field"><label>Địa chỉ</label><input id="dbAddress" readonly></div>
   <div class="field"><label>Hạn nợ (ngày)</label><input id="dbDebtDays" readonly></div>
   <div class="field"><label>Ngày quá hạn</label><input id="dbDueDate" readonly></div>
 </div>
 <div class="inline-info" style="margin-top:16px">
   <div class="info-pill">Hạn mức nợ: <b id="dbLimit">0 ₫</b></div>
   <div class="info-pill">Nợ trong hạn: <b id="dbCurrent">0 ₫</b></div>
   <div class="info-pill">Nợ quá hạn: <b id="dbOverdue">0 ₫</b></div>
 </div>
</div>
<div class="card">
 <div class="section-head"><div><h2>2. Chi tiết đơn hàng</h2><p>Số lượng và đơn giá phải lớn hơn 0.</p></div><button type="button" class="btn btn-primary" id="dbAdd">+ Thêm mặt hàng</button></div>
 <div class="table-wrap"><table><thead><tr><th>STT</th><th>Hàng hóa</th><th>Số lượng</th><th>Đơn giá</th><th>Chiết khấu %</th><th>Thuế %</th><th class="text-right">Tiền thuế</th><th class="text-right">Thành tiền</th><th></th></tr></thead><tbody id="dbBody"></tbody></table></div>
 <div class="finance-box">
   <div class="form-grid-3"><div class="info-pill">Tiền hàng: <b id="dbGoods">0 ₫</b></div><div class="info-pill">Tổng chiết khấu: <b id="dbDiscount">0 ₫</b></div><div class="info-pill">Tổng thuế: <b id="dbTax">0 ₫</b></div></div>
   <div class="finance-total"><span>TỔNG TIỀN</span><span id="dbTotal">0 ₫</span></div>
 </div>
 <div class="actions"><button type="reset" class="btn btn-light">Làm mới</button><button class="btn btn-primary">Lưu đơn bán hàng</button></div>
</div>
</form>
<div class="card"><div class="section-head"><div><h2>Đơn bán gần đây</h2></div></div><div class="table-wrap"><table><thead><tr><th>Số đơn</th><th>Khách hàng</th><th>Nhân viên</th><th>Ngày lập</th><th>Ngày quá hạn</th><th class="text-right">Tổng tiền</th><th>Trạng thái</th></tr></thead><tbody>
<?php if(!$recent):?><tr><td colspan="7" class="empty-state">Chưa có đơn bán hàng.</td></tr><?php endif;?>
<?php foreach($recent as $r):?><tr><td class="text-blue"><?=e($r['SoDonHang'])?></td><td><?=e($r['TenKH'])?></td><td><?=e($r['NhanVien'])?></td><td><?=e($r['NgayLap'])?></td><td><?=e($r['NgayQuaHan'])?></td><td class="text-right"><b><?=money($r['TongTien'])?></b></td><td><span class="badge badge-orange"><?=e($r['TrangThai'])?></span></td></tr><?php endforeach;?>
</tbody></table></div></div>
<script>
const DB_CUSTOMERS=<?=json_encode($customers,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const DB_PRODUCTS=<?=json_encode($products,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const DB_POST_ROWS=<?=json_encode(array_map(function($i){return ['MaHang'=>$_POST['MaHang'][$i]??'','SoLuong'=>$_POST['SoLuong'][$i]??1,'DonGia'=>$_POST['DonGia'][$i]??0,'ChietKhau'=>$_POST['ChietKhau'][$i]??0,'ThueSuat'=>$_POST['ThueSuat'][$i]??0];},array_keys($_POST['MaHang']??[])),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
let dbRows=DB_POST_ROWS.length?DB_POST_ROWS.map(x=>({...x})):(DB_PRODUCTS.length?[{MaHang:DB_PRODUCTS[0].MaHang,SoLuong:1,DonGia:DB_PRODUCTS[0].DonGiaBan,ChietKhau:0,ThueSuat:DB_PRODUCTS[0].ThueSuat}]:[]);
function dbProduct(ma){return DB_PRODUCTS.find(x=>x.MaHang===ma)}
function loadCustomer(){const kh=DB_CUSTOMERS.find(x=>x.MaKH===document.getElementById('dbCustomer')?.value);if(!kh)return;document.getElementById('dbPhone').value=kh.DienThoai||'';document.getElementById('dbAddress').value=kh.DiaChi||'';document.getElementById('dbDebtDays').value=kh.HanNo;document.getElementById('dbLimit').textContent=formatMoney(kh.HanMucNo);document.getElementById('dbCurrent').textContent=formatMoney(kh.NoTrongHan);document.getElementById('dbOverdue').textContent=formatMoney(kh.NoQuaHan);let d=new Date();d.setDate(d.getDate()+Number(kh.HanNo||0));document.getElementById('dbDueDate').value=d.toISOString().slice(0,10)}
function renderDb(){const body=document.getElementById('dbBody');body.innerHTML='';dbRows.forEach((r,i)=>{const opts=DB_PRODUCTS.map(p=>`<option value="${escapeHtml(p.MaHang)}" ${p.MaHang===r.MaHang?'selected':''}>${escapeHtml(p.MaHang+' - '+p.TenHang)}</option>`).join('');const base=Number(r.SoLuong||0)*Number(r.DonGia||0),ck=base*Number(r.ChietKhau||0)/100,tax=(base-ck)*Number(r.ThueSuat||0)/100,total=base-ck+tax;body.insertAdjacentHTML('beforeend',`<tr><td class="text-center">${i+1}</td><td><select class="table-input select-wide" name="MaHang[]" onchange="dbField(${i},'MaHang',this.value,true)">${opts}</select></td><td><input class="table-input" type="number" min="0.01" step="0.01" name="SoLuong[]" value="${escapeHtml(r.SoLuong)}" oninput="dbField(${i},'SoLuong',this.value)"></td><td><input class="table-input" type="number" min="0.01" step="0.01" name="DonGia[]" value="${escapeHtml(r.DonGia)}" oninput="dbField(${i},'DonGia',this.value)"></td><td><input class="table-input" type="number" min="0" max="100" step="0.01" name="ChietKhau[]" value="${escapeHtml(r.ChietKhau||0)}" oninput="dbField(${i},'ChietKhau',this.value)"></td><td><input class="table-input" type="number" min="0" max="100" step="0.01" name="ThueSuat[]" value="${escapeHtml(r.ThueSuat||0)}" oninput="dbField(${i},'ThueSuat',this.value)"></td><td class="text-right">${formatMoney(tax)}</td><td class="text-right text-blue">${formatMoney(total)}</td><td><button type="button" class="btn btn-danger btn-sm" onclick="dbDelete(${i})">Xóa</button></td></tr>`)});calcDb()}
function dbField(i,f,v,productChanged=false){dbRows[i][f]=v;if(productChanged){const p=dbProduct(v);if(p){dbRows[i].DonGia=p.DonGiaBan;dbRows[i].ThueSuat=p.ThueSuat}}renderDb()}
function dbDelete(i){dbRows.splice(i,1);renderDb()}
function calcDb(){let goods=0,discount=0,tax=0,total=0;dbRows.forEach(r=>{const b=Number(r.SoLuong||0)*Number(r.DonGia||0),c=b*Number(r.ChietKhau||0)/100,t=(b-c)*Number(r.ThueSuat||0)/100;goods+=b;discount+=c;tax+=t;total+=b-c+t});document.getElementById('dbGoods').textContent=formatMoney(goods);document.getElementById('dbDiscount').textContent=formatMoney(discount);document.getElementById('dbTax').textContent=formatMoney(tax);document.getElementById('dbTotal').textContent=formatMoney(total)}
document.getElementById('dbCustomer')?.addEventListener('change',loadCustomer);document.getElementById('dbAdd')?.addEventListener('click',()=>{if(!DB_PRODUCTS.length)return;const p=DB_PRODUCTS[0];dbRows.push({MaHang:p.MaHang,SoLuong:1,DonGia:p.DonGiaBan,ChietKhau:0,ThueSuat:p.ThueSuat});renderDb()});loadCustomer();renderDb();
</script>
