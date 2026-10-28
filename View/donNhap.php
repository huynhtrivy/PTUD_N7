<?php
include_once __DIR__ . '/../Controller/cDonNhap.php';
$c=new clsCDonNhap();$error='';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $requestKey = bin2hex(random_bytes(24));
    $_SESSION['purchase_requests'][$requestKey] = time();
    foreach ($_SESSION['purchase_requests'] as $key => $created) {
        if ($created < time() - 86400) unset($_SESSION['purchase_requests'][$key]);
    }
} else {
    $requestKey = is_string($_POST['RequestKey'] ?? null) ? $_POST['RequestKey'] : '';
}
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['uc_action']??'')==='save_purchase'){
 $res=$c->luuDon($_POST,(int)$_SESSION['user_id']);if($res['ok']){setFlash('success',$res['message']);header('Location: index.php?page=donnhap');exit;}$error=$res['message'];
}
$ncc=$c->layNhaCungCap();$warehouses=$c->layKho();$products=$c->layHangHoa();$stockRows=$c->layTonKho();$waitRows=$c->layDangChoNhan();$recent=$c->layDonGanDay();$preview=$c->taoMaPreview();
$stock=[];foreach($stockRows as $r)$stock[$r['MaKho']][$r['MaHang']]=(float)$r['SoLuongTon'];$waiting=[];foreach($waitRows as $r)$waiting[$r['MaKho']][$r['MaHang']]=(float)$r['DangChoNhan'];
?>
<?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<div class="hint"><b>UC06 – Chủ NPP:</b> Lập đơn đặt nhập chỉ tạo kế hoạch nhập hàng, trạng thái “Chờ nhận hàng”. Chức năng này <b>không tăng tồn kho</b>; tồn chỉ tăng khi Thủ kho lập Phiếu nhập kho.</div>
<form method="post" id="frmPurchase"><input type="hidden" name="uc_action" value="save_purchase"><input type="hidden" name="RequestKey" value="<?=e($requestKey)?>">
<div class="card"><div class="section-head"><div><h2>1. Thông tin đơn đặt nhập</h2><p>Nhà cung cấp, kho nhận và hình thức thanh toán dự kiến.</p></div></div>
 <div class="form-grid">
   <div class="field"><label>Số đơn nhập</label><input class="code-input" name="SoDonNhap" value="<?=e($_POST['SoDonNhap']??$preview)?>" readonly></div>
   <div class="field"><label>Ngày lập</label><input value="<?=e(date('Y-m-d'))?>" readonly></div>
   <div class="field"><label>Nhà cung cấp *</label><select name="MaNCC" required><?php foreach($ncc as $x):?><option value="<?=e($x['MaNCC'])?>" <?=($_POST['MaNCC']??'')===$x['MaNCC']?'selected':''?>><?=e($x['MaNCC'].' - '.$x['TenNCC'])?></option><?php endforeach;?></select></div>
   <div class="field"><label>Kho dự kiến nhận *</label><select name="MaKhoDuKien" id="dnKho" required><?php foreach($warehouses as $x):?><option value="<?=e($x['MaKho'])?>" <?=($_POST['MaKhoDuKien']??'')===$x['MaKho']?'selected':''?>><?=e($x['MaKho'].' - '.$x['TenKho'])?></option><?php endforeach;?></select></div>
   <div class="field"><label>Hình thức thanh toán dự kiến</label><select name="HinhThucThanhToan"><option <?=($_POST['HinhThucThanhToan']??'')==='Trả ngay'?'selected':''?>>Trả ngay</option><option <?=($_POST['HinhThucThanhToan']??'Trả nợ')==='Trả nợ'?'selected':''?>>Trả nợ</option><option <?=($_POST['HinhThucThanhToan']??'')==='Ký gửi'?'selected':''?>>Ký gửi</option></select></div>
   <div class="field"><label>Chủ NPP lập đơn</label><input value="<?=e($_SESSION['full_name'])?>" readonly></div>
 </div>
</div>
<div class="card"><div class="section-head"><div><h2>2. Chi tiết hàng cần đặt</h2><p>Gợi ý = max(Nhu cầu - Tồn khả dụng - Đang chờ nhận, 0).</p></div><button type="button" class="btn btn-primary" id="dnAdd">+ Thêm mặt hàng vào đơn</button></div>
 <div class="table-wrap"><table><thead><tr><th>STT</th><th>Hàng hóa</th><th>Nhu cầu</th><th>Tồn khả dụng</th><th>Đang chờ nhận</th><th>Gợi ý</th><th>Số lượng đặt</th><th>Giá dự kiến</th><th class="text-right">Thành tiền</th><th></th></tr></thead><tbody id="dnBody"></tbody></table></div>
 <div class="finance-box"><div class="finance-grid"><div class="field"><label>Chiết khấu dự kiến (%)</label><input id="dnDiscount" name="ChietKhauPhanTram" type="number" min="0" max="100" step="0.01" value="<?=e($_POST['ChietKhauPhanTram']??0)?>"></div><div class="field"><label>Thuế (%)</label><input id="dnTax" name="ThueSuat" type="number" min="0" max="100" step="0.01" value="<?=e($_POST['ThueSuat']??10)?>"></div><div class="info-pill">Tiền hàng: <b id="dnGoods">0 ₫</b></div><div class="info-pill">CK + Thuế: <b id="dnAdjust">0 ₫</b></div></div><div class="finance-total"><span>TỔNG GIÁ TRỊ DỰ KIẾN</span><span id="dnTotal">0 ₫</span></div></div>
 <div class="actions"><button type="button" class="btn btn-light" onclick="if(confirmAction('Hủy đơn đang nhập?')) location.reload()">Hủy</button><button class="btn btn-primary">Xác nhận lưu đơn</button></div>
