<?php
session_start();

$_SESSION = array();
// 空の配列で上書きをする
if (ini_set('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name . '', time() - 42000,
  $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
setcookie('email', '', time() - 3600);
// クッキーに保存してある値を削除

header('Location: index.php');
exit();
?>