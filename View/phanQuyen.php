<?php
include_once __DIR__ . '/../Controller/cPhanQuyen.php';
$c=new clsCPhanQuyen();$error='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['uc_action']??'')==='update_role'){
 $res=$c->capNhatQuyen($_POST);if($res['ok']){setFlash('success',$res['message']);header('Location: index.php?page=phanquyen');exit;}$error=$res['message'];
}
$accounts=$c->layTaiKhoan();$roles=$c->layNhomQuyen();
?>
<?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<div class="hint"><b>UC 6:</b> Admin chọn tài khoản, chọn nhóm quyền mới và nhấn “Cập nhật quyền”. Hệ thống cập nhật <code>MaNhomQuyen</code> trong CSDL.</div>
<div class="card">
 <div class="section-head"><div><h2>Phân quyền người dùng</h2><p>Chỉ tài khoản Admin được truy cập chức năng này.</p></div></div>
 <form method="post" class="stack-form"><input type="hidden" name="uc_action" value="update_role">
   <div class="field"><label>Chọn tài khoản cần phân quyền</label><select name="MaTaiKhoan" id="pqAccount" required><?php foreach($accounts as $a):?><option value="<?=e($a['MaTaiKhoan'])?>"><?=e($a['TenDangNhap'].' - '.$a['HoTen'].' | Hiện tại: '.$a['TenNhomQuyen'])?></option><?php endforeach;?></select></div>
   <div class="field"><label>Thiết lập nhóm quyền mới</label><select name="MaNhomQuyen" id="pqRole" required><?php foreach($roles as $r):?><option value="<?=e($r['MaNhomQuyen'])?>"><?=e($r['TenNhomQuyen'].' ('.$r['MaQuyen'].')')?></option><?php endforeach;?></select></div>
   <div class="inline-info"><div class="info-pill">Quyền hiện tại: <b id="pqCurrent">-</b></div><div class="info-pill">Quyền sau cập nhật: <b id="pqNew">-</b></div></div>
   <div class="actions"><button class="btn btn-primary" onclick="return confirmAction('Xác nhận thay đổi nhóm quyền cho tài khoản này?')">Cập nhật quyền</button></div>
 </form>
</div>
<div class="card"><div class="section-head"><div><h2>Danh sách tài khoản</h2></div></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Tên đăng nhập</th><th>Họ tên</th><th>Mã quyền</th><th>Nhóm quyền</th><th>Trạng thái</th></tr></thead><tbody>
<?php foreach($accounts as $a):?><tr><td><?=e($a['MaTaiKhoan'])?></td><td class="text-blue"><?=e($a['TenDangNhap'])?></td><td><?=e($a['HoTen'])?></td><td><span class="badge badge-gray"><?=e($a['MaQuyen'])?></span></td><td><b><?=e($a['TenNhomQuyen'])?></b></td><td><span class="badge <?=$a['TrangThai']==='Hoạt động'?'badge-green':'badge-red'?>"><?=e($a['TrangThai'])?></span></td></tr><?php endforeach;?>
</tbody></table></div></div>
<script>
const PQ_ACCOUNTS=<?=json_encode($accounts,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>,PQ_ROLES=<?=json_encode($roles,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
function pqSync(){const a=PQ_ACCOUNTS.find(x=>String(x.MaTaiKhoan)===document.getElementById('pqAccount').value),r=PQ_ROLES.find(x=>String(x.MaNhomQuyen)===document.getElementById('pqRole').value);if(a){document.getElementById('pqCurrent').textContent=a.TenNhomQuyen;document.getElementById('pqRole').value=String(a.MaNhomQuyen)}if(r)document.getElementById('pqNew').textContent=r.TenNhomQuyen}
function pqNew(){const r=PQ_ROLES.find(x=>String(x.MaNhomQuyen)===document.getElementById('pqRole').value);document.getElementById('pqNew').textContent=r?.TenNhomQuyen||'-'}
document.getElementById('pqAccount').addEventListener('change',pqSync);document.getElementById('pqRole').addEventListener('change',pqNew);pqSync();pqNew();
</script>