</div></form>
<div class="card"><div class="section-head"><div><h2>Đơn đặt nhập gần đây</h2></div></div><div class="table-wrap"><table><thead><tr><th>Số đơn</th><th>Ngày</th><th>Nhà cung cấp</th><th>Kho nhận</th><th class="text-right">Tổng dự kiến</th><th>Trạng thái</th></tr></thead><tbody>
<?php foreach($recent as $r):?><tr><td class="text-blue"><?=e($r['SoDonNhap'])?></td><td><?=e($r['NgayLap'])?></td><td><?=e($r['TenNCC'])?></td><td><?=e($r['TenKho'])?></td><td class="text-right"><b><?=money($r['TongTien'])?></b></td><td><span class="badge <?=$r['TrangThai']==='Chờ nhận hàng'?'badge-orange':'badge-blue'?>"><?=e($r['TrangThai'])?></span></td></tr><?php endforeach;?>
</tbody></table></div></div>
<script>
const DN_PRODUCTS=<?=json_encode($products,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>,DN_STOCK=<?=json_encode($stock,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>,DN_WAIT=<?=json_encode($waiting,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const DN_POST=<?=json_encode(array_map(function($i){return ['MaHang'=>$_POST['MaHang'][$i]??'','NhuCau'=>$_POST['NhuCau'][$i]??0,'SoLuongDat'=>$_POST['SoLuongDat'][$i]??1,'GiaDuKien'=>$_POST['GiaDuKien'][$i]??0];},array_keys($_POST['MaHang']??[])),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
let dnRows=DN_POST.length?DN_POST.map(x=>({...x})):(DN_PRODUCTS.length?[{MaHang:DN_PRODUCTS[0].MaHang,NhuCau:100,SoLuongDat:1,GiaDuKien:DN_PRODUCTS[0].GiaNhapMacDinh}]:[]);
function dnMetrics(r){const k=document.getElementById('dnKho')?.value,ton=Number(DN_STOCK[k]?.[r.MaHang]||0),wait=Number(DN_WAIT[k]?.[r.MaHang]||0),suggest=Math.max(Number(r.NhuCau||0)-ton-wait,0);return {ton,wait,suggest}}
function renderDn(){const body=document.getElementById('dnBody');body.innerHTML='';dnRows.forEach((r,i)=>{const m=dnMetrics(r),opts=DN_PRODUCTS.map(p=>`<option value="${escapeHtml(p.MaHang)}" ${p.MaHang===r.MaHang?'selected':''}>${escapeHtml(p.MaHang+' - '+p.TenHang)}</option>`).join(''),line=Number(r.SoLuongDat||0)*Number(r.GiaDuKien||0);body.insertAdjacentHTML('beforeend',`<tr><td class="text-center">${i+1}</td><td><select class="table-input select-wide" name="MaHang[]" onchange="dnField(${i},'MaHang',this.value,true)">${opts}</select></td><td><input class="table-input" type="number" min="0" name="NhuCau[]" value="${escapeHtml(r.NhuCau)}" oninput="dnField(${i},'NhuCau',this.value)"></td><td class="text-center"><span class="badge badge-gray">${m.ton}</span></td><td class="text-center"><span class="badge badge-orange">${m.wait}</span></td><td class="text-center"><span class="badge badge-blue">${m.suggest}</span></td><td><input class="table-input" type="number" min="0.01" step="0.01" name="SoLuongDat[]" value="${escapeHtml(r.SoLuongDat)}" oninput="dnField(${i},'SoLuongDat',this.value)"></td><td><input class="table-input" type="number" min="0.01" step="0.01" name="GiaDuKien[]" value="${escapeHtml(r.GiaDuKien)}" oninput="dnField(${i},'GiaDuKien',this.value)"></td><td class="text-right text-blue">${formatMoney(line)}</td><td><button type="button" class="btn btn-danger btn-sm" onclick="dnDelete(${i})">Xóa</button></td></tr>`)});calcDn()}
function dnField(i,f,v,productChanged=false){dnRows[i][f]=v;if(productChanged){const p=DN_PRODUCTS.find(x=>x.MaHang===v);if(p)dnRows[i].GiaDuKien=p.GiaNhapMacDinh;const m=dnMetrics(dnRows[i]);dnRows[i].SoLuongDat=m.suggest||1}renderDn()}
function dnDelete(i){dnRows.splice(i,1);renderDn()}
function calcDn(){const goods=dnRows.reduce((s,r)=>s+Number(r.SoLuongDat||0)*Number(r.GiaDuKien||0),0),ck=goods*numberValue(document.getElementById('dnDiscount'))/100,tax=(goods-ck)*numberValue(document.getElementById('dnTax'))/100;document.getElementById('dnGoods').textContent=formatMoney(goods);document.getElementById('dnAdjust').textContent='- '+formatMoney(ck)+' / + '+formatMoney(tax);document.getElementById('dnTotal').textContent=formatMoney(goods-ck+tax)}
document.getElementById('dnAdd')?.addEventListener('click',()=>{if(!DN_PRODUCTS.length)return;const p=DN_PRODUCTS[0];dnRows.push({MaHang:p.MaHang,NhuCau:100,SoLuongDat:1,GiaDuKien:p.GiaNhapMacDinh});renderDn()});document.getElementById('dnKho')?.addEventListener('change',renderDn);['dnDiscount','dnTax'].forEach(x=>document.getElementById(x)?.addEventListener('input',calcDn));renderDn();
</script>
