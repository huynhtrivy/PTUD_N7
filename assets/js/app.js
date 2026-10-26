function formatMoney(value) {
  const n = Number(value || 0);
  return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
}
function numberValue(el) { return Number(el?.value || 0); }
function confirmAction(message) { return window.confirm(message); }
function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
}
