const SERV = "http://localhost/PYM/Server/index.php/";//192.168.1.69
const LIMIT_LISTS = 4;
var resetFrm = function(frm){
  $('input[type="email"]', frm).val('');
  $('input[type="tel"]', frm).val('');
  $('input[type="text"]', frm).val('');
  $('input[type="password"]', frm).val('');
  $('input[type="hidden"]', frm).val('');
  $('input[type="hidden"]', frm).val('');
  let $miSelect = $('select', frm);
  $miSelect.val($miSelect.children('option:first').val()).change();
}
